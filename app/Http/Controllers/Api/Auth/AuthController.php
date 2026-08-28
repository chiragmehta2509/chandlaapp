<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DeviceToken;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class AuthController extends Controller
{
    /**
     * Shared Firebase Auth instance (lazy-initialised once per request lifecycle).
     */
    protected ?FirebaseAuth $firebaseAuth = null;

    /**
     * Build & cache the Firebase Auth instance using the service-account credentials.
     */
    protected function getFirebaseAuth(): ?FirebaseAuth
    {
        if ($this->firebaseAuth) {
            return $this->firebaseAuth;
        }

        $credentialsPath = config('firebase.credentials_path');

        // Resolve a relative path relative to the Laravel base path
        if (!str_starts_with($credentialsPath, '/') && !str_contains($credentialsPath, ':')) {
            $credentialsPath = base_path($credentialsPath);
        }

        if (!file_exists($credentialsPath)) {
            \Illuminate\Support\Facades\Log::error("Firebase credentials not found at: {$credentialsPath}");
            return null;
        }

        try {
            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->firebaseAuth = $factory->createAuth();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Firebase Auth init failed: " . $e->getMessage());
            return null;
        }

        return $this->firebaseAuth;
    }

    /**
     * Auto-register device_token if passed during login / signup.
     */
    protected function saveDeviceTokenIfPresent(User $user, Request $request): void
    {
        $deviceToken = $request->input('device_token') ?? $request->input('token');

        if (!empty($deviceToken)) {
            try {
                $col = DeviceToken::getTokenColumn();
                DeviceToken::updateOrCreate(
                    [
                        $col => $deviceToken,
                    ],
                    [
                        'user_id'     => $user->id,
                        'platform'    => strtolower($request->input('platform', 'android')),
                        'device_name' => $request->input('device_name'),
                        'app_version' => $request->input('app_version'),
                        'is_active'   => true,
                    ]
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to auto-register device_token during auth', [
                    'user_id' => $user->id,
                    'error'   => $e->getMessage()
                ]);
            }
        }
    }

    // -------------------------------------------------------------------------
    // Firebase Google Sign-In
    // -------------------------------------------------------------------------

    /**
     * POST /api/v1/auth/google/login
     *
     * Flutter sends the Firebase ID token obtained after Google Sign-In through
     * firebase_auth. We verify it with the Firebase Admin SDK so we never blindly
     * trust client-supplied data.
     *
     * Body params:
     *   firebase_id_token  (required) – Firebase ID token from firebase_auth.currentUser.getIdToken()
     *   name               (optional) – display name from Flutter (used only on first signup)
     *   avatar             (optional) – photo URL from Flutter
     */
    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_id_token' => 'required|string',
            'name'              => 'nullable|string|max:255',
            'avatar'            => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $auth = $this->getFirebaseAuth();
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase is not configured on the server.',
            ], 503);
        }

        try {
            $verifiedToken = $auth->verifyIdToken($request->firebase_id_token);
        } catch (FailedToVerifyToken $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired Firebase token.',
                'error'   => $e->getMessage(),
            ], 401);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase token verification failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }

        $claims      = $verifiedToken->claims();
        $firebaseUid = $claims->get('sub');           // Firebase UID
        $email       = $claims->get('email');
        $name        = $request->name  ?? $claims->get('name')    ?? 'User';
        $avatar      = $request->avatar ?? $claims->get('picture') ?? null;

        // Provider must be google.com (sign_in_provider inside firebase claim)
        $firebaseClaim    = $claims->get('firebase');
        $signInProvider   = is_array($firebaseClaim) ? ($firebaseClaim['sign_in_provider'] ?? '') : '';

        if ($signInProvider !== 'google.com') {
            return response()->json([
                'success' => false,
                'message' => 'Token was not issued via Google Sign-In.',
            ], 401);
        }

        try {
            $user = User::where('provider_id', $firebaseUid)
                ->orWhere(function ($q) use ($email) {
                    if ($email) $q->where('email', $email);
                })
                ->first();

            if (!$user) {
                $user = User::create([
                    'name'              => $name,
                    'email'             => $email,
                    'auth_provider'     => 'google',
                    'provider_id'       => $firebaseUid,
                    'avatar'            => $avatar,
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]);
            } else {
                $user->update([
                    'auth_provider' => 'google',
                    'provider_id'   => $firebaseUid,
                    'avatar'        => $avatar ?? $user->avatar,
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $this->saveDeviceTokenIfPresent($user, $request);

            ActivityLog::create([
                'user_id'    => $user->id,
                'action'     => 'google_login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data'    => [
                    'user'       => $user,
                    'token'      => $token,
                    'token_type' => 'Bearer',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Firebase Apple Sign-In
    // -------------------------------------------------------------------------

    /**
     * POST /api/v1/auth/apple/login
     *
     * Flutter (sign_in_with_apple + firebase_auth) exchanges the Apple credential
     * with Firebase and gives us a Firebase ID token. We verify that token here.
     *
     * Body params:
     *   firebase_id_token  (required) – Firebase ID token from firebase_auth.currentUser.getIdToken()
     *   name               (optional) – full name (only sent by Apple on FIRST login)
     *   email              (optional) – email (only sent by Apple on FIRST login)
     */
    public function appleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_id_token' => 'required|string',
            'name'              => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $auth = $this->getFirebaseAuth();
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase is not configured on the server.',
            ], 503);
        }

        try {
            $verifiedToken = $auth->verifyIdToken($request->firebase_id_token);
        } catch (FailedToVerifyToken $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired Firebase token.',
                'error'   => $e->getMessage(),
            ], 401);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase token verification failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }

        $claims      = $verifiedToken->claims();
        $firebaseUid = $claims->get('sub');
        $email       = $request->email ?? $claims->get('email');
        $name        = $request->name  ?? $claims->get('name')    ?? 'User';

        // Provider must be apple.com
        $firebaseClaim  = $claims->get('firebase');
        $signInProvider = is_array($firebaseClaim) ? ($firebaseClaim['sign_in_provider'] ?? '') : '';

        if ($signInProvider !== 'apple.com') {
            return response()->json([
                'success' => false,
                'message' => 'Token was not issued via Apple Sign-In.',
            ], 401);
        }

        try {
            $user = User::where('provider_id', $firebaseUid)
                ->orWhere(function ($q) use ($email) {
                    if ($email) $q->where('email', $email);
                })
                ->first();

            if (!$user) {
                $createData = [
                    'name'              => $name,
                    'auth_provider'     => 'apple',
                    'provider_id'       => $firebaseUid,
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ];
                if ($email) {
                    $createData['email'] = $email;
                }
                $user = User::create($createData);
            } else {
                $updateData = [
                    'auth_provider' => 'apple',
                    'provider_id'   => $firebaseUid,
                ];
                if ($email && !$user->email) {
                    $updateData['email'] = $email;
                }
                $user->update($updateData);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $this->saveDeviceTokenIfPresent($user, $request);

            ActivityLog::create([
                'user_id'    => $user->id,
                'action'     => 'apple_login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data'    => [
                    'user'       => $user,
                    'token'      => $token,
                    'token_type' => 'Bearer',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Facebook Login (unchanged)
    // -------------------------------------------------------------------------

    public function facebookLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
            'email'        => 'required|email',
            'name'         => 'nullable|string',
            'avatar'       => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $fbResponse = file_get_contents("https://graph.facebook.com/me?access_token={$request->access_token}&fields=id,name,email,picture");
            $fbUser     = json_decode($fbResponse, true);

            if (isset($fbUser['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Facebook token',
                ], 401);
            }

            $user = User::where('email', $request->email)
                ->orWhere('provider_id', $fbUser['id'])
                ->first();

            if (!$user) {
                $user = User::create([
                    'name'              => $request->name ?? $fbUser['name'] ?? 'User',
                    'email'             => $request->email,
                    'auth_provider'     => 'facebook',
                    'provider_id'       => $fbUser['id'],
                    'avatar'            => $request->avatar ?? $fbUser['picture']['data']['url'] ?? null,
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]);
            } else {
                $user->update([
                    'auth_provider' => 'facebook',
                    'provider_id'   => $fbUser['id'],
                    'avatar'        => $request->avatar ?? $user->avatar,
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $this->saveDeviceTokenIfPresent($user, $request);

            ActivityLog::create([
                'user_id'    => $user->id,
                'action'     => 'facebook_login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data'    => [
                    'user'       => $user,
                    'token'      => $token,
                    'token_type' => 'Bearer',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Phone OTP
    // -------------------------------------------------------------------------

    public function sendOTP(Request $request)
    {
        // Normalise before validation: strip +91 / 0 prefix
        $request->merge([
            'phone' => $this->normalizeIndianMobile($request->input('phone')),
        ]);

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|size:10|regex:/^[6-9][0-9]{9}$/',
        ], [
            'phone.regex' => 'Enter a valid 10-digit Indian mobile number (starts with 6, 7, 8, or 9).',
            'phone.size'  => 'Mobile number must be exactly 10 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        cache()->put("otp_{$request->phone}", $otp, now()->addMinutes(5));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'data'    => [
                'otp'        => $otp, // Remove in production
                'expires_in' => 300,
            ],
        ]);
    }

    public function verifyOTP(Request $request)
    {
        // Normalise before validation: strip +91 / 0 prefix
        $request->merge([
            'phone' => $this->normalizeIndianMobile($request->input('phone')),
        ]);

        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|size:10|regex:/^[6-9][0-9]{9}$/',
            'otp'   => 'required|string|size:6',
            'name'  => 'nullable|string',
        ], [
            'phone.regex' => 'Enter a valid 10-digit Indian mobile number (starts with 6, 7, 8, or 9).',
            'phone.size'  => 'Mobile number must be exactly 10 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $cachedOTP = cache()->get("otp_{$request->phone}");

        if (!$cachedOTP || $cachedOTP !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP',
            ], 401);
        }

        cache()->forget("otp_{$request->phone}");

        $user = User::where('phone', $request->phone)->where('is_deleted', false)->first();

        if (!$user) {
            $user = User::create([
                'name'             => $request->name ?? 'User',
                'phone'            => $request->phone,
                'auth_provider'    => 'phone',
                'phone_verified_at'=> now(),
                'is_active'        => true,
            ]);

            try {
                $waService  = new \App\Services\WhatsAppService();
                $cleanPhone = preg_replace('/^\+?91/', '', $user->phone);
                $waService->sendTemplateMessage(
                    to: '91' . $cleanPhone,
                    templateName: 'welcome_first_login',
                    languageCode: 'en',
                    components: [
                        [
                            'type'       => 'body',
                            'parameters' => [
                                \App\Services\WhatsAppService::formatTextParameter($user->name ?? 'User'),
                            ],
                        ],
                    ]
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Welcome WhatsApp failed during OTP registration', [
                    'user_id' => $user->id,
                    'phone'   => $user->phone,
                    'error'   => $e->getMessage(),
                ]);
            }
        } else {
            $user->update(['phone_verified_at' => now()]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->saveDeviceTokenIfPresent($user, $request);

        ActivityLog::create([
            'user_id'    => $user->id,
            'action'     => 'phone_otp_login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'data'    => [
                'user'       => $user,
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Email / Password
    // -------------------------------------------------------------------------

    public function register(Request $request)
    {
        // Normalise before validation: strip +91 / 0 prefix
        $request->merge([
            'phone' => $this->normalizeIndianMobile($request->input('phone')),
        ]);

        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|unique:users,email',
            'phone'         => ['required', 'string', 'size:10', 'regex:/^[6-9][0-9]{9}$/', \Illuminate\Validation\Rule::unique('users', 'phone')->where('is_deleted', false)],
            'password'      => 'required|string|min:8|confirmed',
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ], [
            'phone.regex' => 'Enter a valid 10-digit Indian mobile number (starts with 6, 7, 8, or 9).',
            'phone.size'  => 'Mobile number must be exactly 10 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $token = (string) Str::uuid();

        $registrationData = [
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => $request->password,
            'referral_code' => $request->referral_code,
            'source'   => 'api'
        ];

        // Cache the data for 15 minutes. It will be verified by the Web controller.
        cache()->put("reg_data_{$token}", $registrationData, now()->addMinutes(15));

        $sentTo = '';
        $verificationUrl = route('client.register.verify.link', ['token' => $token]);

        try {
            \Illuminate\Support\Facades\Log::info("API Link to WhatsApp {$request->phone}: {$verificationUrl}");

            $waService  = new \App\Services\WhatsAppService();
            $cleanPhone = preg_replace('/^\+?91/', '', $request->phone);

            // Template otp_verification_link has 2 body vars: {{1}}=name, {{2}}=phone number + 1 button (token suffix)
            $waResult = $waService->sendTemplateMessage(
                to: '91' . $cleanPhone,
                templateName: 'otp_verification_link',
                languageCode: 'en_US',
                components: [
                    [
                        'type'       => 'body',
                        'parameters' => [
                            \App\Services\WhatsAppService::formatTextParameter($request->name),
                            \App\Services\WhatsAppService::formatTextParameter('+91' . $cleanPhone),
                        ],
                    ],
                    [
                        'type'       => 'button',
                        'sub_type'   => 'url',
                        'index'      => '0',
                        'parameters' => [\App\Services\WhatsAppService::formatTextParameter($token)],
                    ],
                ]
            );

            \Illuminate\Support\Facades\Log::info('API register WA result', ['result' => $waResult]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('API Link WhatsApp failed', ['error' => $e->getMessage()]);
        }
        $sentTo = 'WhatsApp number ending in ' . substr($request->phone, -4);

        return response()->json([
            'success' => true,
            'message' => "Verification link sent to {$sentTo}",
            'data'    => [
                'sent_to'    => $sentTo,
                'expires_in' => 900 // 15 mins
            ],
        ], 200);
    }

    public function login(Request $request)
    {
        // Auto-fill 'login' if mobile app sends 'email', 'phone', or 'username' instead
        if (!$request->has('login')) {
            $fallbackLogin = $request->input('email') ?? $request->input('phone') ?? $request->input('username');
            if ($fallbackLogin !== null) {
                $request->merge(['login' => $fallbackLogin]);
            }
        }

        $validator = Validator::make($request->all(), [
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'The login (or email/phone) field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $login = trim($request->input('login'));

        // Detect email vs phone and normalise accordingly
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        if ($isEmail) {
            $login = strtolower($login);
            $field = 'email';
        } else {
            $login = $this->normalizeIndianMobile($login);
            $field = 'phone';
        }

        $user = User::where($field, $login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!$user->is_active || $user->is_deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive or deleted',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->saveDeviceTokenIfPresent($user, $request);

        ActivityLog::create([
            'user_id'    => $user->id,
            'action'     => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data'    => [
                'user'       => $user,
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'logout',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function refreshToken(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data'    => $request->user()->load('settings'),
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 401);
        }

        $user->update(['password' => Hash::make($request->password)]);

        ActivityLog::create([
            'user_id'    => $user->id,
            'action'     => 'change_password',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'nullable|string|max:255',
            'phone'    => ['nullable', 'string', \Illuminate\Validation\Rule::unique('users', 'phone')->ignore($request->user()->id)->where('is_deleted', false)],
            'language' => 'nullable|string|in:en,hi,gu',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $user->update($request->only(['name', 'phone', 'language']));

        ActivityLog::create([
            'user_id'    => $user->id,
            'action'     => 'update_profile',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data'    => $user,
        ]);
    }

    public function forgotPassword(Request $request)
    {
        // Support 'email' or 'phone' fields if 'login' key is omitted by mobile app
        if (!$request->has('login')) {
            $fallbackLogin = $request->input('email') ?? $request->input('phone') ?? $request->input('username');
            if ($fallbackLogin !== null) {
                $request->merge(['login' => $fallbackLogin]);
            }
        }

        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $input   = trim($request->input('login'));
        $isEmail = filter_var($input, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            // ── Email path ──
            $input = strtolower($input);
            $user  = User::where('email', $input)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found with that email address.',
                ], 404);
            }

            // Use Laravel password broker to send the standard reset email
            \Illuminate\Support\Facades\Password::sendResetLink(['email' => $input]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email.',
            ]);
        }

        // ── Phone path ──
        $phone = $this->normalizeIndianMobile($input);
        $user  = User::where('phone', $phone)->where('is_deleted', false)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with that mobile number.',
            ], 404);
        }

        // Cell number entered → generate custom token & send WhatsApp message directly
        $token    = Str::random(64);
        $resetUrl = route('password.reset', ['token' => $token]) . '?phone=' . urlencode($phone);

        cache()->put("pwd_reset_phone_{$token}", $phone, now()->addHour());

        try {
            $waService  = new \App\Services\WhatsAppService();
            $cleanPhone = preg_replace('/^\+?91/', '', $phone);
            $waService->sendTemplateMessage(
                to: '91' . $cleanPhone,
                templateName: 'reset_password',
                languageCode: 'en_US',
                components: [
                    [
                        'type'       => 'body',
                        'parameters' => [
                            \App\Services\WhatsAppService::formatTextParameter($resetUrl),
                        ],
                    ],
                    [
                        'type'       => 'button',
                        'sub_type'   => 'url',
                        'index'      => '0',
                        'parameters' => [\App\Services\WhatsAppService::formatTextParameter($token . '?phone=' . urlencode($phone))],
                    ],
                ]
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('API password reset WhatsApp failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent to your WhatsApp number.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        // ── Phone-based reset (custom cache token) ──
        if ($request->filled('phone') && !$request->filled('email')) {
            $validator = Validator::make($request->all(), [
                'token'    => 'required|string',
                'phone'    => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $phone       = $this->normalizeIndianMobile($request->phone);
            $cachedPhone = cache()->get("pwd_reset_phone_{$request->token}");

            if (!$cachedPhone || $cachedPhone !== $phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token.',
                ], 401);
            }

            $user = User::where('phone', $phone)->where('is_deleted', false)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            $user->update(['password' => Hash::make($request->password)]);
            cache()->forget("pwd_reset_phone_{$request->token}");

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully.',
            ]);
        }

        // ── Email-based reset (Laravel broker) ──
        $validator = Validator::make($request->all(), [
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __($status),
        ], 401);
    }

    // -------------------------------------------------------------------------
    // WhatsApp Deep-Link Account Verification
    // -------------------------------------------------------------------------

    /**
     * POST /api/v1/auth/verify-account
     *
     * Called by the Flutter app when it intercepts the WhatsApp verification
     * deep-link (chandlabook://verify?token=...) instead of letting the browser
     * open the web route /client/account_verification/{token}.
     *
     * Body params:
     *   token  (required) – the UUID token that was embedded in the WhatsApp link
     *
     * On success the user account is created (if not already) and a Sanctum token
     * is returned so the user is immediately logged in inside the app.
     */
    public function verifyAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $token = trim($request->input('token'));
        $cacheKey = "reg_data_{$token}";

        \Illuminate\Support\Facades\Log::info('API verifyAccount called', [
            'token'      => $token,
            'cache_key'  => $cacheKey,
            'cache_has'  => cache()->has($cacheKey),
        ]);

        $data = cache()->get($cacheKey);

        if (!$data) {
            return response()->json([
                'success'        => false,
                'message'        => 'This verification link has expired or is invalid. Please register again.',
                'phone_verified' => false,
            ], 410); // 410 Gone
        }

        // Check if user was already created (e.g., web-link was clicked first)
        $existingUser = User::where('phone', $data['phone'] ?? null)->first();
        if ($existingUser) {
            // Already verified — just return a token
            cache()->forget($cacheKey);

            $authToken = $existingUser->createToken('auth_token')->plainTextToken;
            $this->saveDeviceTokenIfPresent($existingUser, $request);

            return response()->json([
                'success'        => true,
                'message'        => 'Account already verified. Logged in successfully.',
                'phone_verified' => !is_null($existingUser->phone_verified_at),
                'data'           => [
                    'user'       => $existingUser,
                    'token'      => $authToken,
                    'token_type' => 'Bearer',
                ],
            ]);
        }

        // Clear cache before creating user to prevent duplicate calls
        cache()->forget($cacheKey);

        // Resolve referrer
        $referrerId = null;
        if (!empty($data['referral_code'])) {
            $referrerId = User::where('referral_code', $data['referral_code'])->value('id');
        }

        $authProvider = !empty($data['email']) ? 'email' : 'phone';
        $freeCredits  = $referrerId ? 1 : 0;

        try {
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'] ?? null,
                'phone'             => $data['phone'] ?? null,
                'password'          => \Illuminate\Support\Facades\Hash::make($data['password']),
                'auth_provider'     => $authProvider,
                'is_active'         => true,
                'phone_verified_at' => now(),
                'email_verified_at' => !empty($data['email']) ? now() : null,
                'referral_code'     => $this->generateReferralCode(),
                'referred_by'       => $referrerId,
                'free_event_credits'=> $freeCredits,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('API verifyAccount user creation failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success'        => false,
                'message'        => 'Account creation failed. Please try again.',
                'phone_verified' => false,
            ], 500);
        }

        if ($referrerId) {
            User::where('id', $referrerId)->increment('free_event_credits');
        }

        // Send Welcome WhatsApp
        if (!empty($user->phone)) {
            try {
                $waService  = new \App\Services\WhatsAppService();
                $cleanPhone = preg_replace('/^\+?91/', '', $user->phone);
                $waService->sendTemplateMessage(
                    to: '91' . $cleanPhone,
                    templateName: 'welcome_first_login',
                    languageCode: 'en',
                    components: [
                        [
                            'type'       => 'body',
                            'parameters' => [
                                \App\Services\WhatsAppService::formatTextParameter($user->name ?? 'User'),
                            ],
                        ],
                    ]
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('API verifyAccount welcome WhatsApp failed', [
                    'user_id' => $user->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        // Send Welcome Email
        if (!empty($user->email)) {
            try {
                \Illuminate\Support\Facades\Mail::send(
                    'emails.welcome',
                    ['user' => $user],
                    function ($message) use ($user) {
                        $message->to($user->email, $user->name)
                            ->subject('Welcome to Chandla Book — your account is ready');
                    }
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('API verifyAccount welcome email failed', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $authToken = $user->createToken('auth_token')->plainTextToken;

        $this->saveDeviceTokenIfPresent($user, $request);

        ActivityLog::create([
            'user_id'    => $user->id,
            'action'     => 'account_verified',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Account verified and created successfully.',
            'phone_verified' => !is_null($user->phone_verified_at),
            'data'           => [
                'user'       => $user,
                'token'      => $authToken,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    /**
     * Normalize to 10-digit Indian mobile: strips spaces, optional +91 / 0 prefix.
     */
    private function normalizeIndianMobile(mixed $raw): string
    {
        $digits = preg_replace('/\D/', '', (string) $raw);
        if (str_starts_with($digits, '91') && strlen($digits) === 12) {
            $digits = substr($digits, 2);
        }
        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Generate a unique 8-character referral code.
     */
    private function generateReferralCode(): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }
}


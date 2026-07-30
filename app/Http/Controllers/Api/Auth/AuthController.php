<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Google_Client;

class AuthController extends Controller
{
    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
            'email' => 'required|email',
            'name' => 'nullable|string',
            'avatar' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client = new Google_Client(['client_id' => config('services.google.client_id')]);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Google token'
                ], 401);
            }

            $user = User::where('email', $request->email)
                ->orWhere('provider_id', $payload['sub'])
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $request->name ?? $payload['name'] ?? 'User',
                    'email' => $request->email,
                    'auth_provider' => 'google',
                    'provider_id' => $payload['sub'],
                    'avatar' => $request->avatar ?? $payload['picture'] ?? null,
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]);
            } else {
                $user->update([
                    'auth_provider' => 'google',
                    'provider_id' => $payload['sub'],
                    'avatar' => $request->avatar ?? $user->avatar,
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'google_login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function facebookLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
            'email' => 'required|email',
            'name' => 'nullable|string',
            'avatar' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fbResponse = file_get_contents("https://graph.facebook.com/me?access_token={$request->access_token}&fields=id,name,email,picture");
            $fbUser = json_decode($fbResponse, true);

            if (isset($fbUser['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Facebook token'
                ], 401);
            }

            $user = User::where('email', $request->email)
                ->orWhere('provider_id', $fbUser['id'])
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $request->name ?? $fbUser['name'] ?? 'User',
                    'email' => $request->email,
                    'auth_provider' => 'facebook',
                    'provider_id' => $fbUser['id'],
                    'avatar' => $request->avatar ?? $fbUser['picture']['data']['url'] ?? null,
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]);
            } else {
                $user->update([
                    'auth_provider' => 'facebook',
                    'provider_id' => $fbUser['id'],
                    'avatar' => $request->avatar ?? $user->avatar,
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'facebook_login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function appleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity_token' => 'required|string',
            'email' => 'nullable|email',
            'name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Apple Sign In verification would go here
        // For now, simplified version
        $user = User::where('email', $request->email)->first();

        if (!$user && $request->email) {
            $user = User::create([
                'name' => $request->name ?? 'User',
                'email' => $request->email,
                'auth_provider' => 'apple',
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        if ($user) {
            $token = $user->createToken('auth_token')->plainTextToken;

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'apple_login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Authentication failed'
        ], 401);
    }

    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate OTP (6 digits)
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache (expires in 5 minutes)
        cache()->put("otp_{$request->phone}", $otp, now()->addMinutes(5));

        // Send OTP via SMS (integrate with MSG91 or similar)
        // SMSService::send($request->phone, "Your OTP is: {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'otp' => $otp, // Remove in production
                'expires_in' => 300
            ]
        ]);
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string|size:6',
            'name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $cachedOTP = cache()->get("otp_{$request->phone}");

        if (!$cachedOTP || $cachedOTP !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 401);
        }

        // Clear OTP
        cache()->forget("otp_{$request->phone}");

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            $user = User::create([
                'name' => $request->name ?? 'User',
                'phone' => $request->phone,
                'auth_provider' => 'phone',
                'phone_verified_at' => now(),
                'is_active' => true,
            ]);
        } else {
            $user->update([
                'phone_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'phone_otp_login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'auth_provider' => 'email',
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'register',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if (!empty($user->email)) {
            try {
                Mail::send('emails.welcome', ['user' => $user], function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                        ->subject('Welcome to Chandla Book — your account is ready');
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Welcome email failed during API registration', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->is_active || $user->is_deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive or deleted'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'logout',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function refreshToken(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('settings')
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'change_password',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|unique:users,phone,' . $request->user()->id,
            'language' => 'nullable|string|in:en,hi,gu',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $user->update($request->only(['name', 'phone', 'language']));

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'update_profile',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate reset token
        $token = Str::random(64);
        cache()->put("password_reset_{$token}", $request->email, now()->addHours(1));

        // Send reset email
        // Mail::to($request->email)->send(new ResetPasswordMail($token));

        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent to your email'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = cache()->get("password_reset_{$request->token}");

        if (!$email || $email !== $request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token'
            ], 401);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        cache()->forget("password_reset_{$request->token}");

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    }
}


<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Rules\NotDisposableEmail;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('web')->check()) {
            return view('client.auth.already_logged_in');
        }
        return view('client.auth.login');
    }

    public function showProfile()
    {
        $user = Auth::guard('web')->user();
        return view('client.auth.profile', compact('user'));
    }

    public function sendProfileVerification(Request $request)
    {
        $request->validate([
            'type' => 'required|in:phone'
        ]);

        $user = Auth::guard('web')->user();
        $token = (string) \Illuminate\Support\Str::uuid();
        
        cache()->put("profile_verify_{$token}", [
            'user_id' => $user->id,
            'type' => 'phone'
        ], now()->addMinutes(15));

        $verificationUrl = route('client.profile.verify.link', ['token' => $token]);

        if (!$user->phone) return back()->withErrors(['phone' => 'No phone number found.']);
        try {
            \Illuminate\Support\Facades\Log::info("Profile Link to WhatsApp {$user->phone}: {$verificationUrl}");
            $waService = new \App\Services\WhatsAppService();
            $cleanPhone = preg_replace('/^\+?91/', '', $user->phone);
            
            $waService->sendTemplateMessage(
                to: '91' . $cleanPhone,
                templateName: 'otp_verification_link',
                languageCode: 'en_US',
                components: [
                    [
                        'type' => 'body',
                        'parameters' => [
                            \App\Services\WhatsAppService::formatTextParameter($user->name),
                            \App\Services\WhatsAppService::formatTextParameter('+91' . $cleanPhone),
                        ]
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => '0',
                        'parameters' => [\App\Services\WhatsAppService::formatTextParameter($token)]
                    ]
                ]
            );
            
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Profile Link WhatsApp failed', ['error' => $e->getMessage()]);
        }

        return back()->with('status', 'WhatsApp verification link sent! Please check your WhatsApp.');
    }

    public function verifyProfileLink(Request $request, $token)
    {
        $data = cache()->get("profile_verify_{$token}");

        if (!$data) {
            return redirect()->route('client.profile')->withErrors(['session' => 'This verification link has expired or is invalid.']);
        }

        $user = \App\Models\User::find($data['user_id']);
        if ($user) {
            if ($data['type'] === 'email') {
                $user->email_verified_at = now();
            } elseif ($data['type'] === 'phone') {
                $user->phone_verified_at = now();
            }
            $user->save();
        }

        cache()->forget("profile_verify_{$token}");

        return redirect()->route('client.profile')->with('status', ucfirst($data['type']) . ' verified successfully!');
    }

    public function showRegisterForm()
    {
        if (Auth::guard('web')->check()) {
            return view('client.auth.already_logged_in');
        }
        return view('client.auth.register');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user = User::where($field, $login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Invalid credentials'])->withInput();
        }

        if (!$user->is_active || $user->is_deleted) {
            return back()->withErrors(['login' => 'Account is inactive or deleted'])->withInput();
        }

        if (Auth::guard('web')->attempt([$field => $login, 'password' => $request->password], $request->filled('remember'))) {
            $request->session()->regenerate();
            $request->session()->put('login_method', $field);

            if ($request->filled('remember')) {
                \Illuminate\Support\Facades\Cookie::queue('remembered_login', $login, 60 * 24 * 30); // 30 days
                \Illuminate\Support\Facades\Cookie::queue('remembered_password', $request->password, 60 * 24 * 30);
            } else {
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('remembered_login'));
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('remembered_password'));
            }

            return redirect()->intended(route('client.dashboard'));
        }

        return back()->withErrors(['login' => 'Invalid credentials'])->withInput();
    }

    public function register(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
            'phone' => $this->normalizeIndianMobile($request->input('phone')),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'string', 'max:255', 'email:rfc,dns', 'unique:users,email', new NotDisposableEmail],
            'phone' => ['required', 'string', 'size:10', 'regex:/^[6-9][0-9]{9}$/', \Illuminate\Validation\Rule::unique('users', 'phone')->where('is_deleted', false)],
            'password' => 'required|string|min:8|confirmed',
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ], [
            'phone.required' => 'Please provide a mobile number.',
            'phone.regex' => 'Enter a valid 10-digit Indian mobile number (starts with 6, 7, 8, or 9).',
            'phone.size' => 'Mobile number must be exactly 10 digits.',
            'email.email' => 'Enter a valid email address.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Resend logic check
        if ($request->has('resend_otp')) {
            $registrationData = $request->session()->get('registration_data');
            if (!$registrationData) {
                return redirect()->route('client.register')->withErrors(['session' => 'Registration session expired. Please start again.']);
            }
            $request->merge($registrationData);
        }

        $token = (string) \Illuminate\Support\Str::uuid();
        
        $registrationData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'referral_code' => $request->referral_code,
            'source' => 'web'
        ];
        
        // Cache the data for 15 minutes (cross-device accessible)
        cache()->put("reg_data_{$token}", $registrationData, now()->addMinutes(15));
        
        // Also keep in session just for the "Resend" button on the waiting page
        $request->session()->put('registration_data', $registrationData);
        $request->session()->put('registration_token', $token);
        
        $sentTo = '';
        $verificationUrl = route('client.register.verify.link', ['token' => $token]);

        try {
            \Illuminate\Support\Facades\Log::info("Link to WhatsApp {$request->phone}: {$verificationUrl}");
            
            $waService = new \App\Services\WhatsAppService();
            $cleanPhone = preg_replace('/^\+?91/', '', $request->phone);
            
            $waService->sendTemplateMessage(
                to: '91' . $cleanPhone,
                templateName: 'otp_verification_link',
                languageCode: 'en_US',
                components: [
                    [
                        'type' => 'body',
                        'parameters' => [
                            \App\Services\WhatsAppService::formatTextParameter($request->name),
                            \App\Services\WhatsAppService::formatTextParameter('+91' . $cleanPhone),
                        ]
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => '0',
                        'parameters' => [\App\Services\WhatsAppService::formatTextParameter($token)]
                    ]
                ]
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Link WhatsApp failed', ['error' => $e->getMessage()]);
        }
        $sentTo = 'WhatsApp number ending in ' . substr($request->phone, -4);

        $request->session()->put('registration_sent_to', $sentTo);

        if ($request->has('resend_otp')) {
            return back()->with('status', 'A new verification link has been sent to WhatsApp.');
        }

        return redirect()->route('client.register.verify');
    }

    public function showOtpVerificationForm(Request $request)
    {
        if (!$request->session()->has('registration_data')) {
            return redirect()->route('client.register')->withErrors(['session' => 'Registration session expired or invalid.']);
        }
        return view('client.auth.verify-otp');
    }

    public function verifyAccountLink(Request $request, $token)
    {
        \Illuminate\Support\Facades\Log::info("verifyAccountLink called", [
            'token' => $token,
            'cache_key' => "reg_data_{$token}",
            'cache_driver' => config('cache.default'),
            'cache_has' => cache()->has("reg_data_{$token}"),
        ]);

        $data = cache()->get("reg_data_{$token}");

        \Illuminate\Support\Facades\Log::info("Cache lookup result", [
            'found' => !is_null($data),
            'data' => $data,
        ]);

        if (!$data) {
            return redirect()->route('client.login')->withErrors(['session' => 'This verification link has expired or is invalid. Please register again.']);
        }

        // Clear cache
        cache()->forget("reg_data_{$token}");

        // Create the user
        $referrerId = null;
        if (!empty($data['referral_code'])) {
            $referrerId = User::where('referral_code', $data['referral_code'])->value('id');
        }

        $authProvider = !empty($data['email']) ? 'email' : 'phone';
        $freeCredits = $referrerId ? 1 : 0;
        
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'auth_provider' => $authProvider,
            'is_active' => true,
            'phone_verified_at' => now(),
            'email_verified_at' => !empty($data['email']) ? now() : null,
            'referral_code' => $this->generateReferralCode(),
            'referred_by' => $referrerId,
            'free_event_credits' => $freeCredits,
        ]);

        if ($referrerId) {
            User::where('id', $referrerId)->increment('free_event_credits');
        }

        // Send Welcome messages
        if (!empty($user->email)) {
            try {
                Mail::send('emails.welcome', ['user' => $user], function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                        ->subject('Welcome to Chandla Book — your account is ready');
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Welcome Email failed', ['error' => $e->getMessage()]);
            }
        }

        if (!empty($user->phone)) {
            try {
                $waService = new \App\Services\WhatsAppService();
                $cleanPhone = preg_replace('/^\+?91/', '', $user->phone);
                $waService->sendTemplateMessage(
                    to: '91' . $cleanPhone,
                    templateName: 'welcome_first_login',
                    languageCode: 'en',
                    components: [
                        [
                            'type' => 'body',
                            'parameters' => [
                                \App\Services\WhatsAppService::formatTextParameter($user->name ?? 'User')
                            ]
                        ]
                    ]
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Welcome WhatsApp failed', ['error' => $e->getMessage()]);
            }
        }

        // If it was initiated via API, redirect to a special success page or back to app
        if (isset($data['source']) && $data['source'] === 'api') {
            return redirect()->route('client.login')->with(
                'status',
                'Account verified successfully! You can now log in from the mobile app.'
            );
        }

        // If initiated via Web, redirect to login
        return redirect()->route('client.login')->with(
            'status',
            'Account verified successfully! Please sign in.'
        );
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('client.login');
    }

    public function showChangePasswordForm()
    {
        return view('client.auth.change-password');
    }

    public function showForgotPasswordForm()
    {
        return view('client.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('client.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('client.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]])->withInput();
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed|different:current_password',
        ]);

        /** @var \App\Models\User|null $user */
        $user = Auth::guard('web')->user();

        if (!$user || !Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $wasForced = (bool) $user->must_change_password;

        $user->password = Hash::make($validated['password']);
        $user->must_change_password = false;
        $user->save();

        if (!empty($user->email)) {
            \Illuminate\Support\Facades\Mail::send('emails.password-changed', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Security Alert: Your password was changed');
            });
        }

        if ($wasForced) {
            return redirect()->route('client.dashboard')
                ->with('success', 'Password updated. Welcome!');
        }

        return back()->with('success', 'Password changed successfully.');
    }

    private function generateReferralCode(): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
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
}

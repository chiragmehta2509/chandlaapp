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
            'email' => ['nullable', 'required_without:phone', 'string', 'max:255', 'email:rfc,dns', 'unique:users,email', new NotDisposableEmail],
            'phone' => ['nullable', 'required_without:email', 'string', 'size:10', 'regex:/^[6-9][0-9]{9}$/', 'unique:users,phone'],
            'password' => 'required|string|min:8|confirmed',
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ], [
            'email.required_without' => 'Please provide at least an email address or mobile number.',
            'phone.required_without' => 'Please provide at least a mobile number or email address.',
            'phone.regex' => 'Enter a valid 10-digit Indian mobile number (starts with 6, 7, 8, or 9).',
            'phone.size' => 'Mobile number must be exactly 10 digits.',
            'email.email' => 'Enter a valid email address.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Resend OTP logic check (if requested from verify page)
        if ($request->has('resend_otp')) {
            $registrationData = $request->session()->get('registration_data');
            if (!$registrationData) {
                return redirect()->route('client.register')->withErrors(['session' => 'Registration session expired. Please start again.']);
            }
            $request->merge($registrationData); // Pretend we submitted the form again
        }

        $otp = (string) random_int(100000, 999999);
        
        $registrationData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password, // Stored raw in session, hashed on create
            'referral_code' => $request->referral_code,
        ];
        
        $request->session()->put('registration_data', $registrationData);
        $request->session()->put('registration_otp', $otp);
        $request->session()->put('registration_otp_expires_at', now()->addMinutes(15));
        
        $sentTo = '';

        if (!empty($request->phone)) { // Both or just Phone -> WhatsApp
            try {
                // Log for local debugging
                \Illuminate\Support\Facades\Log::info("Mock OTP to WhatsApp {$request->phone}: {$otp}");
                
                $waService = new \App\Services\WhatsAppService();
                $cleanPhone = preg_replace('/^\+?91/', '', $request->phone);
                // Placeholder: Update with actual template when provided by user
                /* 
                $waService->sendTemplateMessage(
                    to: '91' . $cleanPhone,
                    templateName: 'otp_verification',
                    languageCode: 'en',
                    components: [
                        ['type' => 'body', 'parameters' => [\App\Services\WhatsAppService::formatTextParameter($otp)]]
                    ]
                );
                */
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('OTP WhatsApp failed', ['error' => $e->getMessage()]);
            }
            $sentTo = 'WhatsApp number ending in ' . substr($request->phone, -4);
        } else { // Email only
            try {
                \Illuminate\Support\Facades\Log::info("Sending OTP via Email to {$request->email}: {$otp}");
                Mail::send('emails.otp', ['name' => $request->name, 'otp' => $otp], function ($message) use ($request) {
                    $message->to($request->email, $request->name)
                        ->subject('Your Verification Code');
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('OTP Email failed', ['error' => $e->getMessage()]);
            }
            $parts = explode('@', $request->email);
            $sentTo = 'email address ' . substr($parts[0], 0, 3) . '***@' . $parts[1];
        }

        $request->session()->put('registration_sent_to', $sentTo);

        if ($request->has('resend_otp')) {
            return back()->with('status', 'A new verification code has been sent.');
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

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $sessionOtp = $request->session()->get('registration_otp');
        $expiresAt = $request->session()->get('registration_otp_expires_at');
        $data = $request->session()->get('registration_data');

        if (!$sessionOtp || !$data) {
            return redirect()->route('client.register')->withErrors(['session' => 'Registration session expired. Please register again.']);
        }

        if (now()->greaterThan($expiresAt)) {
            return back()->withErrors(['otp' => 'This verification code has expired. Please click Resend Code.']);
        }

        if ($request->otp !== $sessionOtp) {
            return back()->withErrors(['otp' => 'Invalid verification code. Please try again.']);
        }

        // OTP is valid, create the user
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

        // Clear session data
        $request->session()->forget(['registration_data', 'registration_otp', 'registration_otp_expires_at', 'registration_sent_to']);

        return redirect()->route('client.login')->with(
            'status',
            'Account created and verified. Please sign in.'
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

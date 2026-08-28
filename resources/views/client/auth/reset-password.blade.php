<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        (function() {
            const t = localStorage.getItem('theme');
            if (t === 'dark' || !t) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset password - Chandla Book</title>
    <link rel="stylesheet" href="{{ asset('css/chandla-client.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="cb-app min-h-screen flex items-center justify-center px-4 py-10">
    <div class="cb-card w-full max-w-md p-6 sm:p-8">
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.jpeg') }}" alt="" class="h-16 w-auto mx-auto mb-3">
            <h1 class="text-2xl font-bold text-cb-navy">Reset password</h1>
            <p class="text-slate-600 text-sm mt-1">Choose a new password for your account.</p>
        </div>

        @if($errors->any())
            <div class="cb-alert cb-alert--error mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            @if(!empty($phone))
            {{-- Phone-only reset flow --}}
            <input type="hidden" name="phone" value="{{ old('phone', $phone) }}">
            <div class="mb-4">
                <label class="cb-label !normal-case !tracking-normal !text-slate-600 !text-sm">Mobile Number</label>
                <div class="cb-field flex items-center gap-2 cursor-default select-all bg-slate-50 border-slate-200">
                    <i class="fas fa-mobile-alt text-slate-400 text-sm shrink-0"></i>
                    <span class="text-slate-700 text-sm truncate">+91 {{ old('phone', $phone) }}</span>
                    <span class="ml-auto text-xs text-slate-400 shrink-0">Verified</span>
                </div>
            </div>
            @else
            {{-- Email reset flow --}}
            <div class="mb-4">
                <label class="cb-label !normal-case !tracking-normal !text-slate-600 !text-sm">Email</label>
                {{-- Keep the value in a hidden field so it's still submitted --}}
                <input type="hidden" name="email" value="{{ old('email', $email) }}">
                <div class="cb-field flex items-center gap-2 cursor-default select-all bg-slate-50 border-slate-200">
                    <i class="fas fa-envelope text-slate-400 text-sm shrink-0"></i>
                    <span class="text-slate-700 text-sm truncate">{{ old('email', $email) }}</span>
                    <span class="ml-auto text-xs text-slate-400 shrink-0">Verified</span>
                </div>
            </div>
            @endif

            <div class="mb-4">
                <label class="cb-label !normal-case !tracking-normal !text-slate-600 !text-sm" for="password">New password</label>
                <div class="relative">
                    <input class="cb-field pr-10" id="password" type="password" name="password" required autocomplete="new-password">
                    <button type="button"
                        onmousedown="document.getElementById('password').type='text'; this.querySelector('i').classList.replace('fa-eye','fa-eye-slash');"
                        onmouseup="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash','fa-eye');"
                        onmouseleave="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash','fa-eye');"
                        ontouchstart="document.getElementById('password').type='text'; this.querySelector('i').classList.replace('fa-eye','fa-eye-slash');"
                        ontouchend="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash','fa-eye');"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="mb-6">
                <label class="cb-label !normal-case !tracking-normal !text-slate-600 !text-sm" for="password_confirmation">Confirm password</label>
                <div class="relative">
                    <input class="cb-field pr-10" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                    <button type="button"
                        onmousedown="document.getElementById('password_confirmation').type='text'; this.querySelector('i').classList.replace('fa-eye','fa-eye-slash');"
                        onmouseup="document.getElementById('password_confirmation').type='password'; this.querySelector('i').classList.replace('fa-eye-slash','fa-eye');"
                        onmouseleave="document.getElementById('password_confirmation').type='password'; this.querySelector('i').classList.replace('fa-eye-slash','fa-eye');"
                        ontouchstart="document.getElementById('password_confirmation').type='text'; this.querySelector('i').classList.replace('fa-eye','fa-eye-slash');"
                        ontouchend="document.getElementById('password_confirmation').type='password'; this.querySelector('i').classList.replace('fa-eye-slash','fa-eye');"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="cb-btn cb-btn-gold w-full py-3 rounded-2xl text-base">
                <i class="fas fa-key"></i>Reset password
            </button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('cb-dark-mode-toggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                });
            }
        });
    </script>
</body>
</html>

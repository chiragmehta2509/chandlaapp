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
    <title>Register - Chandla Book</title>
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white transition-colors duration-200">
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-white dark:from-indigo-950 dark:via-purple-900 dark:to-slate-950 transition-colors duration-200 flex flex-col">
        <header class="max-w-6xl w-full mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ route('public.home') }}" class="flex items-center gap-2 sm:gap-3 group">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Chandla Book" class="h-10 sm:h-12 w-auto" decoding="async">
                <span class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:hover:text-white/90">Chandla Book</span>
            </a>
            <div class="flex items-center gap-4">
                <button id="cb-dark-mode-toggle" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 dark:border-slate-200/20 hover:border-slate-400 dark:hover:border-slate-300/30 hover:bg-slate-200/50 dark:hover:bg-white/10 text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white transition" title="Toggle theme">
                    <i class="fa-solid fa-moon text-sm block dark:hidden"></i>
                    <i class="fa-solid fa-sun text-sm hidden dark:block"></i>
                </button>
                <a href="{{ route('client.login') }}" class="text-sm text-slate-700 dark:text-white/80 hover:text-slate-900 dark:text-white font-medium">
                    &larr; Login
                </a>
            </div>
        </header>

        <div class="flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-white/10 backdrop-blur-md rounded-2xl border border-slate-200 dark:border-white/20 p-6 sm:p-8 shadow-xl">
                    <div class="text-center mb-8">
                        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">Create your account</h1>
                        <p class="text-slate-600 dark:text-white/70 mt-2 text-sm sm:text-base">Join Chandla Book</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-4 rounded-xl border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/20 px-4 py-3 text-sm text-red-700 dark:text-red-200">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(session('status'))
                        <div class="mb-4 rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/20 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-200">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('client.register') }}">
                        @csrf
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 dark:text-white/80 mb-2" for="name">Full name *</label>
                            <input
                                class="w-full rounded-xl border border-slate-300 dark:border-white/20 bg-white dark:bg-white/10 px-4 py-3 text-slate-900 dark:text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors"
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                            >
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 dark:text-white/80 mb-2" for="email">Email <span class="text-xs text-slate-500 font-normal">(Provide at least Email or Mobile)</span></label>
                            <input
                                class="w-full rounded-xl border border-slate-300 dark:border-white/20 bg-white dark:bg-white/10 px-4 py-3 text-slate-900 dark:text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors"
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                maxlength="255"
                            >
                            <p class="mt-1.5 text-xs text-slate-500 dark:text-white/60">Real inbox required — we check the domain and block disposable / temp mail.</p>
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 dark:text-white/80 mb-2" for="phone">Mobile number <span class="text-xs text-slate-500 font-normal">(Provide at least Email or Mobile)</span></label>
                            <input
                                class="w-full rounded-xl border border-slate-300 dark:border-white/20 bg-white dark:bg-white/10 px-4 py-3 text-slate-900 dark:text-white focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors"
                                id="phone"
                                type="tel"
                                name="phone"
                                value="{{ old('phone') }}"
                                autocomplete="tel"
                                inputmode="tel"
                                maxlength="14"
                            >
                            <p class="mt-1.5 text-xs text-slate-500 dark:text-white/60">10-digit Indian mobile (first digit 6–9). You may enter <span class="font-medium text-slate-700 dark:text-white/80">+91</span> or spaces — we normalize it.</p>
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 dark:text-white/80 mb-2" for="password">Password *</label>
                            <div class="relative">
                                <input
                                    class="w-full rounded-xl border border-slate-300 dark:border-white/20 bg-white dark:bg-white/10 px-4 py-3 pr-10 text-slate-900 dark:text-white focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors"
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                >
                                <button type="button" 
                                    onmousedown="document.getElementById('password').type='text'; this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');" 
                                    onmouseup="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                                    onmouseleave="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                                    ontouchstart="document.getElementById('password').type='text'; this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');" 
                                    ontouchend="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 dark:text-white/50 hover:text-slate-700 dark:hover:text-white focus:outline-none">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 dark:text-white/80 mb-2" for="password_confirmation">Confirm password *</label>
                            <div class="relative">
                                <input
                                    class="w-full rounded-xl border border-slate-300 dark:border-white/20 bg-white dark:bg-white/10 px-4 py-3 pr-10 text-slate-900 dark:text-white focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors"
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                >
                                <button type="button" 
                                    onmousedown="document.getElementById('password_confirmation').type='text'; this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');" 
                                    onmouseup="document.getElementById('password_confirmation').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                                    onmouseleave="document.getElementById('password_confirmation').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                                    ontouchstart="document.getElementById('password_confirmation').type='text'; this.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');" 
                                    ontouchend="document.getElementById('password_confirmation').type='password'; this.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');" 
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 dark:text-white/50 hover:text-slate-700 dark:hover:text-white focus:outline-none">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-white/80 mb-2" for="referral_code">Referral code (optional)</label>
                            <input
                                class="w-full rounded-xl border border-slate-300 dark:border-white/20 bg-white dark:bg-white/10 px-4 py-3 text-slate-900 dark:text-white focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-colors"
                                id="referral_code"
                                type="text"
                                name="referral_code"
                                value="{{ old('referral_code', request('ref')) }}"
                            >
                        </div>

                        <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-indigo-600 dark:bg-indigo-500 px-4 py-3.5 text-base font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                            <i class="fas fa-user-plus"></i>
                            Register
                        </button>

                        <p class="mt-6 text-center text-sm text-slate-600 dark:text-white/70">
                            Already have an account?
                            <a href="{{ route('client.login') }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Login</a>
                        </p>
                    </form>
                </div>

                <p class="text-center mt-6 text-xs text-slate-500 dark:text-white/60">
                    <a href="{{ route('client.login') }}" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">Sign in</a>
                    <span class="mx-2 text-slate-300 dark:text-white/20">·</span>
                    <a href="{{ route('public.home') }}" class="hover:text-slate-800 dark:hover:text-white transition-colors">Marketing site</a>
                    <span class="mx-2 text-slate-300 dark:text-white/20">·</span>
                    <a href="{{ route('public.privacy') }}" class="hover:text-slate-800 dark:hover:text-white transition-colors">Privacy</a>
                    <span class="mx-2 text-slate-300 dark:text-white/20">·</span>
                    <a href="{{ route('public.terms') }}" class="hover:text-slate-800 dark:hover:text-white transition-colors">Terms</a>
                </p>
            </div>
        </div>
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

            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const email = document.getElementById('email').value.trim();
                    const phone = document.getElementById('phone').value.trim();
                    if (!email && !phone) {
                        e.preventDefault();
                        alert('Please provide at least an Email address or a Mobile number.');
                    }
                });
            }
        });
    </script>
</body>
</html>

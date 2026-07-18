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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#312e81">
    @stack('head')
    @include('public.partials.seo')
    @include('public.partials.jsonld')
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('css/cb-loader.css') }}?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="{{ asset('js/cb-loader.js') }}?v=2" defer></script>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">
</head>
<body class="min-h-screen flex flex-col bg-gradient-to-br from-indigo-50 via-purple-50 to-white dark:from-indigo-950 dark:via-purple-900 dark:to-slate-950 text-slate-900 dark:text-white transition-colors duration-200">
    <!-- Global Preloader -->
    <div id="cb-loader-overlay" class="cb-loader-overlay--visible" role="status" aria-live="polite" aria-hidden="false" style="position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: flex; align-items: center; justify-content: center; padding: 1rem; transition: opacity 0.18s ease, visibility 0.18s ease;">
        <div class="cb-loader-overlay__panel">
            <div class="cb-loader-logo-container">
                <img src="{{ asset('images/chandla-favicon.png') }}" alt="Chandla Book" class="cb-loader-logo">
                <span class="cb-loader-overlay__spinner">
                    <svg class="cb-loader-spinner" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-opacity="0.25"/>
                        <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>
            <p class="cb-loader-overlay__text">Please wait…</p>
            <p class="cb-loader-overlay__sub" id="cb-loader-overlay-sub" style="display: none;"></p>
        </div>
    </div>

    <header class="max-w-6xl w-full mx-auto px-4 sm:px-6 py-4 flex items-center justify-between shrink-0">
        <a href="{{ route('public.home') }}" class="flex items-center gap-2 sm:gap-3 group">
            <img src="{{ asset('images/chandla-favicon.png') }}" alt="Chandla Book" class="h-10 sm:h-12 w-auto" width="48" height="48" decoding="async">
            <span class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-white/90">Chandla Book</span>
        </a>
        <div class="flex items-center gap-4">
            <button id="cb-dark-mode-toggle" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 dark:border-slate-200/20 hover:border-slate-400 dark:hover:border-slate-300/30 hover:bg-slate-200/50 dark:hover:bg-white/10 text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white transition" title="Toggle theme">
                <i class="fa-solid fa-moon text-sm block dark:hidden"></i>
                <i class="fa-solid fa-sun text-sm hidden dark:block"></i>
            </button>
            <a href="{{ route('public.home') }}" class="text-sm text-slate-600 dark:text-white/80 hover:text-slate-900 dark:hover:text-white font-medium">
                ← Home
            </a>
        </div>
    </header>

    <main class="flex-1 w-full max-w-6xl mx-auto px-4 sm:px-6 pb-10">
        @yield('content')
    </main>

    <footer class="shrink-0 border-t border-slate-200/90 dark:border-slate-800/90 bg-white/50 dark:bg-slate-900/50 backdrop-blur-[2px] pb-8 md:pb-10" role="contentinfo">
        <div class="max-w-6xl w-full mx-auto px-4 sm:px-6 pt-8">
            <div class="flex flex-col items-center justify-center text-center gap-4">
                <div>
                    <p class="text-base font-semibold text-slate-900 dark:text-white">Chandla Book</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-400">
                        Event collections, cash ledger, invitations &amp; guest payments — built for Indian occasions.
                    </p>
                </div>
                <nav class="flex flex-wrap justify-center gap-x-6 gap-y-3 text-sm mt-2" aria-label="Footer">
                    <a href="{{ route('client.dashboard') }}" class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Home</a>
                    <a href="{{ route('client.plans') }}" class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Plans</a>
                    <a href="{{ route('client.faq') }}" class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">FAQ</a>
                    <a href="{{ route('public.contact') }}" class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Contact us</a>
                    @php
                        $footerMail = trim((string) config('chandlabook.support_email', ''));
                        if ($footerMail === '') {
                            $footerMail = (string) config('mail.from.address', '');
                        }
                    @endphp
                    @if($footerMail !== '')
                        <a href="mailto:{{ $footerMail }}" class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Email</a>
                    @endif
                    <a href="{{ route('public.privacy') }}" class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Privacy Policy</a>
                    <a href="{{ route('public.terms') }}" class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Terms of use</a>
                    <a href="{{ route('public.refund') }}" class="text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Refund policy</a>
                </nav>
            </div>
            <div class="mt-8 pt-6 border-t border-slate-200/80 dark:border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left text-[0.7rem] text-slate-500 dark:text-slate-400">
                <span>All rights reserved to SkyLight Technologies</span>
                <span class="flex items-center gap-1">
                    Developed with <i class="fa-solid fa-heart text-red-500 text-[10px]" aria-hidden="true"></i> by
                    <a href="https://skylighttech.in/" target="_blank" rel="noopener noreferrer" class="font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors underline decoration-slate-300 dark:decoration-slate-700 underline-offset-2 hover:decoration-indigo-400">SkyLight Technologies</a>
                </span>
            </div>
        </div>
    </footer>

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
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Chandla Book</title>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">
    <meta name="theme-color" content="#312e81">
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('css/cb-loader.css') }}?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="{{ asset('js/cb-loader.js') }}?v=2" defer></script>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
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

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-indigo-900 to-indigo-800 text-white transform transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0" id="sidebar">
        <div class="flex items-center justify-between h-16 px-6 border-b border-indigo-700">
            <div class="flex items-center">
                <img src="{{ asset('images/chandla-favicon.png') }}" alt="Vaish" class="h-9 w-auto mr-2">
                <h1 class="text-xl font-bold">Chandla Book</h1>
            </div>
            <button id="sidebarToggle" class="lg:hidden">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="mt-6">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-home w-5 mr-3"></i>
                Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-users w-5 mr-3"></i>
                Users
            </a>
            <a href="{{ route('admin.events.index') }}" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.events.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-calendar w-5 mr-3"></i>
                Events
            </a>
            <a href="{{ route('admin.event-types.index') }}" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.event-types.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-tags w-5 mr-3"></i>
                Event Types
            </a>
            <a href="{{ route('admin.contacts.index') }}" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.contacts.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-address-book w-5 mr-3"></i>
                Contacts
            </a>
            <a href="{{ route('admin.payments.index') }}" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.payments.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-credit-card w-5 mr-3"></i>
                Payments
            </a>
            <a href="{{ route('admin.plans.index') }}" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.plans.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-layer-group w-5 mr-3"></i>
                Plans
            </a>
            <a href="{{ route('admin.chandlas.index') }}" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.chandlas.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-gift w-5 mr-3"></i>
                Chandlas
            </a>
            <a href="{{ route('admin.reports.chandla') }}" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.reports.chandla') ? 'bg-indigo-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-chart-bar w-5 mr-3"></i>
                Chandla Report
            </a>
            <a href="{{ route('admin.reports.events-summary') }}" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors {{ request()->routeIs('admin.reports.events-summary') ? 'bg-indigo-700 border-r-4 border-yellow-400' : '' }}">
                <i class="fas fa-clipboard-list w-5 mr-3"></i>
                Event Summary
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-64 flex flex-col min-h-screen">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
            <div class="flex items-center justify-between h-16 px-6">
                <button id="mobileSidebarToggle" class="lg:hidden text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6 flex-1">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="shrink-0 border-t border-gray-200 bg-white px-6 py-5 mt-auto" role="contentinfo">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
                <div class="flex flex-col items-start gap-1">
                    <span>All rights reserved to SkyLight Technologies · Admin</span>
                    <span class="flex items-center gap-1">
                        Developed with <i class="fa-solid fa-heart text-red-500 text-[10px]" aria-hidden="true"></i> by
                        <a href="https://skylighttech.in/" target="_blank" rel="noopener noreferrer" class="font-medium hover:text-indigo-600 transition-colors underline decoration-gray-200 underline-offset-2">SkyLight Technologies</a>
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('public.privacy') }}" class="hover:text-indigo-600 transition-colors">Privacy Policy</a>
                    <a href="{{ route('public.terms') }}" class="hover:text-indigo-600 transition-colors">Terms of use</a>
                    <a href="{{ route('public.refund') }}" class="hover:text-indigo-600 transition-colors">Refund policy</a>
                    <a href="{{ route('public.home') }}" class="text-indigo-600 hover:text-indigo-800 font-medium ml-2 border-l border-gray-300 pl-3">Public website</a>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Mobile sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');

        mobileSidebarToggle?.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
        });

        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
        });

        // Close sidebar on outside click (mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024) {
                if (!sidebar.contains(e.target) && !mobileSidebarToggle.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

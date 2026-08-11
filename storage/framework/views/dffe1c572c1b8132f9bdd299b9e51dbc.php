<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Admin Panel'); ?> - Chandla Book</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta name="theme-color" content="#312e81">
    <link rel="stylesheet" href="<?php echo e(asset('css/tailwind.css')); ?>?v=2">
    <link rel="stylesheet" href="<?php echo e(asset('css/cb-loader.css')); ?>?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <script src="<?php echo e(asset('js/cb-loader.js')); ?>?v=2" defer></script>
    <style>
        /* Minimal Tailwind overrides for simple-datatables */
        .dataTable-wrapper { font-family: inherit; }
        .dataTable-selector, .dataTable-input { border-radius: 0.375rem; border: 1px solid #d1d5db; padding: 0.375rem 0.75rem; font-size: 0.875rem; outline: none; }
        .dataTable-selector:focus, .dataTable-input:focus { border-color: #6366f1; ring: 2px solid #6366f1; }
        .dataTable-table > thead > tr > th { background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; color: #6b7280; padding: 0.75rem 1.5rem; text-align: left; }
        .dataTable-table > tbody > tr > td { padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; }
        .dataTable-pagination a { border-radius: 0.375rem; color: #4f46e5; }
        .dataTable-pagination .active a, .dataTable-pagination .active a:hover { background-color: #4f46e5; color: white; }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Global Preloader -->
    <div id="cb-loader-overlay" class="cb-loader-overlay--visible" role="status" aria-live="polite" aria-hidden="false" style="position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: flex; align-items: center; justify-content: center; padding: 1rem; transition: opacity 0.18s ease, visibility 0.18s ease;">
        <div class="cb-loader-overlay__panel">
            <div class="cb-loader-logo-container">
                <img src="<?php echo e(asset('images/logo.jpeg')); ?>" alt="Chandla Book" class="cb-loader-logo">
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
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-indigo-900 to-indigo-800 text-white transform transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 flex flex-col" id="sidebar">
        <div class="flex items-center justify-between h-16 px-6 border-b border-indigo-700 shrink-0">
            <div class="flex items-center">
                <img src="<?php echo e(asset('images/logo.jpeg')); ?>" alt="Vaish" class="h-9 w-auto mr-2">
                <h1 class="text-xl font-bold">Chandla Book</h1>
            </div>
            <button id="sidebarToggle" class="lg:hidden">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto py-6">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-home w-5 mr-3"></i>
                Dashboard
            </a>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.users.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-users w-5 mr-3"></i>
                Users
            </a>
            <a href="<?php echo e(route('admin.events.index')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.events.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-calendar w-5 mr-3"></i>
                Events
            </a>
            <a href="<?php echo e(route('admin.event-types.index')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.event-types.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-tags w-5 mr-3"></i>
                Event Types
            </a>
            <a href="<?php echo e(route('admin.contacts.index')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.contacts.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-address-book w-5 mr-3"></i>
                Contacts
            </a>
            <a href="<?php echo e(route('admin.payments.index')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.payments.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-credit-card w-5 mr-3"></i>
                Payments
            </a>
            <a href="<?php echo e(route('admin.plans.index')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.plans.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-layer-group w-5 mr-3"></i>
                Plans
            </a>
            <a href="<?php echo e(route('admin.vendors.index')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.vendors.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-store w-5 mr-3"></i>
                Vendors
            </a>
            <a href="<?php echo e(route('admin.chandlas.index')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.chandlas.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-gift w-5 mr-3"></i>
                Chandlas
            </a>
            <a href="<?php echo e(route('admin.reports.chandla')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.reports.chandla') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-chart-bar w-5 mr-3"></i>
                Chandla Report
            </a>
            <a href="<?php echo e(route('admin.reports.events-summary')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.reports.events-summary') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-clipboard-list w-5 mr-3"></i>
                Event Summary
            </a>
            <a href="<?php echo e(route('admin.notifications.create')); ?>" class="flex items-center px-6 py-3 text-gray-200 hover:bg-indigo-700 transition-colors <?php echo e(request()->routeIs('admin.notifications.*') ? 'bg-indigo-700 border-r-4 border-yellow-400' : ''); ?>">
                <i class="fas fa-bell w-5 mr-3"></i>
                Push Notifications
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
                    <span class="text-gray-700"><?php echo e(Auth::user()->name); ?></span>
                    <form action="<?php echo e(route('admin.logout')); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6 flex-1">
            <?php if(session('success')): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
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
                    <a href="<?php echo e(route('public.privacy')); ?>" class="hover:text-indigo-600 transition-colors">Privacy Policy</a>
                    <a href="<?php echo e(route('public.terms')); ?>" class="hover:text-indigo-600 transition-colors">Terms of use</a>
                    <a href="<?php echo e(route('public.refund')); ?>" class="hover:text-indigo-600 transition-colors">Refund policy</a>
                    <a href="<?php echo e(route('public.home')); ?>" class="text-indigo-600 hover:text-indigo-800 font-medium ml-2 border-l border-gray-300 pl-3">Public website</a>
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
        
        // Initialize simple-datatables
        document.addEventListener('DOMContentLoaded', () => {
            const tables = document.querySelectorAll('.data-table');
            tables.forEach(table => {
                new simpleDatatables.DataTable(table, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 15,
                    perPageSelect: [15, 25, 50, 100],
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/layouts/admin.blade.php ENDPATH**/ ?>
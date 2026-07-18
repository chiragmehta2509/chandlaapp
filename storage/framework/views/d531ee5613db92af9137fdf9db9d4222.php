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
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - Chandla Book</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta name="theme-color" content="#1a3646">
    <link rel="stylesheet" href="<?php echo e(asset('css/tailwind.css')); ?>?v=4">
    <link rel="stylesheet" href="<?php echo e(asset('css/chandla-client.css')); ?>?v=12">
    <link rel="stylesheet" href="<?php echo e(asset('css/cb-loader.css')); ?>?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="<?php echo e(asset('js/cb-loader.js')); ?>?v=2" defer></script>
    <style>
        .cb-user-menu > summary { list-style: none; }
        .cb-user-menu > summary::-webkit-details-marker { display: none; }
        .cb-user-menu[open] > summary .cb-user-menu-chev { transform: rotate(180deg); }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="cb-app min-h-screen flex flex-col">
    <!-- Global Preloader -->
    <div id="cb-loader-overlay" class="cb-loader-overlay--visible" role="status" aria-live="polite" aria-hidden="false" style="position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); display: flex; align-items: center; justify-content: center; padding: 1rem; transition: opacity 0.18s ease, visibility 0.18s ease;">
        <div class="cb-loader-overlay__panel">
            <div class="cb-loader-logo-container">
                <img src="<?php echo e(asset('images/chandla-favicon.png')); ?>" alt="Chandla Book" class="cb-loader-logo">
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

    <header class="cb-topnav sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex h-14 sm:h-16 items-center justify-between gap-2">
                <div class="flex items-center min-w-0 flex-1">
                    <a href="<?php echo e(route('client.dashboard')); ?>" class="flex items-center min-w-0 group">
                        <img src="<?php echo e(asset('images/chandla-favicon.png')); ?>" alt="Chandla Book" class="h-8 w-auto sm:h-10 shrink-0 opacity-90 group-hover:opacity-100" width="40" height="40">
                        <span class="cb-brand-title text-lg sm:text-2xl ml-2 sm:ml-3 truncate">Chandla Book</span>
                    </a>
                </div>
                <nav class="hidden md:flex items-center gap-1 flex-wrap justify-end" aria-label="Main">
                    <a href="<?php echo e(route('client.dashboard')); ?>" class="cb-nav-item <?php echo e(request()->routeIs('client.dashboard') ? 'cb-nav-item--active' : ''); ?>">Home</a>
                    <a href="<?php echo e(route('client.events.index')); ?>" class="cb-nav-item <?php echo e(request()->routeIs('client.events.*') ? 'cb-nav-item--active' : ''); ?>">Events</a>
                    <a href="<?php echo e(route('client.chandlas.index')); ?>" class="cb-nav-item <?php echo e(request()->routeIs('client.chandlas.*') ? 'cb-nav-item--active' : ''); ?>">Ledger</a>
                    <a href="<?php echo e(route('client.contacts.index')); ?>" class="cb-nav-item <?php echo e(request()->routeIs('client.contacts.*') ? 'cb-nav-item--active' : ''); ?>">Contacts</a>
                    <a href="<?php echo e(route('client.marriage-invitations.index')); ?>" class="cb-nav-item <?php echo e(request()->routeIs('client.marriage-invitations.*') ? 'cb-nav-item--active' : ''); ?>">Invitation</a>
                    <a href="<?php echo e(route('client.pre-wedding.index')); ?>" class="cb-nav-item <?php echo e(request()->routeIs('client.pre-wedding.*') ? 'cb-nav-item--active' : ''); ?>">Pre-wedding</a>
                    <a href="<?php echo e(route('client.plans')); ?>" class="cb-nav-item <?php echo e(request()->routeIs('client.plans') ? 'cb-nav-item--active' : ''); ?>">Plans</a>
                    <a href="<?php echo e(route('client.faq')); ?>" class="cb-nav-item <?php echo e(request()->routeIs('client.faq') ? 'cb-nav-item--active' : ''); ?>">FAQ</a>
                    
                </nav>
                <div class="flex items-center gap-2 relative shrink-0 z-[60]">
                    <button id="cb-dark-mode-toggle" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-500 hover:text-slate-700 transition" title="Toggle theme">
                        <i class="fa-solid fa-moon text-sm dark-icon"></i>
                        <i class="fa-solid fa-sun text-sm light-icon hidden"></i>
                    </button>
                    <details class="cb-user-menu group relative">
                        <summary
                            class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-transparent px-2 py-1.5 text-sm font-medium text-slate-700 transition hover:border-slate-200 hover:bg-slate-50/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-cb-navy/30"
                            title="Account menu"
                            aria-label="Open account menu"
                        >
                            <span class="max-w-[8rem] sm:max-w-[12rem] truncate" title="<?php echo e(Auth::user()->name); ?>"><?php echo e(Auth::user()->name); ?></span>
                            <i class="cb-user-menu-chev fa-solid fa-chevron-down text-[0.65rem] text-slate-400 transition-transform duration-200" aria-hidden="true"></i>
                        </summary>
                        <div
                            class="absolute right-0 top-full z-[60] mt-1.5 w-52 overflow-hidden rounded-xl border border-slate-200/90 bg-white py-1 shadow-lg"
                            role="menu"
                        >
                            
                            <a
                                href="<?php echo e(route('client.profile')); ?>"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50 <?php echo e(request()->routeIs('client.profile') ? 'bg-slate-50/80' : ''); ?>"
                                role="menuitem"
                            >
                                <i class="fa-solid fa-circle-user w-4 text-center text-slate-400" aria-hidden="true"></i>
                                <span class="flex-1 min-w-0">
                                    <span class="block font-semibold">Profile</span>
                                    <span class="block text-xs text-slate-400 truncate"><?php echo e(Auth::user()->email ?: Auth::user()->phone); ?></span>
                                </span>
                            </a>
                            <div class="my-1 border-t border-slate-100" role="separator"></div>
                            <?php if (\Illuminate\Support\Facades\Blade::check('isMainUser')): ?>
                                <a
                                    href="<?php echo e(route('client.family-members.index')); ?>"
                                    class="flex items-center gap-2 px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50 <?php echo e(request()->routeIs('client.family-members.*') ? 'bg-slate-50/80' : ''); ?>"
                                    role="menuitem"
                                >
                                    <i class="fa-solid fa-users w-4 text-center text-slate-400" aria-hidden="true"></i>
                                    Family members
                                </a>
                            <?php endif; ?>
                            <a
                                href="<?php echo e(route('client.plans')); ?>"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50 <?php echo e(request()->routeIs('client.plans') ? 'bg-slate-50/80' : ''); ?>"
                                role="menuitem"
                            >
                                <i class="fa-solid fa-box-open w-4 text-center text-slate-400" aria-hidden="true"></i>
                                My Packages
                            </a>
                            <a
                                href="<?php echo e(route('client.transactions.index')); ?>"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50 <?php echo e(request()->routeIs('client.transactions.*') ? 'bg-slate-50/80' : ''); ?>"
                                role="menuitem"
                            >
                                <i class="fa-solid fa-receipt w-4 text-center text-slate-400" aria-hidden="true"></i>
                                Transactions
                            </a>
                            <a
                                href="<?php echo e(route('client.password.edit')); ?>"
                                class="flex items-center gap-2 px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50 <?php echo e(request()->routeIs('client.password.*') ? 'bg-slate-50/80' : ''); ?>"
                                role="menuitem"
                            >
                                <i class="fa-solid fa-key w-4 text-center text-slate-400" aria-hidden="true"></i>
                                Change password
                            </a>
                            <form action="<?php echo e(route('client.logout')); ?>" method="POST" role="none">
                                <?php echo csrf_field(); ?>
                                <button
                                    type="submit"
                                    class="flex w-full items-center gap-2 border-t border-slate-100 px-3 py-2.5 text-left text-sm text-slate-700 transition hover:bg-slate-50"
                                    role="menuitem"
                                >
                                    <i class="fa-solid fa-right-from-bracket w-4 text-center text-slate-400" aria-hidden="true"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </header>

    <?php if (\Illuminate\Support\Facades\Blade::check('familyViewer')): ?>
        <div class="bg-amber-100 border-b border-amber-200 text-amber-900 text-xs sm:text-sm px-3 sm:px-6 py-2 text-center">
            <i class="fas fa-eye mr-1.5" aria-hidden="true"></i>
            You're signed in as a <strong>family viewer</strong> for <strong><?php echo e(Auth::user()->parent?->name ?? 'this account'); ?></strong>. Read-only — you can browse and download but cannot add or edit.
        </div>
    <?php endif; ?>
    <?php if (\Illuminate\Support\Facades\Blade::check('familyEditor')): ?>
        <div class="bg-emerald-100 border-b border-emerald-200 text-emerald-900 text-xs sm:text-sm px-3 sm:px-6 py-2 text-center">
            <i class="fas fa-shield-halved mr-1.5" aria-hidden="true"></i>
            You're signed in as a <strong>family editor</strong> for <strong><?php echo e(Auth::user()->parent?->name ?? 'this account'); ?></strong>. You can add and edit, but cannot delete records, manage other family members, or buy plans.
        </div>
    <?php endif; ?>

    <main class="cb-main flex-1 min-h-0 min-w-0 max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 pb-24 md:pb-6 overflow-x-clip w-full">
        <?php if(session('success')): ?>
            <div class="cb-alert cb-alert--success mb-4" role="status" aria-live="polite">
                <span class="block font-semibold"><?php echo e(session('success')); ?></span>
                <?php if(session('chandla_saved_summary') && is_array(session('chandla_saved_summary'))): ?>
                    <?php $cs = session('chandla_saved_summary'); ?>
                    <dl class="mt-3 space-y-2 text-sm border-t border-emerald-700/15 pt-3 text-emerald-950/95">
                        <div class="flex flex-col gap-0.5 sm:flex-row sm:items-baseline sm:justify-between sm:gap-4">
                            <dt class="font-medium text-emerald-950/80 shrink-0">Name</dt>
                            <dd class="break-words sm:text-right"><?php echo e($cs['giver_name'] ?? '—'); ?></dd>
                        </div>
                        <div class="flex flex-col gap-0.5 sm:flex-row sm:items-baseline sm:justify-between sm:gap-4">
                            <dt class="font-medium text-emerald-950/80 shrink-0">Amount</dt>
                            <dd class="tabular-nums font-semibold sm:text-right">₹<?php echo e(number_format((float) ($cs['amount'] ?? 0), 2)); ?></dd>
                        </div>
                        <div class="flex flex-col gap-0.5 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                            <dt class="font-medium text-emerald-950/80 shrink-0 pt-0.5">Address</dt>
                            <dd class="break-words sm:text-right max-w-xl"><?php echo e($cs['giver_address'] ?? '—'); ?></dd>
                        </div>
                    </dl>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if(session('info')): ?>
            <div class="mb-4 rounded-lg border border-sky-200 bg-sky-50 text-sky-900 px-4 py-3 text-sm" role="status">
                <?php echo e(session('info')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="cb-alert cb-alert--error mb-4" role="alert">
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer class="cb-site-footer shrink-0 border-t border-slate-200/90 bg-white/95 backdrop-blur-[2px]" role="contentinfo">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-8 pb-28 md:pb-10">
            <div class="flex flex-col items-center justify-center text-center gap-4">
                <div>
                    <p class="text-base font-semibold text-[var(--cb-navy)]">Chandla Book</p>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">
                        Event collections, cash ledger, invitations &amp; guest payments — built for Indian occasions.
                    </p>
                </div>
                <nav class="flex flex-wrap justify-center gap-x-6 gap-y-3 text-sm mt-2" aria-label="Footer">
                    <a href="<?php echo e(route('client.dashboard')); ?>" class="text-slate-600 hover:text-[var(--cb-navy)] transition-colors">Home</a>
                    <a href="<?php echo e(route('client.plans')); ?>" class="text-slate-600 hover:text-[var(--cb-navy)] transition-colors">Plans</a>
                    <a href="<?php echo e(route('client.faq')); ?>" class="text-slate-600 hover:text-[var(--cb-navy)] transition-colors">FAQ</a>
                    <a href="<?php echo e(route('client.contact')); ?>" class="text-slate-600 hover:text-[var(--cb-navy)] transition-colors">Contact us</a>
                    <?php
                        $footerMail = trim((string) config('chandlabook.support_email', ''));
                        if ($footerMail === '') {
                            $footerMail = (string) config('mail.from.address', '');
                        }
                    ?>
                    <?php if($footerMail !== ''): ?>
                        <a href="mailto:<?php echo e($footerMail); ?>" class="text-slate-600 hover:text-[var(--cb-navy)] transition-colors">Email</a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('public.privacy')); ?>" class="text-slate-600 hover:text-[var(--cb-navy)] transition-colors">Privacy Policy</a>
                    <a href="<?php echo e(route('public.terms')); ?>" class="text-slate-600 hover:text-[var(--cb-navy)] transition-colors">Terms of use</a>
                    <a href="<?php echo e(route('public.refund')); ?>" class="text-slate-600 hover:text-[var(--cb-navy)] transition-colors">Refund policy</a>
                </nav>
            </div>
            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left text-[0.7rem] text-slate-400">
                <span>All rights reserved to SkyLight Technologies</span>
                <span class="flex items-center gap-1">
                    Developed with <i class="fa-solid fa-heart text-red-500 text-[10px]" aria-hidden="true"></i> by
                    <a href="https://skylighttech.in/" target="_blank" rel="noopener noreferrer" class="font-medium hover:text-[var(--cb-navy)] transition-colors underline decoration-slate-200 underline-offset-2">SkyLight Technologies</a>
                </span>
            </div>
        </div>
    </footer>

    <nav class="cb-bottom-nav md:hidden fixed bottom-0 left-0 right-0 z-50" aria-label="Primary">
        <div class="cb-bottom-nav__scroll max-w-7xl mx-auto">
            <a href="<?php echo e(route('client.dashboard')); ?>" class="cb-bottom-nav__item <?php echo e(request()->routeIs('client.dashboard') ? 'cb-bottom-nav__item--active' : ''); ?>">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>
            <a href="<?php echo e(route('client.events.index')); ?>" class="cb-bottom-nav__item <?php echo e(request()->routeIs('client.events.*') ? 'cb-bottom-nav__item--active' : ''); ?>">
                <i class="fa-regular fa-calendar"></i>
                <span>Events</span>
            </a>
            <a href="<?php echo e(route('client.chandlas.index')); ?>" class="cb-bottom-nav__item <?php echo e(request()->routeIs('client.chandlas.*') ? 'cb-bottom-nav__item--active' : ''); ?>">
                <i class="fa-solid fa-book-open"></i>
                <span>Ledger</span>
            </a>
            <a href="<?php echo e(route('client.contacts.index')); ?>" class="cb-bottom-nav__item <?php echo e(request()->routeIs('client.contacts.*') ? 'cb-bottom-nav__item--active' : ''); ?>">
                <i class="fa-solid fa-user-group"></i>
                <span>Contacts</span>
            </a>
            <a href="<?php echo e(route('client.marriage-invitations.index')); ?>" class="cb-bottom-nav__item <?php echo e(request()->routeIs('client.marriage-invitations.*') ? 'cb-bottom-nav__item--active' : ''); ?>">
                <i class="fa-solid fa-envelope-open-text"></i>
                <span>Invite</span>
            </a>
            <a href="<?php echo e(route('client.pre-wedding.index')); ?>" class="cb-bottom-nav__item <?php echo e(request()->routeIs('client.pre-wedding.*') ? 'cb-bottom-nav__item--active' : ''); ?>">
                <i class="fa-solid fa-camera-retro"></i>
                <span>PreWed</span>
            </a>
            
            <a href="<?php echo e(route('client.password.edit')); ?>" class="cb-bottom-nav__item <?php echo e(request()->routeIs('client.password.*') ? 'cb-bottom-nav__item--active' : ''); ?>">
                <i class="fa-solid fa-gear"></i>
                <span>Account</span>
            </a>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('cb-dark-mode-toggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    if (window.eventCollectionsChartInstance) {
                        const chartColor = isDark ? '#cbd5e1' : '#6b7280';
                        window.eventCollectionsChartInstance.options.plugins.legend.labels.color = chartColor;
                        window.eventCollectionsChartInstance.update();
                    }
                });
            }
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/layouts/client.blade.php ENDPATH**/ ?>
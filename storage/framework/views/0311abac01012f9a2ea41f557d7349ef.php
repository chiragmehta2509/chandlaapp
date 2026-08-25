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
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Already Logged In - Chandla Book</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/tailwind.css')); ?>?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white transition-colors duration-200">
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-white dark:from-indigo-950 dark:via-purple-900 dark:to-slate-950 transition-colors duration-200 flex flex-col">
        <header class="max-w-6xl w-full mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="<?php echo e(route('public.home')); ?>" class="flex items-center gap-2 sm:gap-3 group">
                <img src="<?php echo e(asset('images/logo.jpeg')); ?>" alt="Chandla Book" class="h-10 sm:h-12 w-auto">
                <span class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:hover:text-white/90">Chandla Book</span>
            </a>
            <div class="flex items-center gap-4">
                <button id="cb-dark-mode-toggle" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 dark:border-slate-200/20 hover:border-slate-400 dark:hover:border-slate-300/30 hover:bg-slate-200/50 dark:hover:bg-white/10 text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white transition" title="Toggle theme">
                    <i class="fa-solid fa-moon text-sm block dark:hidden"></i>
                    <i class="fa-solid fa-sun text-sm hidden dark:block"></i>
                </button>
                <a href="<?php echo e(route('public.home')); ?>" class="text-sm text-slate-700 dark:text-white/80 hover:text-slate-900 dark:text-white font-medium">
                    &larr; Home
                </a>
            </div>
        </header>

        <div class="flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-white/10 backdrop-blur-md rounded-2xl border border-slate-200 dark:border-white/20 shadow-xl overflow-hidden">
                    <div class="p-6 sm:p-8">
                        <div class="text-center mb-8">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-300 mb-4 ring-8 ring-indigo-50 dark:ring-indigo-900/20">
                                <i class="fa-solid fa-user text-2xl"></i>
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">Already Signed In</h1>
                            <p class="text-slate-600 dark:text-white/70 mt-3 text-sm sm:text-base">
                                You are currently logged in as <strong class="text-slate-800 dark:text-white font-semibold"><?php echo e(Auth::user()->name ?: Auth::user()->email); ?></strong>.
                            </p>
                        </div>

                        <div class="space-y-4">
                            <a href="<?php echo e(route('client.dashboard')); ?>" class="w-full inline-flex items-center justify-center rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 px-4 shadow-lg shadow-indigo-900/20 transition duration-150 ease-in-out">
                                <i class="fa-solid fa-gauge mr-2"></i> Continue to Dashboard
                            </a>

                            <div class="relative flex py-2 items-center">
                                <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
                                <span class="flex-shrink mx-4 text-slate-400 dark:text-white/30 text-xs font-bold uppercase tracking-wider">or</span>
                                <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
                            </div>

                            <form id="logout-form" action="<?php echo e(route('client.logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full inline-flex items-center justify-center rounded-xl border border-red-200 hover:border-red-300 dark:border-red-500/30 hover:bg-red-50 dark:hover:bg-red-500/10 text-red-600 dark:text-red-300 font-semibold py-3 px-4 transition duration-150 ease-in-out">
                                    <i class="fa-solid fa-right-from-bracket mr-2"></i> Log out and login again
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const toggle = document.getElementById('cb-dark-mode-toggle');
            if (toggle) {
                toggle.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                });
            }
        })();
    </script>
</body>
</html>
<?php /**PATH /home/chandlabook/public_html/resources/views/client/auth/already_logged_in.blade.php ENDPATH**/ ?>
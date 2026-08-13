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
    <title>Login - Chandla Book</title>
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
                            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">Welcome back</h1>
                            <p class="text-slate-600 dark:text-white/70 mt-2 text-sm sm:text-base">Sign in to your account</p>
                        </div>

                        <?php if($errors->any()): ?>
                            <div class="mb-4 rounded-xl border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/20 px-4 py-3 text-sm text-red-700 dark:text-red-200">
                                <ul class="list-disc list-inside space-y-1">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <?php if(session('status')): ?>
                            <div class="mb-4 rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/20 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-200">
                                <?php echo e(session('status')); ?>

                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('client.login')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="mb-5">
                                <label class="block text-sm font-medium text-slate-700 dark:text-white/80 mb-2" for="login">Email or phone</label>
                                <input
                                    class="w-full rounded-xl border border-slate-200 dark:border-white/20 bg-white dark:bg-white/10 px-4 py-3 text-slate-900 dark:text-white placeholder:text-slate-400 dark:text-white/40 focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                                    id="login"
                                    type="text"
                                    name="login"
                                    value="<?php echo e(old('login', \Illuminate\Support\Facades\Cookie::get('remembered_login'))); ?>"
                                    required
                                    autocomplete="username"
                                    autofocus
                                >
                            </div>

                            <div class="mb-5">
                                <label class="block text-sm font-medium text-slate-700 dark:text-white/80 mb-2" for="password">Password</label>
                                <div class="relative">
                                    <input
                                        class="w-full rounded-xl border border-slate-200 dark:border-white/20 bg-white dark:bg-white/10 px-4 py-3 pr-10 text-slate-900 dark:text-white placeholder:text-slate-400 dark:text-white/40 focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                                        id="password"
                                        type="password"
                                        name="password"
                                        value="<?php echo e(\Illuminate\Support\Facades\Cookie::get('remembered_password')); ?>"
                                        required
                                        autocomplete="current-password"
                                    >
                                    <button type="button"
                                        onmousedown="document.getElementById('password').type='text'; this.querySelector('i').classList.replace('fa-eye','fa-eye-slash');"
                                        onmouseup="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash','fa-eye');"
                                        onmouseleave="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash','fa-eye');"
                                        ontouchstart="document.getElementById('password').type='text'; this.querySelector('i').classList.replace('fa-eye','fa-eye-slash');"
                                        ontouchend="document.getElementById('password').type='password'; this.querySelector('i').classList.replace('fa-eye-slash','fa-eye');"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 dark:text-white/50 hover:text-slate-900 dark:text-white focus:outline-none">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="text-right mt-2">
                                    <a href="<?php echo e(route('password.request')); ?>" class="text-sm font-medium text-indigo-600 dark:text-indigo-200 hover:text-indigo-800 dark:hover:text-slate-900 dark:text-white">Forgot password?</a>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-white/80 cursor-pointer">
                                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 dark:border-white/30 bg-white dark:bg-white/10 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0" <?php echo e(\Illuminate\Support\Facades\Cookie::has('remembered_login') ? 'checked' : ''); ?>>
                                    Remember me
                                </label>
                            </div>

                            <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-lg bg-indigo-600 dark:bg-white px-4 py-3 text-base font-semibold text-white dark:text-indigo-700 shadow transition hover:bg-indigo-700 dark:hover:bg-indigo-50">
                                <i class="fas fa-sign-in-alt"></i>
                                Login
                            </button>

                            <p class="mt-6 text-center text-sm text-slate-600 dark:text-white/70">
                                Don&apos;t have an account?
                                <a href="<?php echo e(route('client.register')); ?>" class="font-semibold text-indigo-600 dark:text-white hover:underline">Register</a>
                            </p>
                        </form>
                    </div>

                    
                    <div class="border-t border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-6 py-3 flex flex-wrap items-center justify-center gap-x-5 gap-y-1.5 text-xs text-slate-500 dark:text-white/50">
                        <a href="<?php echo e(route('public.privacy')); ?>"
                           class="inline-flex items-center gap-1.5 hover:text-indigo-600 dark:hover:text-white/90 transition-colors">
                            <i class="fas fa-shield-halved text-[0.65rem]" aria-hidden="true"></i>
                            Privacy Policy
                        </a>
                        <span class="text-slate-300 dark:text-white/20" aria-hidden="true">|</span>
                        <a href="<?php echo e(route('public.terms')); ?>"
                           class="inline-flex items-center gap-1.5 hover:text-indigo-600 dark:hover:text-white/90 transition-colors">
                            <i class="fas fa-file-lines text-[0.65rem]" aria-hidden="true"></i>
                            Terms of Service
                        </a>
                        <span class="text-slate-300 dark:text-white/20" aria-hidden="true">|</span>
                        <a href="<?php echo e(route('public.home')); ?>"
                           class="inline-flex items-center gap-1.5 hover:text-indigo-600 dark:hover:text-white/90 transition-colors">
                            <i class="fas fa-globe text-[0.65rem]" aria-hidden="true"></i>
                            Marketing site
                        </a>
                    </div>

                </div>
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
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/auth/login.blade.php ENDPATH**/ ?>
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
    <title>Verify OTP - Chandla Book</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/tailwind.css')); ?>?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white transition-colors duration-200">
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-white dark:from-indigo-950 dark:via-purple-900 dark:to-slate-950 transition-colors duration-200 flex flex-col">
        <header class="max-w-6xl w-full mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="<?php echo e(route('public.home')); ?>" class="flex items-center gap-2 sm:gap-3 group">
                <img src="<?php echo e(asset('images/logo.jpeg')); ?>" alt="Chandla Book" class="h-10 sm:h-12 w-auto" decoding="async">
                <span class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:hover:text-white/90">Chandla Book</span>
            </a>
            <div class="flex items-center gap-4">
                <button id="cb-dark-mode-toggle" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 dark:border-slate-200/20 hover:border-slate-400 dark:hover:border-slate-300/30 hover:bg-slate-200/50 dark:hover:bg-white/10 text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white transition" title="Toggle theme">
                    <i class="fa-solid fa-moon text-sm block dark:hidden"></i>
                    <i class="fa-solid fa-sun text-sm hidden dark:block"></i>
                </button>
            </div>
        </header>

        <div class="flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-white/10 backdrop-blur-md rounded-2xl border border-slate-200 dark:border-white/20 p-6 sm:p-8 shadow-xl">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/50 mb-4 text-indigo-600 dark:text-indigo-400">
                            <i class="fas fa-shield-alt text-2xl"></i>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">Verify Your Account</h1>
                        <p class="text-slate-600 dark:text-white/70 mt-2 text-sm sm:text-base">We've sent a verification link to your <?php echo e(session('registration_sent_to', 'contact details')); ?>.</p>
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

                        <div class="mb-8 p-6 bg-indigo-50/50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-500/20 rounded-xl text-center">
                            <i class="fab fa-whatsapp text-4xl text-green-500 mb-3"></i>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Verification Link Sent!</h3>
                            <p class="text-sm text-slate-600 dark:text-white/70">
                                Please check your WhatsApp messages. Tap the link we sent you to securely verify your account.
                            </p>
                        </div>

                        <a href="<?php echo e(route('client.login')); ?>" class="w-full flex items-center justify-center gap-2 rounded-xl bg-indigo-600 dark:bg-indigo-500 px-4 py-3.5 text-base font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                            I've Verified My Account (Login)
                        </a>
                    
                    <div class="mt-6 text-center text-sm">
                        <form method="POST" action="<?php echo e(route('client.register')); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="resend_otp" value="1">
                            <button type="submit" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline bg-transparent border-none p-0 cursor-pointer">
                                Resend Link
                            </button>
                        </form>
                    </div>
                </div>

                <p class="text-center mt-6 text-xs text-slate-500 dark:text-white/60">
                    <a href="<?php echo e(route('client.register')); ?>" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">Change details</a>
                    <span class="mx-2 text-slate-300 dark:text-white/20">·</span>
                    <a href="<?php echo e(route('client.login')); ?>" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">Login</a>
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
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/auth/verify-otp.blade.php ENDPATH**/ ?>
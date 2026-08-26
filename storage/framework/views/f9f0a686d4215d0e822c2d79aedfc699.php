<?php $__env->startSection('title', 'My Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto w-full min-w-0">
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-5">
            <div class="flex h-12 w-12 sm:h-14 sm:w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500/15 to-violet-600/10 text-indigo-700 ring-1 ring-indigo-200/60 shadow-sm"
                 aria-hidden="true">
                <i class="fas fa-user text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="cb-page-title text-2xl sm:text-3xl leading-tight">My Profile</h1>
                <p class="cb-subtitle mt-1.5 max-w-xl text-sm sm:text-base leading-relaxed">
                    Your account information.
                </p>
            </div>
        </div>
    </div>

    <div class="cb-card overflow-hidden border border-slate-200/80 shadow-sm rounded-2xl">
        <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 via-white to-indigo-50/20">
            <h2 class="text-sm font-bold text-cb-navy tracking-wide">Account details</h2>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">Your current login information.</p>
        </div>

        <?php if(session('status')): ?>
            <div class="px-4 py-3 bg-emerald-50 text-emerald-700 text-sm border-b border-emerald-100">
                <i class="fas fa-check-circle mr-1"></i> <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="px-4 py-3 bg-red-50 text-red-700 text-sm border-b border-red-100">
                <i class="fas fa-exclamation-circle mr-1"></i> <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <div class="divide-y divide-slate-100">
            
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5">
                <div class="sm:w-40 shrink-0">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                        <i class="fas fa-id-card w-4 text-center text-slate-400" aria-hidden="true"></i>
                        Full Name
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 break-words"><?php echo e($user->name); ?></p>
                </div>
            </div>

            
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5">
                <div class="sm:w-40 shrink-0">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                        <i class="fas fa-envelope w-4 text-center text-slate-400" aria-hidden="true"></i>
                        Email
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <?php if($user->email): ?>
                        <p class="text-sm font-semibold text-slate-900 break-all"><?php echo e($user->email); ?></p>
                    <?php else: ?>
                        <p class="text-sm text-slate-400 italic">Not set</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if($user->phone): ?>
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5">
                <div class="sm:w-40 shrink-0">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                        <i class="fas fa-phone w-4 text-center text-slate-400" aria-hidden="true"></i>
                        Phone
                    </span>
                </div>
                <div class="flex-1 min-w-0 flex items-center justify-between gap-4">
                    <div class="flex flex-col gap-1">
                        <p class="text-sm font-semibold text-slate-900"><?php echo e($user->phone); ?></p>
                        <?php if($user->phone_verified_at): ?>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                                <i class="fas fa-check-circle"></i> Verified
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600">
                                <i class="fas fa-exclamation-circle"></i> Not Verified
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if(!$user->phone_verified_at): ?>
                        <form method="POST" action="<?php echo e(route('client.profile.send-verification')); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="type" value="phone">
                            <button type="submit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors border border-indigo-100">
                                Verify Now
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5">
                <div class="sm:w-40 shrink-0">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                        <i class="fas fa-shield-halved w-4 text-center text-slate-400" aria-hidden="true"></i>
                        Account Type
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <?php if($user->parent_id): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                            <i class="fas fa-users text-[0.6rem]" aria-hidden="true"></i>
                            Family Member
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                            <i class="fas fa-star text-[0.6rem]" aria-hidden="true"></i>
                            Main Account
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 p-4 sm:p-6 bg-slate-50/40 border-t border-slate-100">
            <a href="<?php echo e(route('client.password.edit')); ?>"
               class="cb-btn cb-btn-ghost w-full sm:w-auto justify-center min-h-[2.75rem] touch-manipulation">
                <i class="fas fa-key text-sm opacity-80" aria-hidden="true"></i>
                Change Password
            </a>
            <a href="<?php echo e(route('client.dashboard')); ?>"
               class="cb-btn cb-btn-gold inline-flex items-center gap-2 w-full sm:w-auto justify-center min-h-[2.75rem] shadow-md touch-manipulation">
                <i class="fas fa-house text-sm opacity-90" aria-hidden="true"></i>
                Go to Dashboard
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/auth/profile.blade.php ENDPATH**/ ?>
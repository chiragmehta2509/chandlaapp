<?php $__env->startSection('title', 'Interest privacy'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full min-w-0 max-w-2xl mx-auto">
    <a href="<?php echo e(route('client.matrimonial.interests.index')); ?>" class="cb-link text-sm inline-flex items-center gap-2 mb-3 sm:mb-4 min-h-[44px] touch-manipulation">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Interests
    </a>
    <h1 class="cb-page-title">Interest privacy</h1>
    <p class="cb-subtitle mt-1 break-words leading-relaxed">Control who can send you interest and manage people you’ve blocked.</p>

    <div class="cb-card p-4 sm:p-5 mt-5 space-y-3">
        <h2 class="text-sm font-bold text-cb-navy">Allow new interest requests</h2>
        <p class="text-sm text-slate-600 break-words">This is the same as the switch on <a href="<?php echo e(route('client.matrimonial.profile.edit')); ?>" class="text-cb-navy font-medium underline">your profile</a>. When it’s off, no one can start a new interest request to you.</p>
        <p class="text-sm">
            <span class="font-medium">Current:</span>
            <?php if($profile?->interests_receiving_enabled ?? true): ?>
                <span class="text-emerald-700">On — you are accepting new requests</span>
            <?php else: ?>
                <span class="text-amber-800">Off — you are not accepting new requests</span>
            <?php endif; ?>
        </p>
    </div>

    <h2 class="text-base sm:text-lg font-bold text-cb-navy mt-8 mb-2.5">Blocked members</h2>
    <p class="text-sm text-slate-600 mb-4 break-words">These members cannot send you interest. You can add someone from a received interest using <strong>Block from sending me interest</strong> on the <a href="<?php echo e(route('client.matrimonial.interests.index')); ?>" class="text-cb-navy underline">Interests</a> page.</p>

    <?php $__empty_1 = true; $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="cb-card p-3.5 sm:p-4 mb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 min-w-0">
            <div class="min-w-0">
                <p class="font-semibold text-slate-900 break-words"><?php echo e($b->blockedUser->matrimonialProfile?->display_name ?? $b->blockedUser->name); ?></p>
                <p class="text-xs text-slate-500 mt-0.5">Blocked <?php echo e($b->created_at->format('d/m/Y')); ?></p>
            </div>
            <form method="post" action="<?php echo e(route('client.matrimonial.interest-blocks.remove', $b->blocked_user_id)); ?>" class="w-full sm:w-auto shrink-0">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full sm:w-auto cb-btn border border-slate-200 cb-btn--sm min-h-[2.75rem] touch-manipulation">Unblock</button>
            </form>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-slate-600 text-sm">Nobody is on your block list yet.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/matrimonial/blocks.blade.php ENDPATH**/ ?>
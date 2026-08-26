<?php $__env->startSection('title', 'Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full min-w-0 max-w-2xl mx-auto">
    <a href="<?php echo e(route('client.matrimonial.index')); ?>" class="cb-link text-sm inline-flex items-center gap-2 mb-3 sm:mb-4 min-h-[44px] touch-manipulation">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back
    </a>

    <div class="cb-card overflow-hidden">
        <div class="relative w-full aspect-[3/4] sm:aspect-[4/5] max-h-[min(62vh,26rem)] sm:max-h-[24rem] bg-slate-100">
            <?php if($p->photo_path): ?>
                <img src="<?php echo e($p->photoUrl()); ?>" alt="" class="h-full w-full object-cover object-top" loading="lazy" decoding="async">
            <?php else: ?>
                <div class="flex h-full min-h-[12rem] items-center justify-center text-slate-400 text-sm">No photo</div>
            <?php endif; ?>
        </div>

        <div class="p-4 sm:p-6 min-w-0">
            <?php if($viewerHasPlan): ?>
                <h1 class="text-xl sm:text-2xl font-bold text-cb-navy break-words"><?php echo e($p->display_name); ?></h1>
                <p class="text-slate-600 mt-1.5 text-sm sm:text-base break-words"><?php echo e($p->age); ?> years · <?php echo e($p->city); ?> · <?php echo e(ucfirst($p->gender)); ?></p>
                <?php if($p->religion || $p->caste): ?>
                    <p class="text-sm text-slate-700 mt-2 break-words">
                        <?php if($p->religion): ?><?php echo e($p->religion); ?><?php endif; ?>
                        <?php if($p->caste): ?> · <?php echo e($p->caste); ?><?php if($p->sub_caste): ?> (<?php echo e($p->sub_caste); ?>)<?php endif; ?> <?php endif; ?>
                    </p>
                <?php endif; ?>
                <dl class="mt-4 space-y-2 text-sm">
                    <?php if($p->education): ?><div class="break-words"><dt class="inline text-slate-500">Education:</dt> <dd class="inline text-slate-800"><?php echo e($p->education); ?></dd></div><?php endif; ?>
                    <?php if($p->profession): ?><div class="break-words"><dt class="inline text-slate-500">Profession:</dt> <dd class="inline text-slate-800"><?php echo e($p->profession); ?></dd></div><?php endif; ?>
                    <?php if($p->income): ?><div class="break-words"><dt class="inline text-slate-500">Income:</dt> <dd class="inline text-slate-800"><?php echo e($p->income); ?></dd></div><?php endif; ?>
                </dl>
                <?php if($p->about_me): ?>
                    <h2 class="mt-5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500">About</h2>
                    <p class="mt-1 text-slate-800 whitespace-pre-line break-words text-sm sm:text-base leading-relaxed"><?php echo e($p->about_me); ?></p>
                <?php endif; ?>
                <?php if($p->family_details): ?>
                    <h2 class="mt-4 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500">Family</h2>
                    <p class="mt-1 text-slate-800 whitespace-pre-line break-words text-sm sm:text-base leading-relaxed"><?php echo e($p->family_details); ?></p>
                <?php endif; ?>
                <?php if($p->partner_preferences): ?>
                    <h2 class="mt-4 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500">Partner preferences</h2>
                    <p class="mt-1 text-slate-800 whitespace-pre-line break-words text-sm sm:text-base leading-relaxed"><?php echo e($p->partner_preferences); ?></p>
                <?php endif; ?>
                <?php if($p->phone_visible_to_matches && $targetUser->phone): ?>
                    <h2 class="mt-4 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500">Contact</h2>
                    <p class="mt-1 text-slate-800 break-all text-sm sm:text-base"><a href="tel:<?php echo e(preg_replace('/\s+/', '', (string) $targetUser->phone)); ?>" class="text-cb-navy underline touch-manipulation"><?php echo e($targetUser->phone); ?></a></p>
                <?php endif; ?>
            <?php else: ?>
                <div class="relative rounded-lg border border-amber-200 bg-amber-50/80 p-3.5 sm:p-4 text-center">
                    <i class="fa-solid fa-lock text-amber-600 text-2xl mb-2" aria-hidden="true"></i>
                    <p class="font-semibold text-slate-800 text-sm sm:text-base">Details are hidden on the free plan</p>
                    <p class="text-sm text-slate-600 mt-1 break-words">Name, age, city, profession, and contact are visible only to paid members.</p>
                    <a href="<?php echo e(route('client.matrimonial.plans')); ?>" class="mt-4 inline-flex w-full sm:w-auto min-h-[2.75rem] items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-slate-900 shadow hover:bg-amber-400 touch-manipulation">Unlock full profiles</a>
                </div>
            <?php endif; ?>

            <?php if($viewerHasPlan): ?>
                <div class="mt-6 border-t border-slate-200 pt-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Interests</p>
                    <?php if($interest): ?>
                        <p class="text-sm text-slate-700 break-words">You sent an interest: <span class="font-medium"><?php echo e($interest->status); ?></span></p>
                    <?php elseif($reverseInterest && $reverseInterest->status === 'pending'): ?>
                        <p class="text-sm text-slate-700 mb-2 break-words">This member sent you an interest (pending in your <a class="text-cb-navy underline font-medium" href="<?php echo e(route('client.matrimonial.interests.index')); ?>">inbox</a>).</p>
                    <?php elseif($canSendInterest): ?>
                        <form method="post" action="<?php echo e(route('client.matrimonial.interests.store')); ?>" class="block sm:inline">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="to_user_id" value="<?php echo e($p->user_id); ?>">
                            <button type="submit" class="cb-btn cb-btn-navy cb-btn--sm w-full sm:w-auto min-h-[2.75rem] touch-manipulation">Send interest</button>
                        </form>
                    <?php else: ?>
                        <p class="text-sm text-slate-600 break-words">
                            <?php if(($interestBlockReason ?? null) === 'not_accepting'): ?>
                                This member is not accepting new interest requests.
                            <?php elseif(($interestBlockReason ?? null) === 'you_are_blocked'): ?>
                                You cannot send interest to this member.
                            <?php else: ?>
                                You cannot send interest to this member right now.
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="mt-4 text-sm text-slate-500">Upgrade to send and accept interests.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/matrimonial/show.blade.php ENDPATH**/ ?>
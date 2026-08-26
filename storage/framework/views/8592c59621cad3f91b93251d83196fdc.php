<?php $__env->startSection('title', 'Find Partner'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full min-w-0 max-w-5xl mx-auto">
    <div class="mb-5 sm:mb-8">
        <h1 class="cb-page-title">Find Partner</h1>
        <p class="cb-subtitle mt-1.5 sm:mt-1 max-w-3xl break-words leading-relaxed">Browse profiles. <span class="text-slate-600">Free:</span> photo only. <span class="text-slate-600">Paid:</span> full details &amp; interests.</p>
        <div class="mt-3 flex flex-col gap-2.5 sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-2 sm:gap-y-1">
            <div class="flex flex-wrap items-center gap-x-2 gap-y-2 text-sm">
                <a href="<?php echo e(route('client.matrimonial.profile.edit')); ?>" class="text-cb-navy font-medium hover:underline py-0.5">Edit my profile</a>
                <span class="text-slate-300 hidden sm:inline" aria-hidden="true">|</span>
                <a href="<?php echo e(route('client.matrimonial.interests.index')); ?>" class="text-cb-navy font-medium hover:underline py-0.5">Interests</a>
                <span class="text-slate-300 hidden sm:inline" aria-hidden="true">|</span>
                <a href="<?php echo e(route('client.matrimonial.interest-privacy')); ?>" class="text-cb-navy font-medium hover:underline py-0.5">Interest privacy</a>
            </div>
            <?php if(!$viewerHasPlan): ?>
                <a href="<?php echo e(route('client.matrimonial.plans')); ?>" class="inline-flex w-full sm:w-auto min-h-[2.75rem] items-center justify-center gap-1.5 rounded-lg bg-amber-500 px-3 py-2 text-sm font-semibold text-slate-900 shadow hover:bg-amber-400 touch-manipulation text-center sm:ml-0">
                    <i class="fa-solid fa-unlock shrink-0" aria-hidden="true"></i> Unlock full profiles
                </a>
            <?php endif; ?>
        </div>
        <?php if($activePlan): ?>
            <p class="mt-2.5 text-sm text-emerald-700 break-words">Your plan is active until <span class="font-semibold"><?php echo e($activePlan->expiry_date->format('d/m/Y')); ?></span>.</p>
        <?php endif; ?>
    </div>

    <form method="get" action="<?php echo e(route('client.matrimonial.index')); ?>" class="cb-card p-3 sm:p-5 mb-5 sm:mb-6">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2 sm:mb-3">Filters</p>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <label class="block min-w-0 col-span-1">
                <span class="text-xs text-slate-600">Age min</span>
                <input type="number" name="age_min" value="<?php echo e(request('age_min')); ?>" min="18" max="99" inputmode="numeric" class="mt-0.5 w-full min-w-0 rounded-lg border border-slate-200 px-2.5 sm:px-3 py-2.5 sm:py-2 text-base sm:text-sm">
            </label>
            <label class="block min-w-0 col-span-1">
                <span class="text-xs text-slate-600">Age max</span>
                <input type="number" name="age_max" value="<?php echo e(request('age_max')); ?>" min="18" max="99" inputmode="numeric" class="mt-0.5 w-full min-w-0 rounded-lg border border-slate-200 px-2.5 sm:px-3 py-2.5 sm:py-2 text-base sm:text-sm">
            </label>
            <label class="block min-w-0 col-span-2 sm:col-span-1">
                <span class="text-xs text-slate-600">City</span>
                <input type="text" name="city" value="<?php echo e(request('city')); ?>" class="mt-0.5 w-full min-w-0 rounded-lg border border-slate-200 px-2.5 sm:px-3 py-2.5 sm:py-2 text-base sm:text-sm" placeholder="Contains" autocapitalize="words" autocomplete="address-level2">
            </label>
            <label class="block min-w-0 col-span-2 sm:col-span-1">
                <span class="text-xs text-slate-600">Caste</span>
                <input type="text" name="caste" value="<?php echo e(request('caste')); ?>" class="mt-0.5 w-full min-w-0 rounded-lg border border-slate-200 px-2.5 sm:px-3 py-2.5 sm:py-2 text-base sm:text-sm" placeholder="Contains">
            </label>
            <label class="block min-w-0 col-span-2">
                <span class="text-xs text-slate-600">Education</span>
                <input type="text" name="education" value="<?php echo e(request('education')); ?>" class="mt-0.5 w-full min-w-0 rounded-lg border border-slate-200 px-2.5 sm:px-3 py-2.5 sm:py-2 text-base sm:text-sm" placeholder="Contains">
            </label>
        </div>
        <div class="mt-3 sm:mt-4 flex flex-col sm:flex-row flex-wrap gap-2">
            <button type="submit" class="cb-btn cb-btn-navy cb-btn--sm w-full sm:w-auto min-h-[2.75rem] touch-manipulation">Apply filters</button>
            <a href="<?php echo e(route('client.matrimonial.index')); ?>" class="cb-btn cb-btn--sm border border-slate-200 w-full sm:w-auto min-h-[2.75rem] justify-center text-center">Clear</a>
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        <?php $__empty_1 = true; $__currentLoopData = $matches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="cb-card overflow-hidden flex flex-col min-w-0">
                <a href="<?php echo e(route('client.matrimonial.profiles.show', $m)); ?>" class="relative block aspect-[3/4] sm:aspect-[4/5] max-h-[min(65vh,22rem)] sm:max-h-none bg-slate-100">
                    <?php if($m->photo_path): ?>
                        <img src="<?php echo e($m->photoUrl()); ?>" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
                    <?php else: ?>
                        <div class="flex h-full min-h-[10rem] items-center justify-center text-slate-400 text-sm">No photo</div>
                    <?php endif; ?>
                    <?php if(!$viewerHasPlan): ?>
                        <div class="absolute inset-0 flex items-end justify-center bg-gradient-to-t from-slate-900/85 via-slate-900/25 to-transparent p-2.5 sm:p-4">
                            <span class="text-[0.7rem] sm:text-xs font-semibold text-white/95 text-center leading-tight"><i class="fa-solid fa-lock mr-0.5"></i> Upgrade to view details</span>
                        </div>
                    <?php endif; ?>
                </a>
                <div class="p-3.5 sm:p-4 flex-1 flex flex-col min-w-0">
                    <?php if($viewerHasPlan): ?>
                        <h2 class="font-bold text-cb-navy text-base sm:text-lg leading-tight break-words"><?php echo e($m->display_name); ?></h2>
                        <p class="text-sm text-slate-600 mt-1 break-words"><?php echo e($m->age); ?> · <?php echo e($m->city); ?></p>
                        <?php if($m->profession): ?>
                            <p class="text-sm text-slate-700 mt-1 break-words line-clamp-2"><?php echo e($m->profession); ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="select-none blur-sm pointer-events-none" aria-hidden="true">
                            <p class="font-bold text-slate-500 text-base sm:text-lg">██████</p>
                            <p class="text-sm text-slate-500 mt-1">██ · ██████</p>
                        </div>
                    <?php endif; ?>
                    <div class="mt-auto pt-3">
                        <a href="<?php echo e(route('client.matrimonial.profiles.show', $m)); ?>" class="text-sm font-medium text-cb-navy hover:underline min-h-[44px] inline-flex items-center touch-manipulation">View profile →</a>
                    </div>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-slate-600 col-span-full text-sm sm:text-base py-2">No profiles match your filters. Try broadening the search.</p>
        <?php endif; ?>
    </div>

    <div class="mt-6 sm:mt-8 w-full min-w-0 overflow-x-auto pb-1 -mx-1 px-1">
        <div class="flex min-w-0 justify-center sm:justify-start py-1">
            <?php echo e($matches->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/matrimonial/index.blade.php ENDPATH**/ ?>
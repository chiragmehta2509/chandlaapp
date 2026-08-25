<?php $__env->startSection('title', 'Find Vendors'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full min-w-0 max-w-5xl mx-auto">
    <div class="mb-5 sm:mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="cb-page-title">Find Vendors</h1>
            <p class="cb-subtitle mt-1.5 sm:mt-1 max-w-3xl break-words leading-relaxed">Browse event service partners for your special occasions.</p>
            <?php if($event): ?>
                <div class="mt-2 text-sm text-slate-500">
                    Showing vendors for: <span class="font-bold text-cb-navy"><?php echo e($event->title); ?></span> 
                    <?php if($event->venue): ?> (<?php echo e($event->venue); ?>) <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <a href="<?php echo e(route('client.vendors.register')); ?>" class="cb-btn cb-btn-gold text-sm min-h-[2.5rem] justify-center text-center">
                <i class="fa-solid fa-plus-circle mr-1"></i> Register Business
            </a>
        </div>
    </div>

    
    <form method="get" action="<?php echo e(route('client.vendors.index')); ?>" class="cb-card p-3 sm:p-5 mb-5 sm:mb-6">
        <input type="hidden" name="event_id" value="<?php echo e(request('event_id')); ?>">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2 sm:mb-3">Filters</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
            <label class="block min-w-0">
                <span class="text-xs text-slate-600">Category</span>
                <select name="category" class="mt-0.5 w-full min-w-0 rounded-lg border border-slate-200 px-2.5 sm:px-3 py-2 text-base sm:text-sm bg-white">
                    <option value="">All Categories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </label>
            <label class="block min-w-0">
                <span class="text-xs text-slate-600">City / Service Area</span>
                <input type="text" name="city" value="<?php echo e(request('city')); ?>" class="mt-0.5 w-full min-w-0 rounded-lg border border-slate-200 px-2.5 sm:px-3 py-2 text-base sm:text-sm" placeholder="Search by city..." autocomplete="address-level2">
            </label>
            <label class="block min-w-0">
                <span class="text-xs text-slate-600">Price Range</span>
                <select name="price_tier" class="mt-0.5 w-full min-w-0 rounded-lg border border-slate-200 px-2.5 sm:px-3 py-2 text-base sm:text-sm bg-white">
                    <option value="">All Prices</option>
                    <option value="budget" <?php echo e(request('price_tier') == 'budget' ? 'selected' : ''); ?>>Budget (₹)</option>
                    <option value="mid" <?php echo e(request('price_tier') == 'mid' ? 'selected' : ''); ?>>Mid-range (₹₹)</option>
                    <option value="premium" <?php echo e(request('price_tier') == 'premium' ? 'selected' : ''); ?>>Premium (₹₹₹)</option>
                </select>
            </label>
        </div>
        <div class="mt-3 sm:mt-4 flex flex-col sm:flex-row flex-wrap gap-2">
            <button type="submit" class="cb-btn cb-btn-navy cb-btn--sm w-full sm:w-auto min-h-[2.75rem] touch-manipulation">Apply Filters</button>
            <a href="<?php echo e(route('client.vendors.index', ['event_id' => request('event_id')])); ?>" class="cb-btn cb-btn--sm border border-slate-200 w-full sm:w-auto min-h-[2.75rem] justify-center text-center">Clear</a>
        </div>
    </form>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        <?php $__empty_1 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="cb-card overflow-hidden flex flex-col min-w-0 relative group hover:shadow-md transition duration-200 border border-slate-200/80">
                
                
                <a href="<?php echo e(route('client.vendors.show', ['vendor' => $v->id, 'event_id' => request('event_id')])); ?>" class="relative block aspect-[4/3] bg-slate-100 overflow-hidden">
                    <?php if($v->portfolioImages->isNotEmpty()): ?>
                        <img src="<?php echo e(asset('storage/' . $v->portfolioImages->first()->image_url)); ?>" alt="<?php echo e($v->business_name); ?>" class="h-full w-full object-cover group-hover:scale-105 transition duration-300" loading="lazy" decoding="async">
                    <?php else: ?>
                        <div class="flex h-full items-center justify-center text-slate-400 text-sm flex-col bg-slate-50 gap-1.5">
                            <i class="fa-solid fa-store text-2xl text-slate-300"></i>
                            <span>No portfolio photos</span>
                        </div>
                    <?php endif; ?>

                    
                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                        <?php if($v->is_featured): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.6rem] font-bold bg-amber-500 text-slate-900 shadow-sm border border-amber-400 uppercase tracking-wider">
                                <i class="fa-solid fa-star mr-1"></i> Featured
                            </span>
                        <?php endif; ?>
                        <?php if($v->is_verified): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.6rem] font-bold bg-emerald-600 text-white shadow-sm border border-emerald-500 uppercase tracking-wider">
                                <i class="fa-solid fa-circle-check mr-1"></i> Verified
                            </span>
                        <?php endif; ?>
                    </div>

                    
                    <div class="absolute bottom-2 right-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-slate-900/75 text-white backdrop-blur-sm">
                            <?php if($v->price_tier === 'budget'): ?>
                                Budget (₹)
                            <?php elseif($v->price_tier === 'mid'): ?>
                                Mid (₹₹)
                            <?php else: ?>
                                Premium (₹₹₹)
                            <?php endif; ?>
                        </span>
                    </div>
                </a>

                
                <div class="p-3.5 sm:p-4 flex-1 flex flex-col min-w-0">
                    <p class="text-[0.65rem] font-bold uppercase tracking-wider text-amber-600 mb-1 leading-none"><?php echo e($v->category->name); ?></p>
                    <h2 class="font-bold text-cb-navy text-base sm:text-lg leading-tight break-words group-hover:text-amber-500 transition duration-150">
                        <a href="<?php echo e(route('client.vendors.show', ['vendor' => $v->id, 'event_id' => request('event_id')])); ?>">
                            <?php echo e($v->business_name); ?>

                        </a>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-map-marker-alt text-slate-400 text-[0.7rem]"></i>
                        <span><?php echo e($v->city); ?></span>
                    </p>
                    
                    <?php if($v->description): ?>
                        <p class="text-xs text-slate-600 mt-2 break-words line-clamp-2 leading-relaxed"><?php echo e($v->description); ?></p>
                    <?php endif; ?>

                    <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between">
                        <a href="<?php echo e(route('client.vendors.show', ['vendor' => $v->id, 'event_id' => request('event_id')])); ?>" class="text-xs font-semibold text-cb-navy hover:underline inline-flex items-center gap-1">
                            View Profile <i class="fa-solid fa-arrow-right text-[0.6rem]"></i>
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full py-12 flex flex-col items-center justify-center text-center text-slate-500 bg-white rounded-2xl border border-slate-150 p-6">
                <i class="fa-solid fa-store-slash text-4xl text-slate-300 mb-3"></i>
                <h3 class="font-bold text-cb-navy text-lg">No vendors found</h3>
                <p class="text-sm mt-1 max-w-sm">No active partners match your search filters. Try adjusting your category or city filter.</p>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="mt-6 sm:mt-8 w-full min-w-0 overflow-x-auto pb-1 -mx-1 px-1">
        <div class="flex min-w-0 justify-center sm:justify-start py-1">
            <?php echo e($vendors->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/vendors/index.blade.php ENDPATH**/ ?>
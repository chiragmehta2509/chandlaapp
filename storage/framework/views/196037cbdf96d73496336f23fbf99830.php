<?php $__env->startSection('title', 'Register Your Business'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full min-w-0 max-w-2xl mx-auto">
    <div class="mb-5 sm:mb-6">
        <a href="<?php echo e(route('client.vendors.index')); ?>" class="cb-link text-sm inline-flex items-center gap-2 mb-3">
            <i class="fas fa-arrow-left"></i> Back to Directory
        </a>
        <h1 class="cb-page-title">Register Your Business</h1>
        <p class="cb-subtitle mt-1.5 sm:mt-1 max-w-3xl leading-relaxed text-slate-600">Grow your business by receiving direct leads from Chandla Book hosts.</p>
    </div>

    <?php if(session('success')): ?>
        <div class="cb-card p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl mb-5 text-sm leading-relaxed flex gap-2">
            <i class="fa-solid fa-circle-check mt-0.5 text-base text-emerald-600"></i>
            <div>
                <p class="font-bold">Success!</p>
                <p><?php echo e(session('success')); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(route('client.vendors.register.submit')); ?>" enctype="multipart/form-data" class="cb-card p-5 sm:p-6 space-y-4">
        <?php echo csrf_field(); ?>

        
        <label class="block min-w-0">
            <span class="text-xs font-semibold text-slate-600">Business / Brand Name *</span>
            <input type="text" name="business_name" required value="<?php echo e(old('business_name')); ?>" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm" placeholder="e.g. Royal Caterers">
            <?php $__errorArgs = ['business_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </label>

        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <label class="block min-w-0">
                <span class="text-xs font-semibold text-slate-600">Service Category *</span>
                <select name="category_id" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm bg-white">
                    <option value="">Select a category</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </label>

            <label class="block min-w-0">
                <span class="text-xs font-semibold text-slate-600">Price Tier *</span>
                <select name="price_tier" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm bg-white">
                    <option value="budget" <?php echo e(old('price_tier') == 'budget' ? 'selected' : ''); ?>>Budget (₹)</option>
                    <option value="mid" <?php echo e(old('price_tier', 'mid') == 'mid' ? 'selected' : ''); ?>>Mid-range (₹₹)</option>
                    <option value="premium" <?php echo e(old('price_tier') == 'premium' ? 'selected' : ''); ?>>Premium (₹₹₹)</option>
                </select>
                <?php $__errorArgs = ['price_tier'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </label>
        </div>

        
        <label class="block min-w-0">
            <span class="text-xs font-semibold text-slate-600">City / Service Area *</span>
            <input type="text" name="city" required value="<?php echo e(old('city')); ?>" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm" placeholder="e.g. Mumbai, Maharashtra">
            <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </label>

        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <label class="block min-w-0">
                <span class="text-xs font-semibold text-slate-600">Contact Number *</span>
                <input type="text" name="phone" required value="<?php echo e(old('phone')); ?>" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm" placeholder="Phone to receive calls">
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </label>

            <label class="block min-w-0">
                <span class="text-xs font-semibold text-slate-600">WhatsApp Number (Optional)</span>
                <input type="text" name="whatsapp" value="<?php echo e(old('whatsapp')); ?>" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm" placeholder="Phone to receive WhatsApp chat">
                <?php $__errorArgs = ['whatsapp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </label>
        </div>

        
        <label class="block min-w-0">
            <span class="text-xs font-semibold text-slate-600">Business Description</span>
            <textarea name="description" rows="5" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm" placeholder="Describe your services, package inclusions, and years of experience..."><?php echo e(old('description')); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </label>

        
        <div class="block min-w-0">
            <span class="text-xs font-semibold text-slate-600">Portfolio Photos (Max 6 images, up to 4MB each)</span>
            <div class="mt-1 p-4 bg-slate-50 border border-slate-200 rounded-lg flex flex-col items-center justify-center text-center">
                <i class="fa-regular fa-images text-2xl text-slate-400 mb-2"></i>
                <input type="file" name="images[]" multiple accept="image/*" class="text-sm text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cb-navy file:text-white hover:file:bg-slate-800 cursor-pointer">
                <p class="text-[0.7rem] text-slate-400 mt-1.5">You can select up to 6 files at once.</p>
            </div>
            <?php $__errorArgs = ['images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="pt-4">
            <button type="submit" class="cb-btn cb-btn-gold w-full justify-center text-base sm:text-sm min-h-[2.75rem] font-bold">
                Submit Registration
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/vendors/register.blade.php ENDPATH**/ ?>
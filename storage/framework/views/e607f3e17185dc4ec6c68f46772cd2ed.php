<?php $__env->startSection('title', 'Edit Plan — ' . $pack->name); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-4xl mx-auto">

    
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="<?php echo e(route('admin.plans.index')); ?>" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-800 transition-colors mb-2">
                <i class="fas fa-arrow-left text-[10px]"></i> Back to Plans
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Edit Subscription Plan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage pricing, description, features, and payment links for <strong><?php echo e($pack->name); ?></strong>.</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1">
            Level <?php echo e($pack->min_level); ?>

        </span>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <form method="POST" action="<?php echo e(route('admin.plans.update', $pack->id)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="space-y-6">

                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Plan Display Name</label>
                        <input type="text" name="name" value="<?php echo e(old('name', $pack->name)); ?>" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Plan Slug (Internal Identifier)</label>
                        <input type="text" value="<?php echo e($pack->slug); ?>" disabled
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                    </div>
                </div>

                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Price (₹ INR)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 font-bold text-sm">₹</span>
                            <input type="number" step="1" min="0" name="amount_inr" value="<?php echo e(old('amount_inr', $pack->amount_inr)); ?>" required
                                class="w-full rounded-xl border border-gray-300 pl-8 pr-4 py-2.5 text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Badge Tag (e.g. Best Value)</label>
                        <input type="text" name="badge" value="<?php echo e(old('badge', $pack->badge)); ?>"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="e.g. Recommended, Best Value">
                    </div>
                    <div class="flex items-center pt-6">
                        <label class="inline-flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="is_popular" value="1" <?php echo e(old('is_popular', $pack->is_popular) ? 'checked' : ''); ?>

                                class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-800">Highlight as "Most Popular" Card</span>
                        </label>
                    </div>
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Description / Tagline</label>
                    <textarea name="description" rows="2"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?php echo e(old('description', $pack->description)); ?></textarea>
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
                        Plan Features (One feature per line)
                    </label>
                    <p class="text-xs text-gray-500 mb-2">These bullet points appear on the website pricing cards and checkout pages.</p>
                    <textarea name="features" rows="6"
                        class="w-full rounded-xl border border-gray-300 p-4 text-sm font-mono leading-relaxed focus:outline-none focus:ring-2 focus:ring-indigo-500"><?php echo e(old('features', is_array($pack->features) ? implode("\n", $pack->features) : '')); ?></textarea>
                </div>

                
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wide mb-3">Custom Razorpay Payment URLs (Optional Override)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Live Payment URL</label>
                            <input type="url" name="live_payment_url" value="<?php echo e(old('live_payment_url', $pack->live_payment_url)); ?>"
                                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="https://rzp.io/rzp/...">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Test Payment URL</label>
                            <input type="url" name="test_payment_url" value="<?php echo e(old('test_payment_url', $pack->test_payment_url)); ?>"
                                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="https://rzp.io/rzp/...">
                        </div>
                    </div>
                </div>

                
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="<?php echo e(route('admin.plans.index')); ?>"
                        class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition-colors">
                        <i class="fas fa-save mr-1.5"></i> Save Changes
                    </button>
                </div>

            </div>
        </form>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/admin/plans/edit.blade.php ENDPATH**/ ?>
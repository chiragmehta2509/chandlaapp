<?php $__env->startSection('title', 'Send Push Notifications'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Send Push Notifications</h1>
            <p class="text-gray-600 mt-1">Broadcast messages to all users or target specific plans.</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-800">Compose Notification</h2>
    </div>
    
    <div class="p-6">
        <form action="<?php echo e(route('admin.notifications.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Title -->
                <div class="col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Special Offer!" value="<?php echo e(old('title')); ?>" required>
                </div>
                
                <!-- Message -->
                <div class="col-span-2">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                    <textarea name="message" id="message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter notification message here..." required><?php echo e(old('message')); ?></textarea>
                </div>

                <!-- Target Audience -->
                <div class="col-span-1">
                    <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-1">Target Audience <span class="text-red-500">*</span></label>
                    <select name="target_audience" id="target_audience" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="all" <?php echo e(old('target_audience') == 'all' ? 'selected' : ''); ?>>All Active Users</option>
                        <option value="plan_wise" <?php echo e(old('target_audience') == 'plan_wise' ? 'selected' : ''); ?>>Plan Wise</option>
                    </select>
                </div>

                <!-- Select Plan -->
                <div class="col-span-1" id="plan_level_container" style="display: none;">
                    <label for="plan_level" class="block text-sm font-medium text-gray-700 mb-1">Select Plan <span class="text-red-500">*</span></label>
                    <select name="plan_level" id="plan_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="0" <?php echo e(old('plan_level') == '0' ? 'selected' : ''); ?>>Free</option>
                        <option value="1" <?php echo e(old('plan_level') == '1' ? 'selected' : ''); ?>>Celebration (₹300)</option>
                        <option value="2" <?php echo e(old('plan_level') == '2' ? 'selected' : ''); ?>>Guest Contribution (₹400)</option>
                        <option value="3" <?php echo e(old('plan_level') == '3' ? 'selected' : ''); ?>>Host Plus / Ledger Duo (₹500)</option>
                        <option value="4" <?php echo e(old('plan_level') == '4' ? 'selected' : ''); ?>>Family Plan (₹600)</option>
                        <option value="5" <?php echo e(old('plan_level') == '5' ? 'selected' : ''); ?>>Premium Host (₹700)</option>
                        <option value="6" <?php echo e(old('plan_level') == '6' ? 'selected' : ''); ?>>Professional (₹999)</option>
                        <option value="7" <?php echo e(old('plan_level') == '7' ? 'selected' : ''); ?>>Enterprise</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Users whose highest active plan matches this selection.</p>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium flex items-center" id="sendBtn">
                    <i class="fas fa-paper-plane mr-2"></i> Send Notification
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const targetAudience = document.getElementById('target_audience');
        const planLevelContainer = document.getElementById('plan_level_container');
        const planLevelSelect = document.getElementById('plan_level');
        const sendBtn = document.getElementById('sendBtn');
        const form = sendBtn.closest('form');

        function togglePlanVisibility() {
            if (targetAudience.value === 'plan_wise') {
                planLevelContainer.style.display = 'block';
                planLevelSelect.required = true;
            } else {
                planLevelContainer.style.display = 'none';
                planLevelSelect.required = false;
            }
        }

        targetAudience.addEventListener('change', togglePlanVisibility);
        
        // Initial check in case of old input
        togglePlanVisibility();

        form.addEventListener('submit', function() {
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';
            sendBtn.disabled = true;
            sendBtn.classList.add('opacity-75', 'cursor-not-allowed');
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/admin/notifications/create.blade.php ENDPATH**/ ?>
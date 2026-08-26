<?php $__env->startSection('title', 'Create Event Type'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('admin.event-types.index')); ?>" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to Event Types
    </a>
    <h1 class="text-3xl font-bold text-gray-800">Create New Event Type</h1>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="<?php echo e(route('admin.event-types.store')); ?>">
        <?php echo csrf_field(); ?>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                   placeholder="e.g., Wedding, Birthday, Anniversary">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      placeholder="Brief description of this event type"><?php echo e(old('description')); ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Icon (Font Awesome class)</label>
                <input type="text" name="icon" value="<?php echo e(old('icon')); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g., fas fa-heart">
                <p class="text-xs text-gray-500 mt-1">Use Font Awesome icon classes</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Color (Hex code)</label>
                <input type="color" name="color" value="<?php echo e(old('color', '#6366f1')); ?>"
                       class="w-full h-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                <input type="number" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
            </div>

            <div class="flex items-center mt-8">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="form-checkbox">
                    <span class="ml-2 text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="<?php echo e(route('admin.event-types.index')); ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Create Event Type
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/admin/event-types/create.blade.php ENDPATH**/ ?>
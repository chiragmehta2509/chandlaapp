<?php $__env->startSection('title', 'User Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('admin.users.index')); ?>" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to Users
    </a>
    <h1 class="text-3xl font-bold text-gray-800">User Details</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center space-x-6 mb-6">
                <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-indigo-600 font-bold text-2xl"><?php echo e(substr($user->name, 0, 1)); ?></span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo e($user->name); ?></h2>
                    <p class="text-gray-600"><?php echo e($user->email); ?></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <p class="text-gray-900"><?php echo e($user->phone ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                        <?php echo e($user->is_active ? 'Active' : 'Inactive'); ?>

                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subscription</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($user->subscription_status === 'premium' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'); ?>">
                        <?php echo e(ucfirst($user->subscription_status ?? 'free')); ?>

                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Joined</label>
                    <p class="text-gray-900"><?php echo e($user->created_at->format('M d, Y')); ?></p>
                </div>
            </div>

            <div class="mt-6">
                <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-block">
                    <i class="fas fa-edit mr-2"></i>Edit User
                </a>
            </div>
        </div>

        <!-- User Events -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Events (<?php echo e($user->events->count()); ?>)</h3>
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $user->events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border-b border-gray-200 pb-4">
                        <h4 class="font-semibold text-gray-900"><?php echo e($event->title); ?></h4>
                        <p class="text-sm text-gray-600"><?php echo e($event->event_date->format('M d, Y')); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500">No events</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Statistics</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Total Events</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo e($user->events->count()); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Contacts</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo e($user->contacts->count()); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Payments</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo e($user->upiTransactions->count()); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/admin/users/show.blade.php ENDPATH**/ ?>
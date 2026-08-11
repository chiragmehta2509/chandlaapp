

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-600 mt-1">Welcome back, <?php echo e(Auth::user()->name); ?>!</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Users</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['total_users']); ?></p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-users text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Active Users</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['active_users']); ?></p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-user-check text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Premium Users</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['premium_users']); ?></p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-crown text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Events</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['total_events']); ?></p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <i class="fas fa-calendar text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Contacts</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['total_contacts']); ?></p>
            </div>
            <div class="bg-indigo-100 p-3 rounded-full">
                <i class="fas fa-address-book text-indigo-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-pink-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Invitations</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['total_invitations']); ?></p>
            </div>
            <div class="bg-pink-100 p-3 rounded-full">
                <i class="fas fa-envelope text-pink-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Payments</p>
                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['total_payments']); ?></p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-credit-card text-red-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-teal-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Revenue</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">₹<?php echo e(number_format($stats['revenue'], 2)); ?></p>
            </div>
            <div class="bg-teal-100 p-3 rounded-full">
                <i class="fas fa-rupee-sign text-teal-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Users -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Recent Users</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $recent_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-600 font-semibold"><?php echo e(substr($user->name, 0, 1)); ?></span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?php echo e($user->name); ?></p>
                            <p class="text-sm text-gray-500 truncate"><?php echo e($user->email); ?></p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                <?php echo e($user->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-center py-4">No users yet</p>
                <?php endif; ?>
            </div>
            <div class="mt-4">
                <a href="<?php echo e(route('admin.users.index')); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View all users →</a>
            </div>
        </div>
    </div>

    <!-- Recent Events -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Recent Events</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $recent_events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-calendar text-purple-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?php echo e($event->title); ?></p>
                            <p class="text-sm text-gray-500 truncate"><?php echo e($event->user->name); ?></p>
                            <p class="text-xs text-gray-400"><?php echo e($event->event_date->format('M d, Y')); ?></p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-center py-4">No events yet</p>
                <?php endif; ?>
            </div>
            <div class="mt-4">
                <a href="<?php echo e(route('admin.events.index')); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View all events →</a>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Recent Payments</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $recent_payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-rupee-sign text-green-600"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?php echo e($payment->user->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($payment->created_at->format('M d, Y')); ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">₹<?php echo e(number_format($payment->amount, 2)); ?></p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                Completed
                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-center py-4">No payments yet</p>
                <?php endif; ?>
            </div>
            <div class="mt-4">
                <a href="<?php echo e(route('admin.payments.index')); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View all payments →</a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>
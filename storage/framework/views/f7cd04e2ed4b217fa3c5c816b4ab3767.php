

<?php $__env->startSection('title', 'Chandlas Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Chandlas Management</h1>
    <p class="text-gray-600 mt-1">Manage all chandlas in the system</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Total Records</p>
        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['total']); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Total Amount</p>
        <p class="text-3xl font-bold text-indigo-600 mt-2">₹<?php echo e(number_format($stats['total_amount'], 2)); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Cash Count</p>
        <p class="text-3xl font-bold text-green-600 mt-2"><?php echo e($stats['chandla_count']); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Cover + Gift</p>
        <p class="text-3xl font-bold text-purple-600 mt-2"><?php echo e($stats['cover_count'] + $stats['gift_count']); ?></p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="<?php echo e(route('admin.chandlas.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <select name="event_id" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Events</option>
            <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($event->id); ?>" <?php echo e(request('event_id') == $event->id ? 'selected' : ''); ?>>
                    <?php echo e($event->title); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="category" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Categories</option>
            <option value="chandla" <?php echo e(request('category') === 'chandla' ? 'selected' : ''); ?>>Cash</option>
            <option value="cover" <?php echo e(request('category') === 'cover' ? 'selected' : ''); ?>>Cover</option>
            <option value="gift" <?php echo e(request('category') === 'gift' ? 'selected' : ''); ?>>Gift</option>
        </select>
        <select name="payment_method" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Payment Methods</option>
            <option value="gpay" <?php echo e(request('payment_method') === 'gpay' ? 'selected' : ''); ?>>GPay</option>
            <option value="cash" <?php echo e(request('payment_method') === 'cash' ? 'selected' : ''); ?>>Cash</option>
            <option value="other" <?php echo e(request('payment_method') === 'other' ? 'selected' : ''); ?>>N/A</option>
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            <i class="fas fa-search mr-2"></i>Filter
        </button>
    </form>
</div>

<!-- Chandlas Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $chandlas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chandla): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e($chandla->received_date->format('M d, Y')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($chandla->event->title); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo e($chandla->giver_name); ?></div>
                            <div class="text-sm text-gray-500"><?php echo e($chandla->giver_phone); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <?php echo e($chandla->category_label); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <?php echo e($chandla->payment_method_label); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            ₹<?php echo e(number_format($chandla->amount, 2)); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($chandla->user->name); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?php echo e(route('admin.chandlas.show', $chandla->id)); ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if(!$chandla->is_verified): ?>
                                <form action="<?php echo e(route('admin.chandlas.verify', $chandla->id)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="text-green-600 hover:text-green-900 mr-3" title="Verify">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            <form action="<?php echo e(route('admin.chandlas.destroy', $chandla->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No chandlas found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="bg-gray-50 px-6 py-4">
        <?php echo e($chandlas->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/admin/chandlas/index.blade.php ENDPATH**/ ?>
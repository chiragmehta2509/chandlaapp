

<?php $__env->startSection('title', 'Event Summary Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Event Summary</h1>
    <p class="text-gray-600 mt-1">User and event-wise transaction totals</p>
</div>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form method="GET" action="<?php echo e(route('admin.reports.events-summary')); ?>" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select name="event_id" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Events</option>
                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($event->id); ?>" <?php echo e(request('event_id') == $event->id ? 'selected' : ''); ?>>
                        <?php echo e($event->title); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="user_id" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Users</option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                        <?php echo e($user->name); ?> (<?php echo e($user->email); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" placeholder="Start Date"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" placeholder="End Date"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                <i class="fas fa-search mr-2"></i>Generate
            </button>
        </div>
    </form>
</div>

<?php
    $totalTransactions = $summaries->sum('chandla_count');
    $totalAmount = $summaries->sum('total_amount');
    $totalFees = $summaries->sum('total_fee');
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <p class="text-gray-600 text-sm font-medium">Total Transactions</p>
        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($totalTransactions); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <p class="text-gray-600 text-sm font-medium">Total Amount</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">₹<?php echo e(number_format($totalAmount, 2)); ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <p class="text-gray-600 text-sm font-medium">Total Fees (Payg + Unlimited)</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">₹<?php echo e(number_format($totalFees, 2)); ?></p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-800">Event-wise Summary</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fees</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $summaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e($summary['event']->title); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e($summary['user']->name ?? 'N/A'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e($summary['chandla_count']); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            ₹<?php echo e(number_format($summary['total_amount'], 2)); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php if($summary['plan'] === 'unlimited'): ?>
                                Unlimited
                            <?php elseif($summary['plan'] === 'payg'): ?>
                                Pay-as-you-go
                            <?php else: ?>
                                Free
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₹<?php echo e(number_format($summary['total_fee'], 2)); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No events found for the selected filters</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/admin/reports/event-summary.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'Cash Inventory'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('client.events.show', $event->id)); ?>" class="cb-link mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to Event
    </a>
    <h1 class="text-3xl font-bold text-gray-800">Cash Inventory - <?php echo e($event->title); ?></h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Current Notes & Coins</h2>
            <form method="POST" action="<?php echo e(route('client.cash-inventory.update', $event->id)); ?>">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <?php $__currentLoopData = [1,2,5,10,20,50,100,200,500]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $denomination): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">₹<?php echo e($denomination); ?></label>
                            <input type="number" name="note_<?php echo e($denomination); ?>" min="0"
                                   value="<?php echo e(old('note_' . $denomination, $inventory->{'note_' . $denomination})); ?>"
                                   class="cb-field">
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="cb-btn cb-btn-gold">
                        Update Inventory
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Pending Change</h2>
            <?php if($pendingChanges->count() > 0): ?>
                <div class="space-y-4">
                    <?php $__currentLoopData = $pendingChanges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chandla): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm text-gray-500"><?php echo e($chandla->received_date->format('d/m/Y')); ?></div>
                            <div class="text-gray-900 font-medium"><?php echo e($chandla->giver_name); ?></div>
                            <div class="text-sm text-gray-700">
                                Change Due: ₹<?php echo e(number_format($chandla->change_amount, 2)); ?>

                            </div>
                            <a href="<?php echo e(route('client.chandlas.show', $chandla->id)); ?>" class="cb-link text-sm">
                                View Chandla
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">No pending change for this event.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/cash-inventory/show.blade.php ENDPATH**/ ?>
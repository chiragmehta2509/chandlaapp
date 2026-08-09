

<?php $__env->startSection('title', 'Chandla Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('client.chandlas.index')); ?>" class="cb-link mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to Chandlas
    </a>
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
        <h1 class="text-3xl font-bold text-gray-800">Chandla Details</h1>
        <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
            <a href="<?php echo e(route('client.chandlas.create', ['event_id' => $chandla->event_id])); ?>" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-center">
                <i class="fas fa-plus mr-2"></i>Add another for this event
            </a>
            <a href="<?php echo e(route('client.chandlas.edit', $chandla->id)); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-center">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Giver Information</h2>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <p class="text-gray-900"><?php echo e($chandla->giver_name); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <p class="text-gray-900"><?php echo e($chandla->giver_phone ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <p class="text-gray-900"><?php echo e($chandla->giver_email ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <p class="text-gray-900"><?php echo e($chandla->giver_address ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Chandla Information</h2>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Event</label>
                    <p class="text-gray-900"><?php echo e($chandla->event->title); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Received Date & Time</label>
                    <p class="text-gray-900">
                        <?php echo e($chandla->received_date->format('d/m/Y')); ?>

                        <?php if($chandla->created_at): ?>
                            <?php echo e($chandla->created_at->timezone(config('app.timezone'))->format('h:i A')); ?>

                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <?php echo e($chandla->category_label); ?>

                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <?php echo e($chandla->payment_method_label); ?>

                    </span>
                </div>
                <?php if($chandla->category === 'chandla'): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                    <p class="text-gray-900 text-2xl font-bold">₹<?php echo e(number_format($chandla->amount, 2)); ?></p>
                </div>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Receipt Number</label>
                    <p class="text-gray-900"><?php echo e($chandla->receipt_number ?? 'N/A'); ?></p>
                </div>
                <?php if($chandla->category === 'gift' && $chandla->gift_item_name): ?>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gift Item</label>
                    <p class="text-gray-900"><?php echo e($chandla->gift_item_name); ?></p>
                </div>
                <?php endif; ?>
                <?php if($chandla->category === 'gift'): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gift Given</label>
                    <p class="text-gray-900">
                        <?php echo e($chandla->gift_received === null ? 'N/A' : ($chandla->gift_received ? 'Yes' : 'No')); ?>

                    </p>
                </div>
                <?php endif; ?>
                <?php if($chandla->description): ?>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <p class="text-gray-900"><?php echo e($chandla->description); ?></p>
                </div>
                <?php endif; ?>
                <?php if($chandla->category === 'chandla' && $chandla->payment_method === 'cash'): ?>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cash Notes</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm text-gray-900">
                        <div>₹1: <?php echo e($chandla->cash_note_1); ?></div>
                        <div>₹2: <?php echo e($chandla->cash_note_2); ?></div>
                        <div>₹5: <?php echo e($chandla->cash_note_5); ?></div>
                        <div>₹10: <?php echo e($chandla->cash_note_10); ?></div>
                        <div>₹20: <?php echo e($chandla->cash_note_20); ?></div>
                        <div>₹50: <?php echo e($chandla->cash_note_50); ?></div>
                        <div>₹100: <?php echo e($chandla->cash_note_100); ?></div>
                        <div>₹200: <?php echo e($chandla->cash_note_200); ?></div>
                        <div>₹500: <?php echo e($chandla->cash_note_500); ?></div>
                    </div>
                    <?php
                        $receivedTotal = ($chandla->cash_note_1 * 1)
                            + ($chandla->cash_note_2 * 2)
                            + ($chandla->cash_note_5 * 5)
                            + ($chandla->cash_note_10 * 10)
                            + ($chandla->cash_note_20 * 20)
                            + ($chandla->cash_note_50 * 50)
                            + ($chandla->cash_note_100 * 100)
                            + ($chandla->cash_note_200 * 200)
                            + ($chandla->cash_note_500 * 500);
                    ?>
                    <div class="mt-4 space-y-1 text-sm text-gray-900">
                        <div>Received Total: ₹<?php echo e(number_format($receivedTotal, 2)); ?></div>
                        <div>Change Due: ₹<?php echo e(number_format($chandla->change_amount, 2)); ?></div>
                        <div>Change Status: <?php echo e($chandla->change_status ? ucfirst($chandla->change_status) : 'N/A'); ?></div>
                    </div>
                    <?php if($chandla->change_status === 'returned' && $chandla->change_amount > 0): ?>
                    <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-2 text-sm text-gray-900">
                        <div>₹1: <?php echo e($chandla->change_note_1); ?></div>
                        <div>₹2: <?php echo e($chandla->change_note_2); ?></div>
                        <div>₹5: <?php echo e($chandla->change_note_5); ?></div>
                        <div>₹10: <?php echo e($chandla->change_note_10); ?></div>
                        <div>₹20: <?php echo e($chandla->change_note_20); ?></div>
                        <div>₹50: <?php echo e($chandla->change_note_50); ?></div>
                        <div>₹100: <?php echo e($chandla->change_note_100); ?></div>
                        <div>₹200: <?php echo e($chandla->change_note_200); ?></div>
                        <div>₹500: <?php echo e($chandla->change_note_500); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if($chandla->notes): ?>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <p class="text-gray-900"><?php echo e($chandla->notes); ?></p>
                </div>
                <?php endif; ?>
                <?php if($chandla->gpay_transaction_id): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">GPay Transaction ID</label>
                    <p class="text-gray-900"><?php echo e($chandla->gpay_transaction_id); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if($chandla->gpay_image): ?>
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">GPay Screenshot</h2>
            <div class="flex justify-center">
                <a href="<?php echo e(route('client.gpay.view-image', $chandla->id)); ?>" target="_blank" class="inline-block">
                    <img src="<?php echo e(route('client.gpay.view-image', $chandla->id)); ?>" 
                         alt="GPay Screenshot" 
                         class="max-w-full h-auto rounded-lg border border-gray-300 shadow-lg cursor-pointer hover:opacity-90 transition-opacity"
                         style="max-height: 600px;">
                </a>
            </div>
            <p class="text-sm text-gray-500 text-center mt-2">Click image to view full size</p>
        </div>
        <?php endif; ?>
    </div>

    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Info</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Created</p>
                    <p class="text-sm font-medium text-gray-900"><?php echo e($chandla->created_at->timezone(config('app.timezone'))->format('d/m/Y h:i A')); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="text-sm font-medium text-gray-900"><?php echo e($chandla->updated_at->timezone(config('app.timezone'))->format('d/m/Y h:i A')); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/chandlas/show.blade.php ENDPATH**/ ?>
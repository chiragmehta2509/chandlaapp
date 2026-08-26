<?php $__env->startSection('title', 'Payment Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('admin.payments.index', ['tab' => $isRazorpay ? 'razorpay' : 'manual'])); ?>" class="inline-flex items-center text-sm font-semibold text-amber-600 hover:text-amber-800 transition-colors mb-4">
        <i class="fas fa-arrow-left mr-2"></i>Back to Payments
    </a>
    <h1 class="text-3xl font-bold text-gray-800 font-display">Payment Details</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden p-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-5">
            <div>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 mb-2">
                    <i class="fas <?php echo e($isRazorpay ? 'fa-credit-card' : 'fa-qrcode'); ?> text-[9px]"></i>
                    <?php echo e($isRazorpay ? 'Razorpay Gateway Transaction' : 'Manual UPI Submission'); ?>

                </span>
                <h2 class="text-xl font-bold text-slate-800">
                    <?php echo e($isRazorpay ? $payment->package_name : 'Manual UPI: ' . $payment->transaction_id); ?>

                </h2>
                <p class="text-xs text-slate-400 font-mono mt-1">
                    <?php echo e($isRazorpay ? 'TXN Number: ' . $payment->transaction_number : 'UPI Txn Ref: ' . $payment->transaction_id); ?>

                </p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider 
                    <?php if($isRazorpay): ?>
                        <?php echo e($payment->statusBadgeClass()); ?>

                    <?php else: ?>
                        <?php echo e($payment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                           ($payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')); ?>

                    <?php endif; ?>">
                    <?php if($isRazorpay): ?>
                        <?php echo e($payment->statusLabel()); ?>

                    <?php else: ?>
                        <?php echo e(ucfirst($payment->status)); ?>

                    <?php endif; ?>
                </span>
                <div class="text-2xl font-black text-slate-900 mt-2">
                    ₹<?php echo e(number_format($isRazorpay ? $payment->amount_inr : $payment->amount, 2)); ?>

                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Created At</label>
                <p class="text-sm font-semibold text-slate-700"><?php echo e($payment->created_at->format('M d, Y · h:i A')); ?></p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Paid At</label>
                <p class="text-sm font-semibold text-slate-700">
                    <?php if($isRazorpay): ?>
                        <?php echo e($payment->paid_at ? $payment->paid_at->format('M d, Y · h:i A') : '—'); ?>

                    <?php else: ?>
                        <?php echo e($payment->paid_at ? $payment->paid_at->format('M d, Y · h:i A') : '—'); ?>

                    <?php endif; ?>
                </p>
            </div>
            <?php if($isRazorpay): ?>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Razorpay Order ID</label>
                    <p class="text-sm font-mono text-slate-700"><?php echo e($payment->razorpay_order_id ?: '—'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Razorpay Payment ID</label>
                    <p class="text-sm font-mono text-slate-700"><?php echo e($payment->razorpay_payment_id ?: '—'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Payment Method</label>
                    <p class="text-sm font-semibold text-slate-700 uppercase"><?php echo e($payment->payment_method ?: '—'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Package Key</label>
                    <p class="text-sm font-semibold text-slate-700 font-mono"><?php echo e($payment->package_key); ?></p>
                </div>
                <?php if($payment->reference_id): ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Reference ID / Entity</label>
                        <p class="text-sm font-mono text-slate-700"><?php echo e($payment->reference_id); ?></p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Razorpay Order ID (Linked)</label>
                    <p class="text-sm font-mono text-slate-700"><?php echo e($payment->razorpay_order_id ?? '—'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Payment Method</label>
                    <p class="text-sm font-semibold text-slate-700 uppercase"><?php echo e($payment->payment_method ?? 'UPI'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Payment Type (Metadata)</label>
                    <p class="text-sm font-semibold text-slate-700"><?php echo e($payment->metadata['type'] ?? '—'); ?></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Plan / Detail</label>
                    <p class="text-sm font-semibold text-slate-700">
                        <?php if(data_get($payment->metadata, 'type') === 'direct_gpay_unlock'): ?>
                            Direct GPay Event Unlock
                        <?php elseif(data_get($payment->metadata, 'type') === 'matrimonial_plan'): ?>
                            Find Partner Plan: <?php echo e(data_get($payment->metadata, 'matrimonial_plan')); ?>

                        <?php else: ?>
                            <?php echo e(data_get($payment->metadata, 'plan') ?? (($invId = data_get($payment->metadata, 'marriage_invitation_id')) ? 'Marriage card #'.$invId : 'N/A')); ?>

                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php if($isRazorpay && $payment->failure_reason): ?>
            <div class="mt-6 rounded-xl border border-rose-100 bg-rose-50/50 p-4">
                <p class="text-xs font-bold text-rose-800 uppercase tracking-wider">Failure Reason</p>
                <p class="text-sm text-rose-900 mt-1 font-medium"><?php echo e($payment->failure_reason); ?></p>
            </div>
        <?php endif; ?>

        
        <?php if(!$isRazorpay && $payment->status === 'pending'): ?>
            <div class="mt-8 border-t border-slate-100 pt-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Admin Actions Required</h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Verify the UPI transaction in your bank account / GPay statement before approving.
                </p>
                <div class="flex gap-3">
                    <form method="POST" action="<?php echo e(route('admin.payments.verify', $payment->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors">
                            <i class="fas fa-check mr-2"></i> Approve & Activate
                        </button>
                    </form>
                    <form method="POST" action="<?php echo e(route('admin.payments.verify', $payment->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="status" value="failed">
                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors">
                            <i class="fas fa-times mr-2"></i> Reject / Fail
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 self-start">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Customer Details</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-400">Name</label>
                <p class="text-sm font-bold text-slate-800 mt-0.5"><?php echo e($payment->user->name ?? 'Deleted User'); ?></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400">Phone</label>
                <p class="text-sm font-semibold text-slate-800 mt-0.5"><?php echo e($payment->user->phone ?? '—'); ?></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400">Email</label>
                <p class="text-sm font-semibold text-slate-800 mt-0.5"><?php echo e($payment->user->email ?? '—'); ?></p>
            </div>
            <?php if(!$isRazorpay && $payment->event): ?>
                <div class="border-t border-slate-100 pt-4">
                    <label class="block text-xs font-semibold text-slate-400">Linked Event</label>
                    <p class="text-sm font-bold text-slate-800 mt-0.5"><?php echo e($payment->event->title); ?></p>
                    <p class="text-xs text-slate-400 mt-0.5">ID: <?php echo e($payment->event->id); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php if($isRazorpay && ($payment->gateway_response || $payment->metadata)): ?>
    <div class="mt-6 bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 overflow-hidden">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4"><i class="fas fa-code mr-1.5"></i> Gateway Responses / Metadata</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if($payment->metadata): ?>
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-2">Metadata</p>
                    <pre class="bg-slate-900 text-slate-200 text-xs rounded-xl p-4 overflow-x-auto font-mono max-h-96"><?php echo e(json_encode($payment->metadata, JSON_PRETTY_PRINT)); ?></pre>
                </div>
            <?php endif; ?>
            <?php if($payment->gateway_response): ?>
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-2">Gateway Response</p>
                    <pre class="bg-slate-900 text-slate-200 text-xs rounded-xl p-4 overflow-x-auto font-mono max-h-96"><?php echo e(json_encode($payment->gateway_response, JSON_PRETTY_PRINT)); ?></pre>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/admin/payments/show.blade.php ENDPATH**/ ?>
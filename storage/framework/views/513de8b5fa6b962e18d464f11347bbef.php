<?php $__env->startSection('title', 'Pay for invitation card'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('client.marriage-invitations.show', $invitation->id)); ?>" class="cb-link text-sm inline-flex items-center gap-2 mb-2">← Back to card</a>
    <h1 class="cb-page-title">Pay ₹<?php echo e(number_format($amount, 0)); ?></h1>
    <p class="cb-subtitle max-w-2xl">Pay securely with <strong>Razorpay</strong> — your invitation unlocks instantly after payment is verified. No admin confirmation needed.</p>
</div>

<?php if($errors->has('pay')): ?>
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><?php echo e($errors->first('pay')); ?></div>
<?php endif; ?>


<div class="cb-card p-6 sm:p-8 mb-8 border-2 border-emerald-200/80 bg-gradient-to-br from-emerald-50/90 via-white to-white max-w-2xl">
    <p class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-2">Recommended · Instant Unlock</p>
    <h2 class="text-xl font-bold text-cb-navy mb-2">Pay ₹<?php echo e(number_format($amount, 0)); ?> with Razorpay</h2>
    <p class="text-sm text-slate-600 mb-6 leading-relaxed">Opens Razorpay's secure checkout popup — pay by UPI, card, or netbanking. Invitation unlocks automatically on success.</p>

    <button id="rzp-pay-btn" type="button"
        class="cb-btn cb-btn-gold w-full sm:w-auto justify-center px-8 py-3.5 text-base shadow-lg ring-1 ring-amber-400/40">
        <i class="fas fa-lock-open text-xs mr-2" aria-hidden="true"></i>
        Pay ₹<?php echo e(number_format($amount, 0)); ?> — Unlock Now
    </button>
    <p class="text-xs text-slate-500 mt-4">Powered by Razorpay · SSL secured</p>
</div>


<form id="rzp-verify-form" method="POST"
    action="<?php echo e(route('client.marriage-invitations.payment.razorpay.verify', $invitation->id)); ?>"
    style="display:none;">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="razorpay_order_id" id="rzp_order_id">
    <input type="hidden" name="razorpay_payment_id" id="rzp_payment_id">
    <input type="hidden" name="razorpay_signature" id="rzp_signature">
</form>

<p class="text-sm font-semibold text-slate-700 mb-4 max-w-2xl mt-4">Manual UPI (slower)</p>
<p class="text-sm text-slate-600 mb-6 max-w-2xl">Pay from your bank app and submit the reference below — our team verifies before unlock.</p>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-5xl">
    <div class="cb-card p-5 sm:p-6">
        <h2 class="text-lg font-bold text-cb-navy mb-4">UPI QR</h2>
        <p class="text-sm text-slate-600 mb-2">Amount</p>
        <p class="text-2xl font-bold text-cb-navy">₹<?php echo e(number_format($amount, 2)); ?></p>
        <div class="mt-4">
            <?php if($qrSvg): ?>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 inline-block">
                    <?php echo $qrSvg; ?>

                </div>
                <p class="text-xs text-slate-500 mt-2">UPI: <?php echo e($upiId); ?></p>
            <?php else: ?>
                <div class="rounded-lg border border-amber-200 bg-amber-50 text-amber-900 px-4 py-3 text-sm">
                    Set <code class="bg-white/80 px-1 rounded">UPI_ID</code> in <code class="bg-white/80 px-1 rounded">.env</code>.
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="cb-card p-5 sm:p-6">
        <h2 class="text-lg font-bold text-cb-navy mb-4">Submit transaction ID</h2>
        <form method="POST" action="<?php echo e(route('client.marriage-invitations.payment.store', $invitation->id)); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="cb-label cb-label--classic block mb-1" for="transaction_id">UPI reference / transaction ID</label>
                <input type="text" name="transaction_id" id="transaction_id" value="<?php echo e(old('transaction_id')); ?>" class="cb-field" required placeholder="From bank or GPay">
            </div>
            <button type="submit" class="cb-btn cb-btn-navy w-full justify-center py-3">Submit for verification</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('rzp-pay-btn').addEventListener('click', function () {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Opening checkout…';

    fetch('<?php echo e(route('client.marriage-invitations.payment.razorpay.order', $invitation->id)); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(res => res.json())
    .then(data => {
        if (data.message && !data.order_id) {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock-open text-xs mr-2"></i> Pay ₹<?php echo e(number_format($amount, 0)); ?> — Unlock Now';
            return;
        }

        const options = {
            key: data.key_id,
            amount: data.amount,
            currency: 'INR',
            name: data.name,
            description: data.description,
            order_id: data.order_id,
            prefill: {
                email: data.prefill?.email ?? '',
            },
            theme: { color: '#d97706' },
            handler: function (response) {
                document.getElementById('rzp_order_id').value = response.razorpay_order_id;
                document.getElementById('rzp_payment_id').value = response.razorpay_payment_id;
                document.getElementById('rzp_signature').value = response.razorpay_signature;
                document.getElementById('rzp-verify-form').submit();
            },
            modal: {
                ondismiss: function () {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-lock-open text-xs mr-2"></i> Pay ₹<?php echo e(number_format($amount, 0)); ?> — Unlock Now';
                }
            }
        };

        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (resp) {
            alert('Payment failed: ' + (resp.error?.description ?? 'Unknown error'));
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock-open text-xs mr-2"></i> Pay ₹<?php echo e(number_format($amount, 0)); ?> — Unlock Now';
        });
        rzp.open();
    })
    .catch(err => {
        alert('Could not initiate payment. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-lock-open text-xs mr-2"></i> Pay ₹<?php echo e(number_format($amount, 0)); ?> — Unlock Now';
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/marriage-invitations/payment.blade.php ENDPATH**/ ?>
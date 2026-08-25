<?php $__env->startSection('title', 'Plan Payment'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full min-w-0 max-w-4xl mx-auto">
    <a href="<?php echo e(route('client.events.show', $event->id)); ?>" class="cb-link text-sm inline-flex items-center gap-2 mb-4">
        <i class="fas fa-arrow-left mr-2"></i>Back to Event
    </a>
    <h1 class="cb-page-title">Unlimited Plan Payment</h1>
    <p class="cb-subtitle mt-1">Pay <strong>₹<?php echo e(number_format($amount, 0)); ?></strong> for <strong><?php echo e($event->title); ?></strong> (default unlimited price is ₹<?php echo e(number_format((float) config('services.direct_gpay_unlock.amount', 400), 0)); ?> unless changed on the event).</p>

    <?php if(!empty($keyId)): ?>
        <div class="cb-card p-5 sm:p-6 mt-6 mb-6">
            <h2 class="text-lg font-bold text-cb-navy mb-2">Pay with Razorpay</h2>
            <p class="text-sm text-slate-600 mb-4">Secure card, UPI, netbanking, or wallets. Your event upgrades as soon as payment is confirmed.</p>
            <button type="button" id="rzp-event-plan-btn" class="cb-btn cb-btn-navy w-full sm:w-auto min-h-[2.75rem] touch-manipulation">
                Pay ₹<?php echo e(number_format($amount, 0)); ?> with Razorpay
            </button>
        </div>

        <form id="rzp-event-verify-form" method="POST" action="<?php echo e(route('client.events.plan.razorpay.verify', $event->id)); ?>" class="hidden">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="razorpay_order_id" id="rzp-oid" value="">
            <input type="hidden" name="razorpay_payment_id" id="rzp-pid" value="">
            <input type="hidden" name="razorpay_signature" id="rzp-sig" value="">
        </form>
    <?php else: ?>
        <div class="mt-4 cb-alert cb-alert--error" role="alert">Razorpay is not configured. Add <code class="text-xs">RAZORPAY_KEY_ID</code> and <code class="text-xs">RAZORPAY_KEY_SECRET</code> to <code class="text-xs">.env</code> to enable online payment.</div>
    <?php endif; ?>

    
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php if(!empty($keyId)): ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function () {
    var orderUrl = <?php echo json_encode(route('client.events.plan.razorpay.order', $event->id), 512) ?>;
    var token = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    var name = <?php echo json_encode(Auth::user()->name ?? 'Organizer', 15, 512) ?>;
    var email = <?php echo json_encode(Auth::user()->email ?? '', 15, 512) ?>;
    var btn = document.getElementById('rzp-event-plan-btn');
    if (!btn) return;
    var originalHtml = btn.innerHTML;
    btn.addEventListener('click', function () {
        btn.disabled = true;
        btn.setAttribute('aria-busy', 'true');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Opening checkout…';
        fetch(orderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({}),
        })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
        .then(function (res) {
            if (!res.ok) {
                btn.disabled = false;
                btn.removeAttribute('aria-busy');
                btn.innerHTML = originalHtml;
                alert(res.j.message || 'Could not start payment.');
                return;
            }
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
            btn.innerHTML = originalHtml;
            var opt = {
                key: res.j.key_id,
                order_id: res.j.order_id,
                amount: res.j.amount,
                currency: 'INR',
                name: 'Chandla Book',
                description: 'Event unlimited plan — #<?php echo e($event->id); ?>',
                prefill: { name: name, email: email },
                theme: { color: '#1A3646' },
                handler: function (response) {
                    document.getElementById('rzp-oid').value = response.razorpay_order_id;
                    document.getElementById('rzp-pid').value = response.razorpay_payment_id;
                    document.getElementById('rzp-sig').value = response.razorpay_signature;
                    document.getElementById('rzp-event-verify-form').submit();
                },
            };
            var rzp = new Razorpay(opt);
            rzp.open();
        })
        .catch(function () {
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
            btn.innerHTML = originalHtml;
            alert('Network error. Try again.');
        });
    });
})();
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/events/plan-payment.blade.php ENDPATH**/ ?>
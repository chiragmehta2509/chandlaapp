<?php $__env->startSection('title', 'Unlock Direct GPay — ' . $event->title); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6 pb-8">
    
    <div>
        <a href="<?php echo e(route('client.events.index')); ?>" class="cb-link text-sm inline-flex items-center gap-2 mb-3">
            <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i> Back to events
        </a>
        <h1 class="cb-page-title">Direct GPay for this event</h1>
        <div class="mt-2 rounded-xl bg-slate-50 border border-slate-150 p-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Event name</p>
            <p class="text-base font-extrabold text-cb-navy mt-0.5"><?php echo e($event->title); ?></p>
        </div>
        <p class="cb-subtitle mt-4">
            Unlock direct guest payments for this event. Guests can pay any amount directly to your personal UPI/GPay account via a generated link or QR code.
        </p>
    </div>

    
    <?php if($errors->has('pack')): ?>
        <div class="cb-alert cb-alert--error" role="status"><?php echo e($errors->first('pack')); ?></div>
    <?php endif; ?>

    
    <div class="cb-card p-5 sm:p-6 border border-slate-200/90 bg-white rounded-2xl shadow-sm">
        <h3 class="text-sm font-bold text-cb-navy uppercase tracking-wide mb-3">What's included in this unlock:</h3>
        <ul class="space-y-2.5 text-sm text-slate-600">
            <li class="flex items-start gap-2.5">
                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-xs mt-0.5">
                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                </span>
                <span><strong>Direct UPI / GPay Integration</strong>: Show your QR code and UPI ID directly to guests on their invitation cards. Payees redirect directly to their payment apps.</span>
            </li>
            <li class="flex items-start gap-2.5">
                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-xs mt-0.5">
                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                </span>
                <span><strong>Zero Commission Fees</strong>: 100% of the guest gifts go directly to your bank account instantly. No processing fees or hold-ups.</span>
            </li>
            <li class="flex items-start gap-2.5">
                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-xs mt-0.5">
                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                </span>
                <span><strong>Unlimited Ledger Entries</strong>: Log unlimited guest contributions for this event (free tier is capped at 50).</span>
            </li>
            <li class="flex items-start gap-2.5">
                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-xs mt-0.5">
                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                </span>
                <span><strong>PDF Ledger Report</strong>: Generate and download a print-ready PDF containing all guest contributions and balances.</span>
            </li>
        </ul>
    </div>

    
    <div class="grid grid-cols-1 gap-6">
        
        <?php if(($guestPayPackCredits ?? 0) > 0): ?>
            <div class="cb-card p-5 sm:p-6 border-2 border-amber-200 bg-amber-50/80 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    <h2 class="text-base font-bold text-cb-navy mb-1">Use Guest Pay Credit</h2>
                    <p class="text-xs text-slate-700">You have <strong><?php echo e((int) $guestPayPackCredits); ?></strong> unused credit<?php echo e((int) $guestPayPackCredits === 1 ? '' : 's'); ?> in your account. Applying this credit will unlock this event instantly.</p>
                </div>
                <form method="POST" action="<?php echo e(route('client.events.direct-gpay-unlock.redeem-guest-pay-pack', $event)); ?>" class="shrink-0 w-full sm:w-auto">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="cb-btn cb-btn--gold py-2.5 px-4 rounded-xl font-bold w-full">
                        Redeem 1 Credit
                    </button>
                </form>
            </div>
        <?php endif; ?>

        
        <?php if(!empty($keyId)): ?>
            <div class="cb-card p-6 border border-slate-200/90 bg-white rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div class="flex-1">
                    <h2 class="text-base font-bold text-cb-navy mb-1">Pay with Razorpay</h2>
                    <p class="text-xs text-slate-500">Supports Card, UPI, Netbanking, or Wallets. (If you own a Guest Pay Pack, buy credits first, then apply above.)</p>
                </div>
                <div class="shrink-0 w-full sm:w-auto">
                    <button type="button" id="rzp-dgpay-btn" class="cb-btn cb-btn--navy py-3 px-6 rounded-xl font-bold w-full sm:w-auto shadow-md hover:shadow-lg transition-all">
                        Pay ₹<?php echo e(number_format($amount, 0)); ?> with Razorpay
                    </button>
                </div>
            </div>

            <form id="rzp-dgpay-verify-form" method="POST" action="<?php echo e(route('client.events.direct-gpay-unlock.razorpay.verify', $event)); ?>" class="hidden">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="razorpay_order_id" id="rzp-dgpay-oid" value="">
                <input type="hidden" name="razorpay_payment_id" id="rzp-dgpay-pid" value="">
                <input type="hidden" name="razorpay_signature" id="rzp-dgpay-sig" value="">
            </form>
        <?php else: ?>
            <div class="cb-alert cb-alert--error" role="alert">
                Razorpay is not configured. Please contact support.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php if(!empty($keyId)): ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function () {
    var orderUrl = <?php echo json_encode(route('client.events.direct-gpay-unlock.razorpay.order', $event), 512) ?>;
    var token = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    var name = <?php echo json_encode(Auth::user()->name ?? 'Organizer', 15, 512) ?>;
    var email = <?php echo json_encode(Auth::user()->email ?? '', 15, 512) ?>;
    var btn = document.getElementById('rzp-dgpay-btn');
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
                description: 'Direct GPay unlock — ' + <?php echo json_encode($event->title, 15, 512) ?>,
                prefill: { name: name, email: email },
                theme: { color: '#1A3646' },
                handler: function (response) {
                    document.getElementById('rzp-dgpay-oid').value = response.razorpay_order_id;
                    document.getElementById('rzp-dgpay-pid').value = response.razorpay_payment_id;
                    document.getElementById('rzp-dgpay-sig').value = response.razorpay_signature;
                    document.getElementById('rzp-dgpay-verify-form').submit();
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

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/direct-gpay-unlock/payment.blade.php ENDPATH**/ ?>
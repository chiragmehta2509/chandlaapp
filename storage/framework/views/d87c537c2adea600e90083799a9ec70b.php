<?php $__env->startSection('title', 'Matrimonial plans'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full min-w-0 max-w-4xl mx-auto">
    <a href="<?php echo e(route('client.matrimonial.index')); ?>" class="cb-link text-sm inline-flex items-center gap-2 mb-3 sm:mb-4 min-h-[44px] touch-manipulation">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Find Partner
    </a>
    <h1 class="cb-page-title">Unlock full profiles</h1>
    <p class="cb-subtitle mt-1 break-words leading-relaxed">Complete payment on Razorpay. When it succeeds, your access is <strong>activated automatically</strong> (no admin step).</p>

    <div class="mt-4 rounded-lg border border-sky-200 bg-sky-50/90 px-3.5 sm:px-4 py-3 text-sm text-sky-950" role="status">
        <p class="font-medium">Use the same email and/or phone you use for Chandla Book.</p>
        <p class="mt-1 text-sky-900/90">Razorpay must send an email or phone that matches your account so we can attach the payment. If it doesn’t activate in a few minutes, check your Razorpay receipt email vs your profile.</p>
    </div>

    <?php if($active): ?>
        <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 sm:px-4 py-3 text-sm text-emerald-900 break-words">
            You have an active plan until <strong><?php echo e($active->expiry_date->format('d/m/Y')); ?></strong>.
        </div>
    <?php endif; ?>

    <h2 class="text-base sm:text-lg font-bold text-cb-navy mt-8 sm:mt-10 mb-3 sm:mb-4">Pay on Razorpay</h2>

    <?php if(!empty($keyId)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $def): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="cb-card p-4 sm:p-6 flex flex-col min-w-0">
                    <h2 class="text-lg sm:text-xl font-bold text-cb-navy leading-tight break-words"><?php echo e($def['label']); ?></h2>
                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 tabular-nums">₹<?php echo e(number_format($def['price_inr'], 0)); ?></p>
                    <p class="text-sm text-slate-600 mt-1">Valid for <strong><?php echo e($def['months']); ?></strong> month(s) after activation</p>
                    <button
                        type="button"
                        class="rzp-plan-btn mt-auto pt-4 sm:pt-6 w-full min-h-[2.75rem] inline-flex items-center justify-center gap-2 cb-btn cb-btn-navy text-center touch-manipulation"
                        data-plan="<?php echo e($key); ?>"
                        data-amount="<?php echo e(number_format($def['price_inr'], 0)); ?>"
                        data-name="<?php echo e($def['label']); ?>"
                    >
                        <i class="fas fa-lock" aria-hidden="true"></i> Pay ₹<?php echo e(number_format($def['price_inr'], 0)); ?> with Razorpay
                    </button>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <form id="rzp-verify-form" method="POST" action="<?php echo e(route('client.matrimonial.plans.verify')); ?>" class="hidden">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="plan" id="rzp-plan" value="">
            <input type="hidden" name="razorpay_order_id" id="rzp-oid" value="">
            <input type="hidden" name="razorpay_payment_id" id="rzp-pid" value="">
            <input type="hidden" name="razorpay_signature" id="rzp-sig" value="">
        </form>
    <?php else: ?>
        <div class="mt-4 cb-alert cb-alert--error" role="alert">Razorpay is not configured. Add <code class="text-xs">RAZORPAY_KEY_ID</code> and <code class="text-xs">RAZORPAY_KEY_SECRET</code> to <code class="text-xs">.env</code> to enable online payment.</div>
    <?php endif; ?>

    <div class="mt-8 sm:mt-10 cb-card p-4 sm:p-5 hidden">
        <h3 class="text-sm font-bold text-cb-navy">For the site owner: webhook</h3>
        <p class="mt-2 text-xs sm:text-sm text-slate-600 break-words leading-relaxed">In <a href="https://dashboard.razorpay.com/" class="text-cb-navy underline" target="_blank" rel="noopener">Razorpay Dashboard</a> → <strong>Webhooks</strong>, add endpoint <code class="text-[0.7rem] sm:text-xs break-all block mt-1 p-1.5 bg-slate-100 rounded"><?php echo e(url('/webhooks/razorpay')); ?></code> with a generated secret, set <code class="text-xs">RAZORPAY_WEBHOOK_SECRET</code> in <code class="text-xs">.env</code>, and enable events such as <strong>payment.captured</strong> (or your payment link success events).</p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php if(!empty($keyId)): ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function () {
    var orderUrl = <?php echo json_encode(route('client.matrimonial.plans.order'), 15, 512) ?>;
    var token = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    var userName = <?php echo json_encode($user->name ?? 'User', 15, 512) ?>;
    var userEmail = <?php echo json_encode($user->email ?? '', 15, 512) ?>;
    var userPhone = <?php echo json_encode($user->phone ?? '', 15, 512) ?>;
    
    document.querySelectorAll('.rzp-plan-btn').forEach(function(btn) {
        btn.addEventListener('click', function () {
            var planKey = btn.getAttribute('data-plan');
            var planAmount = btn.getAttribute('data-amount');
            var planName = btn.getAttribute('data-name');
            
            var originalHtml = btn.innerHTML;
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
                body: JSON.stringify({ plan: planKey }),
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
                    description: planName,
                    prefill: { name: userName, email: userEmail, contact: userPhone },
                    theme: { color: '#1A3646' },
                    handler: function (response) {
                        document.getElementById('rzp-plan').value = planKey;
                        document.getElementById('rzp-oid').value = response.razorpay_order_id;
                        document.getElementById('rzp-pid').value = response.razorpay_payment_id;
                        document.getElementById('rzp-sig').value = response.razorpay_signature;
                        document.getElementById('rzp-verify-form').submit();
                    },
                };
                var rzp = new Razorpay(opt);
                rzp.on('payment.failed', function (resp) {
                    alert('Payment failed: ' + (resp.error?.description || 'Unknown error'));
                });
                rzp.open();
            })
            .catch(function () {
                btn.disabled = false;
                btn.removeAttribute('aria-busy');
                btn.innerHTML = originalHtml;
                alert('Network error. Try again.');
            });
        });
    });
})();
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/matrimonial/plans.blade.php ENDPATH**/ ?>
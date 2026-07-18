@extends('layouts.client')

@section('title', ($packConfig['label'] ?? 'Pack') . ' — Checkout')

@section('content')
<div class="max-w-xl mx-auto py-4 sm:py-8">

    {{-- Back Link --}}
    <div class="mb-6">
        <a href="{{ route('client.plans') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-cb-navy transition-colors mb-4">
            <i class="fas fa-arrow-left text-xs"></i> Back to Plans
        </a>
        <h1 class="text-2xl font-bold text-cb-navy">Secure Checkout</h1>
        <p class="text-sm text-slate-500 mt-1">Your payment is processed by Razorpay — 100% secure.</p>

        {{-- Test mode badge --}}
        @if($isTestMode)
            <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-200 text-amber-800 text-xs font-medium px-3 py-1.5">
                <i class="fas fa-flask"></i> Test Mode Active — no real money will be charged
            </div>
        @endif
    </div>

    {{-- Upgrade context banner --}}
    @if($isUpgrade)
    <div class="mb-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800 flex items-start gap-2">
        <i class="fas fa-arrow-up-right-dots mt-0.5 shrink-0 text-sky-500"></i>
        <span>Upgrading from <strong>{{ $currentPlanName }}</strong> → <strong>{{ $targetPlanName }}</strong>. All previous plan features are retained.</span>
    </div>
    @elseif($currentLevel === 0)
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-2">
        <i class="fas fa-seedling mt-0.5 shrink-0 text-emerald-500"></i>
        <span>You're on the <strong>Starter Plan</strong>. Purchasing this plan will immediately unlock its features on your account.</span>
    </div>
    @endif

    {{-- Order summary card --}}
    <div class="rounded-2xl border border-slate-200/90 bg-white shadow-sm overflow-hidden mb-6">
        <div class="h-1 bg-gradient-to-r from-amber-300 via-amber-400 to-amber-500 w-full"></div>
        <div class="p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="inline-flex items-center rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold px-2.5 py-1 mb-3">
                        <i class="fas fa-box-open mr-1.5 text-[10px]"></i>Package
                    </span>
                    <h2 class="text-xl font-bold text-cb-navy">{{ $packConfig['label'] ?? ucfirst($configKey) }}</h2>
                    @if(!empty($packConfig['description']))
                        <p class="text-sm text-slate-500 mt-1">{{ $packConfig['description'] }}</p>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <div class="text-3xl font-extrabold text-cb-navy">₹{{ number_format($amountInr, 0) }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">INR, one-time</div>
                </div>
            </div>

            {{-- Features list from config --}}
            @php $featureList = $packConfig['features'] ?? []; @endphp
            @if($featureList)
                <ul class="mt-4 space-y-2 border-t border-slate-100 pt-4">
                    @foreach($featureList as $f)
                        <li class="flex items-center gap-2 text-sm text-slate-700">
                            <i class="fas fa-check-circle text-emerald-500 text-xs shrink-0"></i>
                            {{ $f }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Error message area --}}
    <div id="checkout-error" class="hidden mb-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-900 px-4 py-3 text-sm">
        <i class="fas fa-exclamation-circle mr-1.5"></i>
        <span id="checkout-error-msg"></span>
        <button id="checkout-retry-btn" class="mt-3 w-full cb-btn cb-btn--navy cb-btn--sm justify-center hidden">
            <i class="fas fa-redo text-xs"></i> Try Again
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- PAYMENT BUTTONS                                                     --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}

    @php
        $hasLiveUrl = !empty($livePaymentUrl);
        $hasTestUrl = !empty($testPaymentUrl);
    @endphp

    {{-- SECTION A: Test Payment Button (Orders API — shown in test mode) --}}
    @if($isTestMode)
    <div class="mb-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
        <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-2">
            <i class="fas fa-flask mr-1"></i> Test Payment (No Real Money Deducted)
        </p>
        <p class="text-xs text-amber-600 mb-3">
            Uses Razorpay Test Mode. Completes the payment flow without any real transaction.
            Plan is activated immediately after successful test payment.
        </p>
        <button
            id="pay-btn-test"
            type="button"
            class="cb-btn cb-btn-gold w-full justify-center text-sm py-3 shadow-md ring-1 ring-amber-400/30 font-bold"
        >
            <i class="fas fa-flask text-sm" aria-hidden="true"></i>
            Test Pay ₹{{ number_format($amountInr, 0) }} (Test Mode)
        </button>
        @if($hasTestUrl)
        <div class="mt-2 text-center">
            <span class="text-xs text-amber-600">— or use Razorpay Payment Page —</span>
        </div>
        <a
            href="{{ $testPaymentUrl }}"
            target="_blank"
            class="mt-2 cb-btn cb-btn--navy w-full justify-center text-sm py-2.5 font-semibold"
        >
            <i class="fas fa-external-link-alt text-xs" aria-hidden="true"></i>
            Open Test Payment Page
        </a>
        @endif
    </div>
    @endif

    {{-- SECTION B: Live Payment Button (Orders API — always shown) --}}
    <div class="mb-3 @if($isTestMode) rounded-xl border border-slate-200 bg-slate-50 p-4 @endif">
        @if($isTestMode)
        <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">
            <i class="fas fa-credit-card mr-1"></i> Live Payment (Real Money)
        </p>
        <p class="text-xs text-slate-500 mb-3">
            Uses live Razorpay keys. Real payment will be processed.
        </p>
        @endif

        <button
            id="pay-btn"
            type="button"
            class="cb-btn cb-btn-gold w-full justify-center text-base py-4 shadow-md ring-1 ring-amber-400/30 font-bold"
        >
            <i class="fas fa-lock text-sm" aria-hidden="true"></i>
            Pay ₹{{ number_format($amountInr, 0) }} with Razorpay
        </button>

        @if($hasLiveUrl && !$isTestMode)
        <div class="mt-3">
            <a
                href="{{ $livePaymentUrl }}"
                class="cb-btn w-full justify-center text-sm py-3 font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 shadow-sm rounded-xl"
            >
                <i class="fas fa-external-link-alt text-xs" aria-hidden="true"></i>
                Pay via Razorpay Payment Page
            </a>
        </div>
        @endif
    </div>

    <p class="text-center text-xs text-slate-400 mt-3">
        <i class="fas fa-shield-halved text-slate-300 mr-1"></i>
        Payments are encrypted and processed securely by Razorpay
    </p>

</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function() {
    // ── DOM refs ──────────────────────────────────────────────────────────────
    const payBtn      = document.getElementById('pay-btn');
    const payBtnTest  = document.getElementById('pay-btn-test');
    const errBox      = document.getElementById('checkout-error');
    const errMsg      = document.getElementById('checkout-error-msg');
    const retryBtn    = document.getElementById('checkout-retry-btn');

    const PACK        = @json($pack);
    const ORDER_URL   = @json(route('client.packs.order', $pack));
    const VERIFY_URL  = @json(route('client.packs.verify', $pack));
    const CSRF_TOKEN  = document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token());
    const IS_TEST     = @json($isTestMode);
    const AMOUNT_INR  = @json($amountInr);

    // ── Helpers ───────────────────────────────────────────────────────────────
    function showError(msg, showRetry = true) {
        errMsg.textContent = msg;
        errBox.classList.remove('hidden');
        if (showRetry) retryBtn.classList.remove('hidden');
        if (payBtn)     { payBtn.disabled = false; payBtn.innerHTML = '<i class="fas fa-lock text-sm"></i> Pay ₹{{ number_format($amountInr, 0) }} with Razorpay'; }
        if (payBtnTest) { payBtnTest.disabled = false; payBtnTest.innerHTML = '<i class="fas fa-flask text-sm"></i> Test Pay ₹{{ number_format($amountInr, 0) }} (Test Mode)'; }
    }

    function hideError() {
        errBox.classList.add('hidden');
        retryBtn.classList.add('hidden');
    }

    function setLoading(btn, label) {
        if (!btn) return;
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin text-sm"></i> ${label}`;
    }

    // ── Core checkout flow (Razorpay Orders API) ──────────────────────────────
    async function startCheckout(triggerBtn) {
        hideError();
        setLoading(triggerBtn, 'Creating order…');

        let orderData;
        try {
            const res = await fetch(ORDER_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({}),
            });
            orderData = await res.json();
            if (!res.ok || orderData.error) {
                throw new Error(orderData.error || 'Could not create order.');
            }
        } catch (e) {
            showError(e.message || 'Network error. Please try again.');
            return;
        }

        const options = {
            key:         orderData.key_id,
            amount:      orderData.amount,
            currency:    orderData.currency || 'INR',
            order_id:    orderData.order_id,
            name:        'Chandla Book',
            description: @json($packConfig['label'] ?? $configKey),
            prefill: {
                name:    orderData.user_name  || '',
                email:   orderData.user_email || '',
                contact: orderData.user_phone || '',
            },
            theme: { color: '#92400E' },

            handler: async function(response) {
                setLoading(triggerBtn, 'Verifying…');
                try {
                    const vRes = await fetch(VERIFY_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            razorpay_order_id:   response.razorpay_order_id,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_signature:  response.razorpay_signature,
                        }),
                    });
                    const vData = await vRes.json();
                    if (vData.success && vData.redirect_url) {
                        window.location.href = vData.redirect_url;
                    } else {
                        showError(vData.error || 'Verification failed. Contact support if money was debited.', true);
                    }
                } catch (e) {
                    showError('Network error during verification. Contact support with payment ID: ' + response.razorpay_payment_id, false);
                }
            },

            modal: {
                ondismiss: function() {
                    if (payBtn)     { payBtn.disabled = false;     payBtn.innerHTML = '<i class="fas fa-lock text-sm"></i> Pay ₹{{ number_format($amountInr, 0) }} with Razorpay'; }
                    if (payBtnTest) { payBtnTest.disabled = false; payBtnTest.innerHTML = '<i class="fas fa-flask text-sm"></i> Test Pay ₹{{ number_format($amountInr, 0) }} (Test Mode)'; }
                }
            },
        };

        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function(resp) {
            showError('Payment failed: ' + (resp.error?.description || 'Unknown error'), true);
        });
        rzp.open();
    }

    // ── Attach event listeners ────────────────────────────────────────────────
    if (payBtn) {
        payBtn.addEventListener('click', () => startCheckout(payBtn));
    }
    if (payBtnTest) {
        payBtnTest.addEventListener('click', () => startCheckout(payBtnTest));
    }
    retryBtn.addEventListener('click', () => startCheckout(payBtn));

})();
</script>
@endpush

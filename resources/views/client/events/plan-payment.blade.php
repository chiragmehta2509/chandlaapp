@extends('layouts.client')

@section('title', 'Plan Payment')

@section('content')
<div class="w-full min-w-0 max-w-4xl mx-auto">
    <a href="{{ route('client.events.show', $event->id) }}" class="cb-link text-sm inline-flex items-center gap-2 mb-4">
        <i class="fas fa-arrow-left mr-2"></i>Back to Event
    </a>
    <h1 class="cb-page-title">Unlimited Plan Payment</h1>
    <p class="cb-subtitle mt-1">Pay <strong>₹{{ number_format($amount, 0) }}</strong> for <strong>{{ $event->title }}</strong> (default unlimited price is ₹{{ number_format((float) config('services.direct_gpay_unlock.amount', 400), 0) }} unless changed on the event).</p>

    @if(!empty($keyId))
        <div class="cb-card p-5 sm:p-6 mt-6 mb-6">
            <h2 class="text-lg font-bold text-cb-navy mb-2">Pay with Razorpay</h2>
            <p class="text-sm text-slate-600 mb-4">Secure card, UPI, netbanking, or wallets. Your event upgrades as soon as payment is confirmed.</p>
            <button type="button" id="rzp-event-plan-btn" class="cb-btn cb-btn-navy w-full sm:w-auto min-h-[2.75rem] touch-manipulation">
                Pay ₹{{ number_format($amount, 0) }} with Razorpay
            </button>
        </div>

        <form id="rzp-event-verify-form" method="POST" action="{{ route('client.events.plan.razorpay.verify', $event->id) }}" class="hidden">
            @csrf
            <input type="hidden" name="razorpay_order_id" id="rzp-oid" value="">
            <input type="hidden" name="razorpay_payment_id" id="rzp-pid" value="">
            <input type="hidden" name="razorpay_signature" id="rzp-sig" value="">
        </form>
    @else
        <div class="mt-4 cb-alert cb-alert--error" role="alert">Razorpay is not configured. Add <code class="text-xs">RAZORPAY_KEY_ID</code> and <code class="text-xs">RAZORPAY_KEY_SECRET</code> to <code class="text-xs">.env</code> to enable online payment.</div>
    @endif

    {{-- Manual UPI (QR + admin verification) — uncomment if needed
    <h2 class="text-base font-bold text-cb-navy mt-8 mb-3">Or pay with UPI (manual verification)</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="cb-card p-5 sm:p-6">
            <h3 class="text-lg font-bold text-cb-navy mb-4">UPI QR</h3>
            <div class="space-y-3">
                <p class="text-sm text-slate-600">Amount</p>
                <p class="text-2xl font-bold text-slate-900">₹{{ number_format($amount, 2) }}</p>
            </div>

            <div class="mt-6">
                @if($qrSvg)
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 inline-block max-w-full overflow-hidden">
                        {!! $qrSvg !!}
                    </div>
                    <p class="text-xs text-slate-500 mt-2">UPI ID: {{ $upiId }}</p>
                @else
                    <div class="rounded-lg border border-amber-200 bg-amber-50 text-amber-900 px-4 py-3 text-sm">
                        UPI ID is not configured. Add <code class="text-xs">UPI_ID</code> in <code class="text-xs">.env</code>.
                    </div>
                @endif
            </div>
        </div>

        <div class="cb-card p-5 sm:p-6">
            <h3 class="text-lg font-bold text-cb-navy mb-4">Submit transaction ID</h3>
            <form method="POST" action="{{ route('client.events.plan.payment.store', $event->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Transaction ID</label>
                    <input type="text" name="transaction_id" value="{{ old('transaction_id') }}"
                           class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-base sm:text-sm"
                           placeholder="e.g. 1234567890">
                </div>
                <button type="submit" class="w-full min-h-[2.75rem] bg-emerald-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-emerald-700 touch-manipulation">
                    Submit for admin verification
                </button>
            </form>
        </div>
    </div>
    --}}
</div>
@endsection

@push('scripts')
@if(!empty($keyId))
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function () {
    var orderUrl = @json(route('client.events.plan.razorpay.order', $event->id));
    var token = @json(csrf_token());
    var name = @json(Auth::user()->name ?? 'Organizer');
    var email = @json(Auth::user()->email ?? '');
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
                description: 'Event unlimited plan — #{{ $event->id }}',
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
@endif
@endpush

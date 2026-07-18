@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.payments.index', ['tab' => $isRazorpay ? 'razorpay' : 'manual']) }}" class="inline-flex items-center text-sm font-semibold text-amber-600 hover:text-amber-800 transition-colors mb-4">
        <i class="fas fa-arrow-left mr-2"></i>Back to Payments
    </a>
    <h1 class="text-3xl font-bold text-gray-800 font-display">Payment Details</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left details card --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden p-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-5">
            <div>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 mb-2">
                    <i class="fas {{ $isRazorpay ? 'fa-credit-card' : 'fa-qrcode' }} text-[9px]"></i>
                    {{ $isRazorpay ? 'Razorpay Gateway Transaction' : 'Manual UPI Submission' }}
                </span>
                <h2 class="text-xl font-bold text-slate-800">
                    {{ $isRazorpay ? $payment->package_name : 'Manual UPI: ' . $payment->transaction_id }}
                </h2>
                <p class="text-xs text-slate-400 font-mono mt-1">
                    {{ $isRazorpay ? 'TXN Number: ' . $payment->transaction_number : 'UPI Txn Ref: ' . $payment->transaction_id }}
                </p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider 
                    @if($isRazorpay)
                        {{ $payment->statusBadgeClass() }}
                    @else
                        {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                           ($payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}
                    @endif">
                    @if($isRazorpay)
                        {{ $payment->statusLabel() }}
                    @else
                        {{ ucfirst($payment->status) }}
                    @endif
                </span>
                <div class="text-2xl font-black text-slate-900 mt-2">
                    ₹{{ number_format($isRazorpay ? $payment->amount_inr : $payment->amount, 2) }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Created At</label>
                <p class="text-sm font-semibold text-slate-700">{{ $payment->created_at->format('M d, Y · h:i A') }}</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Paid At</label>
                <p class="text-sm font-semibold text-slate-700">
                    @if($isRazorpay)
                        {{ $payment->paid_at ? $payment->paid_at->format('M d, Y · h:i A') : '—' }}
                    @else
                        {{ $payment->paid_at ? $payment->paid_at->format('M d, Y · h:i A') : '—' }}
                    @endif
                </p>
            </div>
            @if($isRazorpay)
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Razorpay Order ID</label>
                    <p class="text-sm font-mono text-slate-700">{{ $payment->razorpay_order_id ?: '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Razorpay Payment ID</label>
                    <p class="text-sm font-mono text-slate-700">{{ $payment->razorpay_payment_id ?: '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Payment Method</label>
                    <p class="text-sm font-semibold text-slate-700 uppercase">{{ $payment->payment_method ?: '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Package Key</label>
                    <p class="text-sm font-semibold text-slate-700 font-mono">{{ $payment->package_key }}</p>
                </div>
                @if($payment->reference_id)
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Reference ID / Entity</label>
                        <p class="text-sm font-mono text-slate-700">{{ $payment->reference_id }}</p>
                    </div>
                @endif
            @else
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Razorpay Order ID (Linked)</label>
                    <p class="text-sm font-mono text-slate-700">{{ $payment->razorpay_order_id ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Payment Method</label>
                    <p class="text-sm font-semibold text-slate-700 uppercase">{{ $payment->payment_method ?? 'UPI' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Payment Type (Metadata)</label>
                    <p class="text-sm font-semibold text-slate-700">{{ $payment->metadata['type'] ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Plan / Detail</label>
                    <p class="text-sm font-semibold text-slate-700">
                        @if(data_get($payment->metadata, 'type') === 'direct_gpay_unlock')
                            Direct GPay Event Unlock
                        @elseif(data_get($payment->metadata, 'type') === 'matrimonial_plan')
                            Find Partner Plan: {{ data_get($payment->metadata, 'matrimonial_plan') }}
                        @else
                            {{ data_get($payment->metadata, 'plan') ?? (($invId = data_get($payment->metadata, 'marriage_invitation_id')) ? 'Marriage card #'.$invId : 'N/A') }}
                        @endif
                    </p>
                </div>
            @endif
        </div>

        @if($isRazorpay && $payment->failure_reason)
            <div class="mt-6 rounded-xl border border-rose-100 bg-rose-50/50 p-4">
                <p class="text-xs font-bold text-rose-800 uppercase tracking-wider">Failure Reason</p>
                <p class="text-sm text-rose-900 mt-1 font-medium">{{ $payment->failure_reason }}</p>
            </div>
        @endif

        {{-- Verification Actions --}}
        @if(!$isRazorpay && $payment->status === 'pending')
            <div class="mt-8 border-t border-slate-100 pt-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">Admin Actions Required</h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Verify the UPI transaction in your bank account / GPay statement before approving.
                </p>
                <div class="flex gap-3">
                    <form method="POST" action="{{ route('admin.payments.verify', $payment->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors">
                            <i class="fas fa-check mr-2"></i> Approve & Activate
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.payments.verify', $payment->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="failed">
                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors">
                            <i class="fas fa-times mr-2"></i> Reject / Fail
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    {{-- Right User Info Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 self-start">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Customer Details</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-400">Name</label>
                <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $payment->user->name ?? 'Deleted User' }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400">Phone</label>
                <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $payment->user->phone ?? '—' }}</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400">Email</label>
                <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $payment->user->email ?? '—' }}</p>
            </div>
            @if(!$isRazorpay && $payment->event)
                <div class="border-t border-slate-100 pt-4">
                    <label class="block text-xs font-semibold text-slate-400">Linked Event</label>
                    <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $payment->event->title }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">ID: {{ $payment->event->id }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Metadata JSON view --}}
@if($isRazorpay && ($payment->gateway_response || $payment->metadata))
    <div class="mt-6 bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 overflow-hidden">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4"><i class="fas fa-code mr-1.5"></i> Gateway Responses / Metadata</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($payment->metadata)
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-2">Metadata</p>
                    <pre class="bg-slate-900 text-slate-200 text-xs rounded-xl p-4 overflow-x-auto font-mono max-h-96">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
            @if($payment->gateway_response)
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-2">Gateway Response</p>
                    <pre class="bg-slate-900 text-slate-200 text-xs rounded-xl p-4 overflow-x-auto font-mono max-h-96">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection

@extends('layouts.client')

@section('title', 'Transaction ' . $transaction->transaction_number)

@section('content')
<div class="max-w-3xl mx-auto py-4 sm:py-8">

    {{-- Back Link --}}
    <div class="mb-6">
        <a href="{{ route('client.transactions.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-cb-navy transition-colors mb-4">
            <i class="fas fa-arrow-left text-xs"></i> Back to Transactions
        </a>
    </div>

    {{-- Transaction Card --}}
    <div class="rounded-2xl border border-slate-200/90 bg-white shadow-sm overflow-hidden mb-6">
        <div class="absolute-top-stripe h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 w-full"></div>
        <div class="p-6">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5 mb-5">
                <div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 mb-2">
                        <i class="fas fa-receipt text-[9px]"></i> Transaction Record
                    </span>
                    <h1 class="text-xl font-bold text-cb-navy">{{ $transaction->package_name }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Number: <span class="font-mono">{{ $transaction->transaction_number }}</span></p>
                </div>
                <div class="sm:text-right shrink-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $transaction->statusBadgeClass() }}">
                        {{ $transaction->statusLabel() }}
                    </span>
                    <div class="text-2xl font-black text-cb-navy mt-1.5">₹{{ number_format($transaction->amount_inr, 2) }}</div>
                </div>
            </div>

            {{-- Details Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Created At</p>
                    <p class="text-sm font-medium text-slate-700 mt-0.5">{{ $transaction->created_at->format('d/m/Y · g:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Paid At</p>
                    <p class="text-sm font-medium text-slate-700 mt-0.5">
                        {{ $transaction->paid_at ? $transaction->paid_at->format('d/m/Y · g:i A') : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Payment Method</p>
                    <p class="text-sm font-medium text-slate-700 mt-0.5">
                        <span class="uppercase">{{ $transaction->payment_method ?: '—' }}</span>
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Currency</p>
                    <p class="text-sm font-medium text-slate-700 mt-0.5">{{ $transaction->currency }}</p>
                </div>
                <div class="md:col-span-2 border-t border-slate-100 pt-4"></div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Razorpay Order ID</p>
                    <p class="text-sm font-mono text-slate-700 mt-0.5">{{ $transaction->razorpay_order_id ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Razorpay Payment ID</p>
                    <p class="text-sm font-mono text-slate-700 mt-0.5">{{ $transaction->razorpay_payment_id ?: '—' }}</p>
                </div>
                @if($transaction->reference_id)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Reference ID / Entity</p>
                        <p class="text-sm font-mono text-slate-700 mt-0.5">{{ $transaction->reference_id }}</p>
                    </div>
                @endif
                @if($transaction->failure_reason)
                    <div class="md:col-span-2 rounded-xl border border-rose-100 bg-rose-50/50 p-4">
                        <p class="text-xs font-bold text-rose-800 uppercase tracking-wider">Failure Reason</p>
                        <p class="text-sm text-rose-900 mt-1">{{ $transaction->failure_reason }}</p>
                    </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-3 border-t border-slate-100 pt-5">
                @if($transaction->canRetry() && in_array($transaction->package_key, ['celebration', 'ledger_duo', 'family', 'premium_bundle', 'guest_pay_single'], true))
                    @php
                        $checkoutSlug = str_replace('_', '-', $transaction->package_key);
                        if ($checkoutSlug === 'premium-bundle') $checkoutSlug = 'bundle';
                        if ($checkoutSlug === 'ledger-duo') $checkoutSlug = 'host-duo';
                    @endphp
                    <a href="{{ route('client.packs.checkout', $checkoutSlug) }}" class="cb-btn cb-btn-gold">
                        <i class="fas fa-redo text-xs"></i> Retry Checkout
                    </a>
                @elseif($transaction->canRetry() && $transaction->package_key === 'marriage_invitation' && $transaction->reference_id)
                    <a href="{{ route('client.marriage-invitations.payment', (int)$transaction->reference_id) }}" class="cb-btn cb-btn-gold">
                        <i class="fas fa-redo text-xs"></i> Retry Payment
                    </a>
                @elseif($transaction->canRetry() && in_array($transaction->package_key, ['matrimonial_200', 'matrimonial_500'], true))
                    <a href="{{ route('client.matrimonial.plans') }}" class="cb-btn cb-btn-gold">
                        <i class="fas fa-redo text-xs"></i> View Plans
                    </a>
                @endif
                <a href="{{ route('client.transactions.index') }}" class="cb-btn cb-btn--ghost">
                    Back to History
                </a>
            </div>
        </div>
    </div>

    {{-- Collapsible Developer Details / JSON metadata --}}
    @if($transaction->gateway_response || $transaction->metadata)
        <div class="rounded-2xl border border-slate-200/90 bg-white shadow-sm overflow-hidden" x-data="{ open: false }">
            <button
                type="button"
                @click="open = !open"
                class="w-full flex items-center justify-between p-5 text-sm font-semibold text-slate-700 hover:bg-slate-50/50 transition-colors"
            >
                <span><i class="fas fa-code mr-1.5 text-slate-400"></i> Gateway Payload / Metadata</span>
                <i class="fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div x-show="open" class="border-t border-slate-100 p-5 bg-slate-50/50" x-cloak>
                @if($transaction->metadata)
                    <div class="mb-4">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Application Metadata</p>
                        <pre class="bg-slate-900 text-slate-200 text-xs rounded-xl p-4 overflow-x-auto font-mono">{{ json_encode($transaction->metadata, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
                @if($transaction->gateway_response)
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Razorpay Gateway Response</p>
                        <pre class="bg-slate-900 text-slate-200 text-xs rounded-xl p-4 overflow-x-auto font-mono">{{ json_encode($transaction->gateway_response, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection

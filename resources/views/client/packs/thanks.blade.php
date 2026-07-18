@extends('layouts.client')

@section('title', 'Payment')

@section('content')
@php
    $kindLabels = [
        'celebration' => 'Celebration Plan (₹' . number_format((float) config('packs.celebration.amount_inr', 300), 0) . ')',
        'ledger_duo' => 'Host Plus Plan (₹' . number_format((float) config('packs.ledger_duo.amount_inr', 500), 0) . ')',
        'family' => 'Family Plan (₹' . number_format((float) config('packs.family.amount_inr', 600), 0) . ')',
        'premium_bundle' => 'Premium Host Plan (₹' . number_format((float) config('packs.premium_bundle.amount_inr', 700), 0) . ')',
        'guest_pay_single' => 'Guest Contribution credit (₹' . number_format((float) config('packs.guest_pay_single.amount_inr', 400), 0) . ')',
        'professional' => 'Professional Plan (₹' . number_format((float) config('packs.professional.amount_inr', 999), 0) . ')',
        'enterprise' => 'Enterprise Plan (₹' . number_format((float) config('packs.enterprise.amount_inr', 9999), 0) . ')',
        'event_unlimited' => 'Event Unlimited plan',
    ];
    $unlockedLabel = $appliedKind ? ($kindLabels[$appliedKind] ?? ucfirst($appliedKind)) : null;
@endphp

<div class="max-w-lg mx-auto cb-card p-8 sm:p-10 text-center border border-slate-200/90">
    <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 text-2xl mb-4" aria-hidden="true">
        <i class="fas fa-circle-check"></i>
    </div>
    <h1 class="text-xl font-bold text-cb-navy mb-2">Thanks — payment received</h1>

    @if($unlockedLabel)
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 mb-4 text-sm text-emerald-900">
            <p class="font-semibold"><i class="fas fa-unlock mr-1.5" aria-hidden="true"></i>{{ $unlockedLabel }} — unlocked.</p>
            @if($razorpayPaymentId)
                <p class="text-xs text-emerald-700/80 mt-1 font-mono">Payment ID: {{ $razorpayPaymentId }}</p>
            @endif
        </div>
    @else
        <p class="text-sm text-slate-600 leading-relaxed mb-6">If you paid with the <strong>same email or phone</strong> as your Chandla Book account, your unlock should apply within a minute. Refresh the <strong>dashboard</strong>, <strong>Marriage invitation</strong>, <strong>Pre‑wedding</strong>, or <strong>Events</strong> list after returning from Razorpay.</p>
    @endif

    <div class="flex flex-col sm:flex-row gap-3 justify-center mt-4">
        <a href="{{ route('client.dashboard') }}" class="cb-btn cb-btn--navy justify-center">Back to dashboard</a>
        <a href="{{ route('client.events.index') }}" class="cb-btn cb-btn--ghost border border-slate-200 justify-center">My events</a>
    </div>
</div>
@endsection

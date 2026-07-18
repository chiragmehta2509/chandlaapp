@php
    $seoTitle = 'Payment submitted — ' . $event->title;
    $seoDesc = 'Payment submitted for ' . $event->title . ' — Chandla Book.';
    $seoRobots = 'noindex, nofollow';
@endphp
@extends('layouts.public-guest')

@section('content')
<div class="max-w-2xl mx-auto pt-2">
    <div class="rounded-2xl bg-white text-slate-800 shadow-2xl shadow-slate-950/40 ring-1 ring-white/60 overflow-hidden p-6 md:p-8 text-center">
        <div class="mb-6">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-emerald-100 mb-4 ring-4 ring-emerald-50">
                <i class="fas fa-check text-4xl text-emerald-600"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">Payment submitted successfully</h1>
            <p class="text-slate-600">Thank you for your contribution.</p>
        </div>

        <div class="bg-slate-50 rounded-xl p-6 mb-6 text-left ring-1 ring-slate-100">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Payment details</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-slate-600">Event</span>
                    <span class="font-medium text-slate-900 text-right">{{ $event->title }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-600">Your name</span>
                    <span class="font-medium text-slate-900 text-right">{{ $chandla->giver_name }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-600">Amount</span>
                    <span class="font-bold text-indigo-600 tabular-nums">₹{{ number_format($chandla->amount, 2) }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-slate-600">Date</span>
                    <span class="font-medium text-slate-900 text-right">{{ $chandla->received_date->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <div class="p-4 bg-sky-50 border border-sky-100 rounded-xl mb-6 text-left">
            <p class="text-sm text-sky-900">
                <i class="fas fa-info-circle mr-2 text-sky-600"></i>
                Your payment has been recorded. The organiser will verify and confirm when ready.
            </p>
        </div>

        @php
            $paymentSummary = "Payment Details%0A"
                . "Event: {$event->title}%0A"
                . "Name: {$chandla->giver_name}%0A"
                . "Amount: INR " . number_format($chandla->amount, 2) . "%0A"
                . "Date: " . $chandla->received_date->format('d/m/Y') . "%0A"
                . "Status: Submitted";
            $whatsAppNumber = preg_replace('/\D+/', '', (string) ($event->user->phone ?? $chandla->giver_phone ?? ''));
            $whatsAppUrl = $whatsAppNumber
                ? "https://wa.me/{$whatsAppNumber}?text={$paymentSummary}"
                : "https://wa.me/?text={$paymentSummary}";
            $emailBody = str_replace('%0A', "\n", urldecode($paymentSummary));
        @endphp

        <div class="mb-6 text-left">
            <h3 class="text-sm font-semibold text-slate-700 mb-3">Share payment details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 font-medium">
                    <i class="fab fa-whatsapp mr-2 text-lg"></i>Open WhatsApp
                </a>
                <a href="mailto:{{ $event->user->email ?? '' }}?subject={{ rawurlencode('Payment Confirmation - ' . $event->title) }}&body={{ rawurlencode($emailBody) }}"
                   class="inline-flex items-center justify-center px-4 py-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 font-medium {{ empty($event->user->email) ? 'pointer-events-none opacity-60' : '' }}">
                    <i class="fas fa-envelope mr-2"></i>Open email draft
                </a>
            </div>
            <p class="text-xs text-slate-500 mt-2">WhatsApp opens a pre-filled chat — tap send there.</p>
            @if (empty($event->user->email))
                <p class="text-xs text-amber-700 mt-2">Email draft is unavailable because organiser email is missing.</p>
            @endif
        </div>

        @if (! empty($emailStatus))
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl mb-6 text-left">
                <p class="text-sm text-slate-700 font-medium mb-1">Automatic email status</p>
                <p class="text-sm text-slate-600">Guest: {{ $emailStatus['giver_sent'] ? 'Sent' : 'Not sent' }}</p>
                <p class="text-sm text-slate-600">Organiser: {{ $emailStatus['organizer_sent'] ? 'Sent' : 'Not sent' }}</p>
            </div>
        @endif

        <button type="button" onclick="window.close()"
                class="bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-8 py-3 rounded-xl hover:from-indigo-500 hover:to-violet-500 font-semibold shadow-md">
            <i class="fas fa-times mr-2"></i>Close
        </button>
    </div>
</div>
@endsection

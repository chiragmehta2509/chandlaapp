@php
    $seoTitle = 'Payment link unavailable — Chandla Book';
    $seoRobots = 'noindex, nofollow';
@endphp
@extends('layouts.public-guest')

@section('content')
<div class="max-w-md mx-auto pt-8">
    <div class="rounded-2xl bg-white text-slate-800 shadow-2xl shadow-slate-950/40 ring-1 ring-white/60 p-8 text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 text-amber-700">
            <i class="fas fa-link-slash text-2xl"></i>
        </div>
        <h1 class="text-xl font-bold text-slate-900">Payment page unavailable</h1>
        <p class="text-slate-600 mt-3 text-sm leading-relaxed">
            This organiser has not unlocked the Direct GPay QR feature yet, or the event is not active.
            Please contact them if you were invited to pay here.
        </p>
        @isset($eventTitle)
            <p class="text-xs text-slate-400 mt-6 border-t border-slate-100 pt-4">Event: {{ $eventTitle }}</p>
        @endisset
        <p class="mt-8">
            <a href="{{ route('public.home') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm font-semibold hover:from-indigo-500 hover:to-violet-500">
                Chandla Book home
            </a>
        </p>
    </div>
</div>
@endsection

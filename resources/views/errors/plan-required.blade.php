@extends('layouts.client')

@section('title', 'Upgrade Required')

@section('content')
<div class="max-w-lg mx-auto py-12 px-4 text-center">

    {{-- Icon --}}
    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-amber-50 border border-amber-100 shadow-sm">
        <i class="fas fa-lock text-3xl text-amber-500"></i>
    </div>

    {{-- Heading --}}
    <h1 class="text-2xl font-extrabold text-cb-navy mb-2">Plan Upgrade Required</h1>
    <p class="text-slate-500 text-sm leading-relaxed mb-6">
        This feature is available on the
        <strong class="text-cb-navy">{{ $requiredPlan }}</strong>
        and above.
        Your current plan is
        <strong class="text-slate-600">{{ config('packs.level_names.' . $userLevel, 'Starter Plan') }}</strong>.
    </p>

    {{-- Plan comparison strip --}}
    <div class="flex items-center justify-center gap-4 mb-8">
        {{-- Current plan --}}
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-center min-w-[110px]">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Your Plan</p>
            <p class="text-sm font-bold text-slate-700">{{ config('packs.level_names.' . $userLevel, 'Starter Plan') }}</p>
        </div>
        <i class="fas fa-arrow-right text-slate-300 text-lg"></i>
        {{-- Required plan --}}
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-center min-w-[110px] shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-widest text-amber-600 mb-1">Required</p>
            <p class="text-sm font-bold text-amber-700">{{ $requiredPlan }}</p>
        </div>
    </div>

    {{-- CTA --}}
    <a href="{{ $upgradeUrl }}" class="cb-btn cb-btn-gold justify-center px-8 py-3 text-base font-bold shadow-md w-full sm:w-auto">
        <i class="fas fa-rocket mr-2"></i> View Plans & Upgrade
    </a>

    <div class="mt-4">
        <a href="{{ route('client.dashboard') }}" class="text-sm text-slate-500 hover:text-cb-navy transition-colors">
            <i class="fas fa-arrow-left text-xs mr-1"></i> Back to Dashboard
        </a>
    </div>

</div>
@endsection

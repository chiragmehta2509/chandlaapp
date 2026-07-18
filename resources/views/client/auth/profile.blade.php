@extends('layouts.client')

@section('title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto w-full min-w-0">
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-5">
            <div class="flex h-12 w-12 sm:h-14 sm:w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500/15 to-violet-600/10 text-indigo-700 ring-1 ring-indigo-200/60 shadow-sm"
                 aria-hidden="true">
                <i class="fas fa-user text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="cb-page-title text-2xl sm:text-3xl leading-tight">My Profile</h1>
                <p class="cb-subtitle mt-1.5 max-w-xl text-sm sm:text-base leading-relaxed">
                    Your account information.
                </p>
            </div>
        </div>
    </div>

    <div class="cb-card overflow-hidden border border-slate-200/80 shadow-sm rounded-2xl">
        <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 via-white to-indigo-50/20">
            <h2 class="text-sm font-bold text-cb-navy tracking-wide">Account details</h2>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">Your current login information.</p>
        </div>

        <div class="divide-y divide-slate-100">
            {{-- Full Name --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5">
                <div class="sm:w-40 shrink-0">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                        <i class="fas fa-id-card w-4 text-center text-slate-400" aria-hidden="true"></i>
                        Full Name
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 break-words">{{ $user->name }}</p>
                </div>
            </div>

            {{-- Email --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5">
                <div class="sm:w-40 shrink-0">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                        <i class="fas fa-envelope w-4 text-center text-slate-400" aria-hidden="true"></i>
                        Email
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    @if($user->email)
                        <p class="text-sm font-semibold text-slate-900 break-all">{{ $user->email }}</p>
                    @else
                        <p class="text-sm text-slate-400 italic">Not set</p>
                    @endif
                </div>
            </div>

            {{-- Phone --}}
            @if($user->phone)
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5">
                <div class="sm:w-40 shrink-0">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                        <i class="fas fa-phone w-4 text-center text-slate-400" aria-hidden="true"></i>
                        Phone
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900">{{ $user->phone }}</p>
                </div>
            </div>
            @endif

            {{-- Account Type --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 px-4 py-4 sm:px-6 sm:py-5">
                <div class="sm:w-40 shrink-0">
                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                        <i class="fas fa-shield-halved w-4 text-center text-slate-400" aria-hidden="true"></i>
                        Account Type
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    @if($user->parent_id)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                            <i class="fas fa-users text-[0.6rem]" aria-hidden="true"></i>
                            Family Member
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                            <i class="fas fa-star text-[0.6rem]" aria-hidden="true"></i>
                            Main Account
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 p-4 sm:p-6 bg-slate-50/40 border-t border-slate-100">
            <a href="{{ route('client.password.edit') }}"
               class="cb-btn cb-btn-ghost w-full sm:w-auto justify-center min-h-[2.75rem] touch-manipulation">
                <i class="fas fa-key text-sm opacity-80" aria-hidden="true"></i>
                Change Password
            </a>
            <a href="{{ route('client.dashboard') }}"
               class="cb-btn cb-btn-gold inline-flex items-center gap-2 w-full sm:w-auto justify-center min-h-[2.75rem] shadow-md touch-manipulation">
                <i class="fas fa-house text-sm opacity-90" aria-hidden="true"></i>
                Go to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

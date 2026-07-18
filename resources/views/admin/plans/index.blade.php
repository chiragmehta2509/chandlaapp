@extends('layouts.admin')

@section('title', 'Plan Management')

@section('content')
<div class="p-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Subscription Plans</h1>
            <p class="text-sm text-gray-500 mt-1">Overview of all plan tiers, subscriber counts, and revenue.</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total Revenue</p>
            <p class="text-2xl font-extrabold text-indigo-700">₹{{ number_format($totalRevenue, 0) }}</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            <i class="fas fa-check-circle mr-1.5"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            <i class="fas fa-exclamation-circle mr-1.5"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Plans Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach($plans as $plan)
        @php
            $levelColors = [
                0 => ['bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'badge' => 'bg-slate-100 text-slate-600', 'icon' => 'fa-seedling text-slate-400'],
                1 => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'badge' => 'bg-indigo-100 text-indigo-700', 'icon' => 'fa-wand-magic-sparkles text-indigo-500'],
                2 => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'badge' => 'bg-amber-100 text-amber-700', 'icon' => 'fa-qrcode text-amber-500'],
                3 => ['bg' => 'bg-sky-50', 'border' => 'border-sky-200', 'badge' => 'bg-sky-100 text-sky-700', 'icon' => 'fa-book-open text-sky-500'],
                4 => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'badge' => 'bg-purple-100 text-purple-700', 'icon' => 'fa-shield-halved text-purple-500'],
                5 => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'badge' => 'bg-emerald-100 text-emerald-700', 'icon' => 'fa-gem text-emerald-500'],
                6 => ['bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'badge' => 'bg-rose-100 text-rose-700', 'icon' => 'fa-briefcase text-rose-500'],
                7 => ['bg' => 'bg-zinc-800', 'border' => 'border-zinc-700', 'badge' => 'bg-amber-500 text-white', 'icon' => 'fa-building text-amber-300'],
            ];
            $c = $levelColors[$plan['level']] ?? $levelColors[0];
        @endphp
        <div class="rounded-2xl border {{ $c['border'] }} {{ $c['bg'] }} p-5 shadow-sm flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5 rounded-full {{ $c['badge'] }} text-xs font-bold px-2.5 py-1">
                    <i class="fas {{ $c['icon'] }} text-[10px]"></i>
                    Level {{ $plan['level'] }}
                </span>
                @if($plan['amount_inr'] > 0)
                <span class="text-sm font-bold text-gray-700">₹{{ number_format($plan['amount_inr'], 0) }}</span>
                @else
                <span class="text-xs text-gray-400">Free</span>
                @endif
            </div>
            <div>
                <h3 class="font-bold text-gray-800 text-sm">{{ $plan['name'] }}</h3>
                @if($plan['description'])
                <p class="text-xs text-gray-500 mt-0.5 leading-snug">{{ Str::limit($plan['description'], 60) }}</p>
                @endif
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-2xl font-extrabold text-gray-800">{{ number_format($plan['subscriber_count']) }}</p>
                    <p class="text-xs text-gray-400">subscribers</p>
                </div>
                @if($plan['revenue'] > 0)
                <div class="text-right">
                    <p class="text-sm font-bold text-emerald-600">₹{{ number_format($plan['revenue'], 0) }}</p>
                    <p class="text-xs text-gray-400">revenue</p>
                </div>
                @endif
            </div>
            @if($plan['level'] > 0 && $plan['subscriber_count'] > 0)
            <a href="{{ route('admin.plans.subscribers', $plan['level']) }}"
               class="mt-auto inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                <i class="fas fa-users text-[10px]"></i> View subscribers
            </a>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Manual Grant/Revoke Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Grant Plan --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-800 mb-1">
                <i class="fas fa-user-plus text-emerald-500 mr-2"></i>Grant Plan to User
            </h2>
            <p class="text-xs text-gray-500 mb-4">Manually activate a plan tier for a user (admin override). All lower plans are also granted.</p>
            <form method="POST" action="{{ route('admin.plans.grant') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">User ID or Search</label>
                        <input type="number" name="user_id" placeholder="Enter User ID" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Plan Level to Grant</label>
                        <select name="level" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            @foreach($plans as $plan)
                                @if($plan['level'] > 0)
                                <option value="{{ $plan['level'] }}">{{ $plan['level'] }} — {{ $plan['name'] }} @if($plan['amount_inr'] > 0)(₹{{ number_format($plan['amount_inr'], 0) }})@endif</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg px-4 py-2 text-sm font-semibold transition-colors">
                        <i class="fas fa-check mr-1.5"></i> Grant Plan
                    </button>
                </div>
            </form>
        </div>

        {{-- Revoke Plan --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-800 mb-1">
                <i class="fas fa-user-minus text-rose-500 mr-2"></i>Revoke Plan from User
            </h2>
            <p class="text-xs text-gray-500 mb-4">Nullify a plan tier (and all higher tiers) for a user. This is a downgrade action.</p>
            <form method="POST" action="{{ route('admin.plans.revoke') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">User ID</label>
                        <input type="number" name="user_id" placeholder="Enter User ID" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Revoke From Level (and all higher)</label>
                        <select name="level" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-300">
                            @foreach($plans as $plan)
                                @if($plan['level'] > 0)
                                <option value="{{ $plan['level'] }}">{{ $plan['level'] }} — {{ $plan['name'] }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        onclick="return confirm('Are you sure you want to revoke this plan and all higher-tier plans from this user?')"
                        class="w-full bg-rose-600 hover:bg-rose-700 text-white rounded-lg px-4 py-2 text-sm font-semibold transition-colors">
                        <i class="fas fa-ban mr-1.5"></i> Revoke Plan
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection

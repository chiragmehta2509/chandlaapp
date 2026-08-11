@extends('layouts.admin')

@section('title', $planName . ' Subscribers')

@section('content')
<div class="p-6">

    {{-- Breadcrumb --}}
    <div class="mb-4">
        <a href="{{ route('admin.plans.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors">
            <i class="fas fa-arrow-left mr-1 text-xs"></i> Back to Plans
        </a>
    </div>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $planName }} Subscribers</h1>
            <p class="text-sm text-gray-500 mt-1">
                Users whose highest active plan is <strong>{{ $planName }}</strong>
                (Plan Level {{ $level }}).
            </p>
        </div>
        <span class="text-3xl font-extrabold text-indigo-700">{{ $users->total() }}</span>
    </div>

    {{-- Subscribers Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="data-table min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">User</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Email / Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Plan Activated</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Registered</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                @php
                    // Find the activation date for this plan level
                    $levelCol = [
                        1 => 'celebration_pack_paid_at',
                        2 => null,
                        3 => 'ledger_duo_pack_paid_at',
                        4 => 'family_pack_paid_at',
                        5 => 'premium_bundle_paid_at',
                        6 => 'professional_pack_paid_at',
                        7 => 'enterprise_pack_paid_at',
                    ][$level] ?? null;
                    $activatedAt = $levelCol ? $user->$levelCol : null;
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs shrink-0">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $user->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">ID: {{ $user->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-sm text-gray-700">{{ $user->email ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $user->phone ?? '—' }}</p>
                    </td>
                    <td class="px-4 py-3">
                        @if($activatedAt)
                        <p class="text-sm text-emerald-700 font-semibold">{{ $activatedAt->format('d M Y') }}</p>
                        <p class="text-xs text-gray-400">{{ $activatedAt->diffForHumans() }}</p>
                        @elseif($level === 2)
                        <p class="text-sm text-amber-600">{{ $user->guest_pay_single_event_credits }} credit(s)</p>
                        @else
                        <p class="text-xs text-gray-400">—</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.users.show', $user->id) }}"
                           class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                        No subscribers found at this plan level.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>


    </div>

</div>
@endsection

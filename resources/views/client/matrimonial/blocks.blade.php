@extends('layouts.client')

@section('title', 'Interest privacy')

@section('content')
<div class="w-full min-w-0 max-w-2xl mx-auto">
    <a href="{{ route('client.matrimonial.interests.index') }}" class="cb-link text-sm inline-flex items-center gap-2 mb-3 sm:mb-4 min-h-[44px] touch-manipulation">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Interests
    </a>
    <h1 class="cb-page-title">Interest privacy</h1>
    <p class="cb-subtitle mt-1 break-words leading-relaxed">Control who can send you interest and manage people you’ve blocked.</p>

    <div class="cb-card p-4 sm:p-5 mt-5 space-y-3">
        <h2 class="text-sm font-bold text-cb-navy">Allow new interest requests</h2>
        <p class="text-sm text-slate-600 break-words">This is the same as the switch on <a href="{{ route('client.matrimonial.profile.edit') }}" class="text-cb-navy font-medium underline">your profile</a>. When it’s off, no one can start a new interest request to you.</p>
        <p class="text-sm">
            <span class="font-medium">Current:</span>
            @if($profile?->interests_receiving_enabled ?? true)
                <span class="text-emerald-700">On — you are accepting new requests</span>
            @else
                <span class="text-amber-800">Off — you are not accepting new requests</span>
            @endif
        </p>
    </div>

    <h2 class="text-base sm:text-lg font-bold text-cb-navy mt-8 mb-2.5">Blocked members</h2>
    <p class="text-sm text-slate-600 mb-4 break-words">These members cannot send you interest. You can add someone from a received interest using <strong>Block from sending me interest</strong> on the <a href="{{ route('client.matrimonial.interests.index') }}" class="text-cb-navy underline">Interests</a> page.</p>

    @forelse($blocks as $b)
        <div class="cb-card p-3.5 sm:p-4 mb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 min-w-0">
            <div class="min-w-0">
                <p class="font-semibold text-slate-900 break-words">{{ $b->blockedUser->matrimonialProfile?->display_name ?? $b->blockedUser->name }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Blocked {{ $b->created_at->format('d/m/Y') }}</p>
            </div>
            <form method="post" action="{{ route('client.matrimonial.interest-blocks.remove', $b->blocked_user_id) }}" class="w-full sm:w-auto shrink-0">
                @csrf
                <button type="submit" class="w-full sm:w-auto cb-btn border border-slate-200 cb-btn--sm min-h-[2.75rem] touch-manipulation">Unblock</button>
            </form>
        </div>
    @empty
        <p class="text-slate-600 text-sm">Nobody is on your block list yet.</p>
    @endforelse
</div>
@endsection

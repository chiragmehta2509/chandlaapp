@extends('layouts.client')

@section('title', 'Interests')

@section('content')
<div class="w-full min-w-0 max-w-3xl mx-auto">
    <a href="{{ route('client.matrimonial.index') }}" class="cb-link text-sm inline-flex items-center gap-2 mb-3 sm:mb-4 min-h-[44px] touch-manipulation">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Find Partner
    </a>
    <h1 class="cb-page-title">Interests</h1>
    <p class="cb-subtitle mt-1 break-words leading-relaxed">Accept or decline when you have an active paid plan. You can block someone from sending you interest at any time.</p>
    <p class="mt-2 text-sm">
        <a href="{{ route('client.matrimonial.interest-privacy') }}" class="font-medium text-cb-navy hover:underline touch-manipulation">Interest privacy &amp; blocked list →</a>
    </p>
    @if(!$viewerHasPlan)
        <p class="mt-2 text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2.5 break-words">To send or respond to interests, <a href="{{ route('client.matrimonial.plans') }}" class="font-semibold underline">upgrade to a plan</a>. You can still block people below.</p>
    @endif

    <h2 class="text-base sm:text-lg font-bold text-cb-navy mt-6 sm:mt-8 mb-2.5 sm:mb-3">Received</h2>
    @forelse($received as $r)
        @php $isBlocked = in_array($r->from_user_id, $blockedFromMeIds, true); @endphp
        <div class="cb-card p-3.5 sm:p-4 mb-3 flex flex-col gap-3 min-w-0 sm:flex-row sm:items-stretch sm:justify-between sm:gap-4">
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-slate-900 break-words">{{ $r->fromUser->matrimonialProfile?->display_name ?? $r->fromUser->name }}</p>
                <p class="text-sm text-slate-600 mt-0.5">Status: <span class="font-medium">{{ $r->status }}</span></p>
                @if($isBlocked)
                    <p class="mt-2 text-xs text-slate-500">You’ve blocked this member from sending you interest. <a href="{{ route('client.matrimonial.interest-privacy') }}" class="text-cb-navy underline">Manage</a></p>
                @else
                    <form method="post" action="{{ route('client.matrimonial.interest-blocks.store') }}" class="mt-2" onsubmit="return confirm('They will not be able to send you new interest until you unblock them. Pending requests from them will be declined. Continue?');">
                        @csrf
                        <input type="hidden" name="blocked_user_id" value="{{ $r->from_user_id }}">
                        <button type="submit" class="text-sm font-medium text-rose-700 hover:text-rose-800 hover:underline touch-manipulation">Block from sending me interest</button>
                    </form>
                @endif
            </div>
            @if($r->status === 'pending' && $viewerHasPlan)
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto shrink-0 sm:min-w-0">
                    <form method="post" action="{{ route('client.matrimonial.interests.accept', $r->id) }}" class="w-full sm:w-auto min-w-0 sm:min-w-[6.5rem]">
                        @csrf
                        <button type="submit" class="cb-btn cb-btn-navy cb-btn--sm w-full min-h-[2.75rem] touch-manipulation">Accept</button>
                    </form>
                    <form method="post" action="{{ route('client.matrimonial.interests.reject', $r->id) }}" class="w-full sm:w-auto min-w-0 sm:min-w-[6.5rem]">
                        @csrf
                        <button type="submit" class="cb-btn border border-slate-200 cb-btn--sm w-full min-h-[2.75rem] touch-manipulation">Decline</button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <p class="text-slate-600 text-sm">No interests received yet.</p>
    @endforelse

    <h2 class="text-base sm:text-lg font-bold text-cb-navy mt-6 sm:mt-8 mb-2.5 sm:mb-3">Sent</h2>
    @forelse($sent as $s)
        <div class="cb-card p-3.5 sm:p-4 mb-3 min-w-0">
            <p class="font-semibold text-slate-900 break-words">{{ $s->toUser->matrimonialProfile?->display_name ?? $s->toUser->name }}</p>
            <p class="text-sm text-slate-600 mt-0.5">Status: <span class="font-medium">{{ $s->status }}</span></p>
        </div>
    @empty
        <p class="text-slate-600 text-sm">You have not sent any interests yet.</p>
    @endforelse
</div>
@endsection

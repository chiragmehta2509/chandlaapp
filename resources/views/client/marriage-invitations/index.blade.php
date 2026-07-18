@extends('layouts.client')

@section('title', 'Marriage invitation')

@php
    $price = number_format((float) config('marriage_invitations.amount', 300), 0);
    $templateCount = count($templates);
    $packPrice = number_format((float) config('packs.celebration.amount_inr', 300), 0);
    $latestPaid = $latestInvitation && $latestInvitation->exportsUnlockedForUser();
@endphp

@section('content')
<div class="mb-5 sm:mb-6 flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-start">
    <div>
        <h1 class="cb-page-title">Marriage invitation</h1>
        <p class="cb-subtitle max-w-2xl">One form — <strong>{{ $templateCount }} print styles</strong> inspired by modern invites (think Canva / Pinterest). The <strong>celebration pack (₹{{ $packPrice }})</strong> unlocks every layout, video, and pre‑wedding — or pay <strong>₹{{ $price }}</strong> once for this card only with <strong>Razorpay</strong>. Edit anytime.</p>
    </div>
    <div class="flex flex-col sm:items-end gap-2 w-full sm:w-auto">
        @if($latestInvitation)
            <a href="{{ route('client.marriage-invitations.edit', $latestInvitation->id) }}"
               class="cb-btn cb-btn--gold w-full sm:w-auto justify-center text-center px-6 py-3 shadow-md">
                <i class="fas fa-pen-to-square" aria-hidden="true"></i> Edit invitation
            </a>
            <a href="{{ route('client.marriage-invitations.show', $latestInvitation->id) }}"
               class="cb-btn cb-btn--navy w-full sm:w-auto justify-center text-center border-2 border-transparent">
                @if($latestPaid)
                    <i class="fas fa-eye" aria-hidden="true"></i> View &amp; styles
                @else
                    <i class="fas fa-file-lines" aria-hidden="true"></i> Details &amp; payment
                @endif
            </a>
            <p class="text-xs text-slate-500 text-center sm:text-right max-w-xs">You have one invitation; all <strong>{{ $templateCount }} looks</strong> use the <strong>same</strong> details.</p>
        @else
            <a href="{{ route('client.marriage-invitations.create') }}"
               class="cb-btn cb-btn--gold w-full sm:w-auto justify-center text-center px-8 py-3.5 text-base shadow-md ring-1 ring-amber-400/40">
                <i class="fas fa-plus" aria-hidden="true"></i> Create your invitation
            </a>
            @if($showDemoThumbnails)
                <a href="{{ route('client.packs.celebration.pay') }}" data-loader="payment" class="cb-btn cb-btn--navy w-full sm:w-auto justify-center text-center">
                    <i class="fas fa-arrow-up-right-from-square text-xs" aria-hidden="true"></i> Pay ₹{{ $packPrice }} celebration pack (Razorpay)
                </a>
            @endif
            <p class="text-xs text-slate-500 text-center sm:text-right">Takes a few minutes · ₹{{ $price }} for this card on Razorpay, or the ₹{{ $packPrice }} celebration pack</p>
        @endif
    </div>
</div>

@if($latestInvitation)
    @php
        $prevData = $latestInvitation->data ?? [];
        $prevCouple = trim(($prevData['groom_name'] ?? '') . ' & ' . ($prevData['bride_name'] ?? ''));
        $prevDate = '';
        if (!empty($prevData['wedding_date'])) {
            try {
                $prevDate = \Carbon\Carbon::parse($prevData['wedding_date'])->format('j M Y');
            } catch (\Throwable $e) {
                $prevDate = (string) $prevData['wedding_date'];
            }
        }
        $previewLocked = !$latestInvitation->exportsUnlockedForUser();
    @endphp
    <div class="cb-card mb-6 sm:mb-8 overflow-hidden border border-slate-200/90 dark:border-slate-700/80 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-5 sm:gap-6 p-5 sm:p-6 bg-gradient-to-br from-white via-slate-50/80 to-amber-50/30 dark:from-slate-800 dark:via-slate-800/90 dark:to-slate-800/50">
            @include('client.marriage-invitations.partials.invitation-thumb', ['invitation' => $latestInvitation, 'size' => 'lg'])
            <div class="min-w-0 flex-1 flex flex-col justify-center">
                <p class="text-[0.65rem] font-bold uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400 mb-1">Your invitation</p>
                <h2 class="text-lg sm:text-xl font-bold text-cb-navy dark:text-white leading-snug">{{ $prevCouple !== '' ? $prevCouple : 'Invitation saved' }}</h2>
                @if($prevDate !== '')
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1"><i class="fas fa-calendar-day text-cb-gold/90 mr-1" aria-hidden="true"></i>{{ $prevDate }}</p>
                @endif
                @if($previewLocked)
                    <div class="mt-4 rounded-xl border border-amber-300/70 bg-amber-50/95 px-4 py-3 text-sm text-amber-950 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200 leading-relaxed">
                        <p class="font-semibold flex items-center gap-2"><i class="fas fa-lock" aria-hidden="true"></i> Payment required for full previews &amp; downloads</p>
                        <p class="mt-1.5 text-amber-900/90 dark:text-amber-300/90">You can review details and edit copy below. <strong>Open</strong>, <strong>PNG</strong>, print, and video stay locked until payment is confirmed. Use Razorpay with the <strong>same email or phone</strong> as this account.</p>
                    </div>
                    <div class="mt-4 flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
                        <a href="{{ route('client.marriage-invitations.payment', $latestInvitation->id) }}" data-loader="payment" class="cb-btn cb-btn-gold justify-center shadow-md">
                            <i class="fas fa-lock-open text-xs" aria-hidden="true"></i>
                            Pay ₹{{ $price }} with Razorpay
                        </a>
                        <a href="{{ route('client.marriage-invitations.show', $latestInvitation->id) }}" class="cb-btn cb-btn--ghost justify-center border border-slate-200 bg-white">
                            View details
                        </a>
                    </div>
                @else
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('client.marriage-invitations.show', $latestInvitation->id) }}" class="cb-btn cb-btn-navy justify-center shadow-md">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            Open styles &amp; downloads
                        </a>
                        <a href="{{ route('client.marriage-invitations.edit', $latestInvitation->id) }}" class="cb-btn cb-btn--ghost justify-center border border-slate-200 bg-white">
                            Edit invitation
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

@if($latestInvitation)
    @if(! $latestInvitation->exportsUnlockedForUser())
        <div class="mb-8 flex flex-wrap items-start gap-3 rounded-xl border border-amber-200 bg-amber-50/95 px-4 py-3 text-sm text-amber-950 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200">
            <i class="fas fa-circle-info text-amber-600 dark:text-amber-500 mt-0.5 shrink-0" aria-hidden="true"></i>
            <span><strong>Tip:</strong> Use <strong>Edit invitation</strong> to change wording. Full card previews and downloads unlock after payment — see buttons above.</span>
        </div>
    @else
        <div class="mb-8 flex flex-wrap items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-200">
            <i class="fas fa-circle-check text-emerald-600 dark:text-emerald-500" aria-hidden="true"></i>
            <span><strong>Next:</strong> <strong>Edit</strong> to change details, or <strong>View &amp; styles</strong> to preview all {{ $templateCount }} designs.</span>
        </div>
    @endif
@endif

@if(!$latestInvitation)
    <div class="mb-8 rounded-2xl border border-amber-200/90 dark:border-amber-900/50 bg-gradient-to-br from-amber-50/90 via-white to-amber-50/40 dark:from-amber-950/40 dark:via-slate-900 dark:to-amber-950/20 p-5 sm:p-6">
        <p class="text-xs font-bold uppercase tracking-wider text-amber-800/90 dark:text-amber-500/90 mb-1">Start here</p>
        <h2 class="text-lg sm:text-xl font-bold text-cb-navy dark:text-white mb-2">Build your card once</h2>
        <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl">We ask for couple names, families, date &amp; venue, then you can switch between <strong class="dark:text-white">{{ $templateCount }} visual themes</strong> — heritage gold, minimal, coastal, festive, and more. No second form.</p>
    </div>
@endif

<div class="mb-3 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
    <h2 class="text-lg font-bold text-cb-navy dark:text-white">Print styles</h2>
    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $templateCount }} looks · one shared form</span>
</div>

@if($latestInvitation && !$latestPaid)
    {{-- Invitation saved but not paid / verified — no template actions here --}}
    <div class="cb-card mb-10 overflow-hidden border border-amber-200/90 dark:border-amber-900/50 bg-gradient-to-br from-amber-50/90 to-white dark:from-amber-950/40 dark:to-slate-900">
        <div class="px-5 py-4 border-b border-amber-100 dark:border-amber-900/50 flex items-center gap-3">
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200"><i class="fas fa-lock" aria-hidden="true"></i></span>
            <div>
                <p class="font-bold text-cb-navy dark:text-white">Templates unlock after payment</p>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">You’ll see each style here with <strong class="dark:text-white">Open</strong> and <strong class="dark:text-white">PNG</strong> once payment is confirmed for this card.</p>
            </div>
        </div>
        <div class="px-5 py-4 text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
            Complete payment using the buttons in <strong class="dark:text-white">Your invitation</strong> above — then return to this page for thumbnails and downloads.
        </div>
    </div>
@elseif($latestPaid)
    {{-- Paid / unlocked — thumbnails + Open + PNG --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5 mb-10">
        @foreach($templates as $key => $t)
            @php
                $thumbSrc = route('client.marriage-invitations.download', $latestInvitation->id).'?layout='.urlencode($key);
                $thumbTitle = ($t['name'] ?? $key).' — your invitation preview';
            @endphp
            <div class="cb-card p-0 overflow-hidden border border-slate-200/80 dark:border-slate-700/80 flex flex-col shadow-sm hover:shadow-md transition-shadow">
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700/80 flex items-center gap-3 bg-slate-50/80 dark:bg-slate-800/80">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-sm font-bold {{ $t['badge_class'] ?? 'bg-slate-200 text-slate-800' }}">{{ $t['badge'] ?? substr($key, 0, 1) }}</span>
                    <h3 class="text-base font-bold text-cb-navy dark:text-white leading-tight">{{ $t['name'] }}</h3>
                </div>
                <div class="shrink-0">
                    @include('client.marriage-invitations.partials.template-thumb-iframe', ['thumbSrc' => $thumbSrc, 'thumbTitle' => $thumbTitle])
                </div>
                <div class="p-4 sm:p-5 flex-1 flex flex-col gap-3">
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed flex-1">{{ $t['description'] ?? '' }}</p>
                    <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/80">
                        <a href="{{ route('client.marriage-invitations.download', $latestInvitation->id) }}?layout={{ urlencode($key) }}" target="_blank" rel="noopener noreferrer" class="cb-btn cb-btn--navy cb-btn--sm flex-1 min-w-[6.5rem] justify-center">
                            <i class="fas fa-external-link-alt text-xs" aria-hidden="true"></i> Open
                        </a>
                        <a href="{{ route('client.marriage-invitations.export.png', $latestInvitation->id) }}?layout={{ urlencode($key) }}" target="_blank" rel="noopener noreferrer" class="cb-btn cb-btn-gold cb-btn--sm flex-1 min-w-[6.5rem] justify-center">
                            <i class="fas fa-download text-xs" aria-hidden="true"></i> PNG
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    {{-- No invitation yet — browse styles as marketing only --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5 mb-10">
        @if($showDemoThumbnails)
            @include('client.marriage-invitations.partials.demo-style-cards', ['templates' => $templates])
        @else
            @foreach($templates as $key => $t)
                @php
                    $params = ['layout' => $key];
                    if (isset($latestInvitation) && $latestInvitation) {
                        $params['invitation_id'] = $latestInvitation->id;
                    }
                    $thumbSrc = route('client.marriage-invitations.template-demo', $params);
                    $thumbTitle = ($t['name'] ?? $key).' — demo preview';
                @endphp
                <div class="cb-card p-0 overflow-hidden border border-slate-200/80 dark:border-slate-700/80 flex flex-col shadow-sm hover:shadow-md transition-shadow">
                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700/80 flex items-center gap-3 bg-slate-50/80 dark:bg-slate-800/80">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-sm font-bold {{ $t['badge_class'] ?? 'bg-slate-200 text-slate-800' }}">{{ $t['badge'] ?? substr($key, 0, 1) }}</span>
                        <h3 class="text-base font-bold text-cb-navy dark:text-white leading-tight">{{ $t['name'] }}</h3>
                    </div>
                    <div class="w-full overflow-hidden shrink-0">
                        @include('client.marriage-invitations.partials.template-thumb-iframe', ['thumbSrc' => $thumbSrc, 'thumbTitle' => $thumbTitle])
                    </div>
                    <div class="p-4 sm:p-5 flex-1 flex flex-col">
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed flex-1">{{ $t['description'] }}</p>
                        <p class="text-xs text-amber-800/80 dark:text-amber-500/80 mt-4 pt-4 border-t border-amber-100/80 dark:border-amber-900/50">Available after you <strong class="dark:text-white">create your invitation</strong> and <strong class="dark:text-white">complete payment</strong>.</p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endif
<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-3">
    <h2 class="text-lg font-bold text-cb-navy dark:text-white">Your saved invitations</h2>
    @if(!$invitations->isEmpty())
        <span class="text-sm text-slate-500 dark:text-slate-400">{{ $invitations->total() }} total</span>
    @endif
</div>
@if($invitations->isEmpty())
    <div class="cb-card p-10 sm:p-14 text-center border-2 border-dashed border-slate-200 bg-slate-50/50 dark:border-slate-700 dark:bg-slate-800/50">
        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200 text-2xl mb-4" aria-hidden="true">
            <i class="fas fa-envelope-open-text"></i>
        </div>
        <h3 class="text-lg font-bold text-cb-navy dark:text-white mb-2">No invitation yet</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm max-w-md mx-auto mb-6">Create your card once, then choose any of <strong class="dark:text-white">{{ $templateCount }} styles</strong> when you view or save.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center items-stretch sm:items-center">
            <a href="{{ route('client.marriage-invitations.create') }}"
               class="cb-btn cb-btn--gold inline-flex justify-center px-8 py-3 text-base">
                <i class="fas fa-plus" aria-hidden="true"></i> Create your invitation
            </a>
            @if($showDemoThumbnails)
                <a href="{{ route('client.packs.celebration.pay') }}" data-loader="payment" class="cb-btn cb-btn--navy inline-flex justify-center px-8 py-3 text-base">
                    Pay ₹{{ $packPrice }} pack
                </a>
            @endif
        </div>
    </div>
@else
    <div class="cb-card overflow-hidden border border-slate-200/80 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-100">
                        <th class="px-4 sm:px-5 py-3.5 w-14 sm:w-16"></th>
                        <th class="px-4 sm:px-5 py-3.5">Couple</th>
                        <th class="px-4 sm:px-5 py-3.5 hidden sm:table-cell">Styles</th>
                        <th class="px-4 sm:px-5 py-3.5">Status</th>
                        <th class="px-4 sm:px-5 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invitations as $inv)
                        @php
                            $dDisp = \App\Support\MarriageInvitationCard::mergeUserDataWithDemoDefaults($inv->data ?? []);
                            $couple = trim(($dDisp['groom_name'] ?? '') . ' & ' . ($dDisp['bride_name'] ?? ''));
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 sm:px-5 py-3.5 align-middle">
                                @include('client.marriage-invitations.partials.invitation-thumb', ['invitation' => $inv])
                            </td>
                            <td class="px-4 sm:px-5 py-3.5 font-medium text-slate-800">{{ $couple ?: '—' }}</td>
                            <td class="px-4 sm:px-5 py-3.5 text-slate-500 hidden sm:table-cell">{{ $templateCount }} themes</td>
                            <td class="px-4 sm:px-5 py-3.5">
                                @if($inv->isUnlocked())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5">Paid · ready</span>
                                @elseif($inv->exportsUnlockedForUser())
                                    @if(auth()->user()->hasCelebrationPackAccess())
                                        <span class="inline-flex items-center gap-1 rounded-full bg-indigo-100 text-indigo-900 text-xs font-semibold px-2.5 py-0.5">Celebration pack</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-900 text-xs font-semibold px-2.5 py-0.5 ring-1 ring-emerald-200/80">Unlocked</span>
                                    @endif
                                @elseif($inv->hasPendingPayment())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-0.5">Payment pending</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium px-2.5 py-0.5">Not paid</span>
                                @endif
                            </td>
                            <td class="px-4 sm:px-5 py-3.5 text-right align-middle">
                                <div class="flex flex-col items-end gap-1.5">
                                    <a href="{{ route('client.marriage-invitations.show', $inv->id) }}" class="cb-btn cb-btn--navy text-sm py-1.5 px-3 whitespace-nowrap">View</a>
                                    @if(! $inv->exportsUnlockedForUser())
                                        <a href="{{ route('client.marriage-invitations.payment', $inv->id) }}" class="text-xs font-semibold text-amber-800 hover:text-amber-950 underline underline-offset-2">Pay on Razorpay</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50 text-sm text-slate-500">{{ $invitations->links() }}</div>
    </div>
@endif

@if($latestInvitation)
    <a href="{{ route('client.marriage-invitations.edit', $latestInvitation->id) }}"
       class="cb-fab"
       title="Edit invitation"
       aria-label="Edit invitation">
        <i class="fas fa-pen"></i>
    </a>
@else
    @if($showDemoThumbnails)
        <a href="{{ route('client.packs.celebration.pay') }}" data-loader="payment"
           class="cb-fab !bg-cb-navy"
           title="Pay celebration pack"
           aria-label="Pay celebration pack">
            <i class="fas fa-gift"></i>
        </a>
    @else
        <a href="{{ route('client.marriage-invitations.create') }}"
           class="cb-fab"
           title="Create invitation"
           aria-label="Create invitation">
            <i class="fas fa-plus"></i>
        </a>
    @endif
@endif
@endsection

@push('styles')
<style>
@keyframes cb-spin    { to { transform: rotate(360deg); } }
@keyframes cb-shimmer { 0%,100% { background-position:0% 50%; } 50% { background-position:100% 50%; } }
</style>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';

    var EAGER_COUNT = 3;   // load first N immediately (fills first grid row)
    var STAGGER_MS  = 400; // delay between each subsequent card

    function revealIframe(iframe, skeleton) {
        iframe.style.opacity = '1';
        if (skeleton) {
            skeleton.style.transition = 'opacity 0.4s';
            skeleton.style.opacity = '0';
            setTimeout(function () {
                if (skeleton.parentNode) skeleton.parentNode.removeChild(skeleton);
            }, 420);
        }
    }

    function loadIframe(wrap, onDone) {
        if (wrap.dataset.cbLoaded) { if (onDone) onDone(); return; }
        wrap.dataset.cbLoaded = '1';

        var iframe   = wrap.querySelector('.cb-lazy-iframe');
        var skeleton = wrap.querySelector('.cb-iframe-skeleton');
        if (!iframe || !iframe.dataset.src) { if (onDone) onDone(); return; }

        iframe.src = iframe.dataset.src;
        delete iframe.dataset.src;

        var done = false;
        function finish() {
            if (done) return;
            done = true;
            revealIframe(iframe, skeleton);
            if (onDone) onDone();
        }

        iframe.addEventListener('load',  finish, { once: true });
        iframe.addEventListener('error', finish, { once: true });
        // Fallback: unblock the queue if load never fires
        setTimeout(finish, 5000);
    }

    function loadSequentially(wraps, index) {
        if (index >= wraps.length) return;
        loadIframe(wraps[index], function () {
            setTimeout(function () {
                loadSequentially(wraps, index + 1);
            }, STAGGER_MS);
        });
    }

    function init() {
        var wraps = Array.from(document.querySelectorAll('.cb-lazy-iframe-wrap'));
        if (!wraps.length) return;

        // Eager: first row loads in parallel immediately
        wraps.slice(0, EAGER_COUNT).forEach(function (w) { loadIframe(w, null); });

        // Staggered: remaining load one-by-one automatically, no scroll needed
        var remaining = wraps.slice(EAGER_COUNT);
        if (!remaining.length) return;

        setTimeout(function () {
            loadSequentially(remaining, 0);
        }, 600);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endpush

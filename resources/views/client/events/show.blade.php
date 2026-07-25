@extends('layouts.client')

@section('title', $event->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('client.events.index') }}" class="cb-link text-sm inline-flex items-center gap-2 mb-3">
        <i class="fas fa-arrow-left"></i>Back
    </a>
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-start">
        <h1 class="cb-page-title pr-2">{{ $event->title }}</h1>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto shrink-0">
            <a href="{{ route('client.vendors.index', ['event_id' => $event->id]) }}" class="cb-btn cb-btn-gold w-full sm:w-auto justify-center">
                <i class="fas fa-store"></i> Find Vendors
            </a>
            <a href="{{ route('client.events.edit', $event->id) }}" class="cb-btn cb-btn-ghost w-full sm:w-auto justify-center border-slate-200">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-6 items-start">
    <div class="lg:col-span-2 space-y-5 lg:space-y-6">
        {{-- Event Hero Card --}}
        <div class="cb-card p-5 sm:p-6 sm:p-8 bg-gradient-to-br from-white via-slate-50/80 to-amber-50/30 dark:from-slate-800 dark:via-slate-800/90 dark:to-slate-800/50 border border-slate-200/90 dark:border-slate-700/80 shadow-sm relative overflow-hidden">
            {{-- Decorative accent --}}
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 rounded-full bg-gradient-to-br from-amber-200/30 to-amber-100/10 dark:from-amber-900/20 dark:to-amber-950/5 blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1">
                    <p class="text-[0.65rem] font-bold uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400 mb-1.5 flex items-center gap-2">
                        Event Information
                        @if($event->is_archived)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.6rem] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 tracking-normal uppercase">Archived</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.6rem] font-bold bg-emerald-100/80 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 tracking-normal uppercase relative"><span class="absolute -left-1 w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>Active</span>
                        @endif
                    </p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-cb-navy dark:text-white leading-tight mb-2">{{ $event->title }}</h2>
                    @if($event->description)
                        <p class="text-sm text-slate-600 dark:text-slate-300 max-w-2xl leading-relaxed">{{ $event->description }}</p>
                    @endif
                </div>
                
                @if($event->eventType)
                    <div class="shrink-0">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm" style="background-color: {{ $event->eventType->color ?? '#e2e8f0' }}20; color: {{ $event->eventType->color ?? '#475569' }}; border: 1px solid {{ $event->eventType->color ?? '#e2e8f0' }}40;">
                            @if($event->eventType->color)
                                <span class="h-2 w-2 rounded-full mr-2" style="background-color: {{ $event->eventType->color }}"></span>
                            @endif
                            {{ $event->eventType->name }}
                        </span>
                    </div>
                @endif
            </div>

            <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 sm:mt-8 pt-5 border-t border-slate-100 dark:border-slate-700/60">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0 text-slate-500 dark:text-slate-400">
                        <i class="fas fa-calendar-alt text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Date &amp; Time</p>
                        <p class="text-sm font-semibold text-cb-navy dark:text-white">{{ $event->event_date->format('d/m/Y') }} @if($event->event_time) <span class="text-slate-400 font-normal ml-1">{{ $event->event_time->format('h:i A') }}</span> @endif</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0 text-slate-500 dark:text-slate-400">
                        <i class="fas fa-map-marker-alt text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Venue</p>
                        <p class="text-sm font-semibold text-cb-navy dark:text-white">{{ $event->venue ?: 'Not specified' }}</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0 text-slate-500 dark:text-slate-400">
                        <i class="fas fa-wallet text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">UPI ID</p>
                        <p class="text-sm font-semibold text-cb-navy dark:text-white break-all">{{ $event->upi_id ?: 'Not provided' }}</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0 text-slate-500 dark:text-slate-400">
                        <i class="fab fa-google-pay text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Direct GPay</p>
                        <p class="mt-0.5">
                            @if($event->hasDirectGpayQrUnlocked())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-bold bg-emerald-100/80 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 uppercase"><i class="fas fa-check mr-1.5"></i>Unlocked</span>
                            @elseif($event->hasDirectGpayUnlockPending())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-bold bg-yellow-100/80 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400 uppercase"><i class="fas fa-clock mr-1.5"></i>Pending</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 uppercase"><i class="fas fa-lock mr-1.5"></i>Locked</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        @php
            $chandlaCount = $event->chandlas->count();
            $freeLimit = min((int) ($event->free_entry_limit ?? 50), 50);
            $freeRemaining = max(0, $freeLimit - $chandlaCount);
            
            $plan = $event->pricing_plan ?? 'free';
            if (Auth::user()->hasLedgerUnlimitedChandla()) {
                $plan = 'account_unlimited';
            }

            $extraEntries = max(0, $chandlaCount - $freeLimit);
            $perEntryPrice = $event->per_entry_price ?? 1;
            $unlimitedPrice = $event->unlimited_price ?? 500;
            $paygFee = $extraEntries * $perEntryPrice;
        @endphp
        
        <div class="cb-card p-0 border border-slate-200/90 dark:border-slate-700/80 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/50 flex items-center justify-between">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Plan &amp; Usage</h3>
                @if($plan === 'unlimited' || $plan === 'account_unlimited')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-bold bg-amber-100/80 text-amber-800 dark:bg-amber-900/40 dark:text-amber-400 uppercase ring-1 ring-amber-200/50 dark:ring-amber-800/50"><i class="fas fa-crown mr-1.5"></i>Premium</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-bold bg-slate-200/80 text-slate-700 dark:bg-slate-700 dark:text-slate-300 uppercase">Free Tier</span>
                @endif
            </div>
            
            <div class="p-5 flex-1 flex flex-col gap-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Current Plan</p>
                    <p class="text-lg font-bold text-cb-navy dark:text-white">
                        @if($plan === 'unlimited')
                            Unlimited <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline">(Event)</span>
                        @elseif($plan === 'account_unlimited')
                            Unlimited <span class="text-sm font-normal text-slate-500 dark:text-slate-400 block sm:inline">(Account)</span>
                        @elseif($plan === 'payg')
                            Pay-as-you-go
                        @else
                            Free Limit
                        @endif
                    </p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <p class="text-[0.65rem] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-0.5">Entries Used</p>
                        <p class="text-xl font-bold text-cb-navy dark:text-white">{{ $chandlaCount }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <p class="text-[0.65rem] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-0.5">Free Limit</p>
                        <p class="text-xl font-bold text-cb-navy dark:text-white">{{ $freeLimit }} <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 block xl:inline mt-1 xl:mt-0 xl:ml-1">({{ $freeRemaining }} left)</span></p>
                    </div>
                </div>
                
                @if($plan === 'payg')
                    <div class="bg-red-50/80 dark:bg-red-900/20 p-3 rounded-xl border border-red-100 dark:border-red-900/30">
                        <p class="text-[0.65rem] font-bold uppercase tracking-wider text-red-500 dark:text-red-400 mb-0.5">Extra Entries Fee</p>
                        <p class="text-lg font-bold text-red-700 dark:text-red-300">₹{{ number_format($paygFee, 2) }}</p>
                    </div>
                @elseif($plan === 'unlimited')
                    <div class="bg-amber-50/80 dark:bg-amber-900/20 p-3 rounded-xl border border-amber-100 dark:border-amber-900/30">
                        <p class="text-[0.65rem] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-500 mb-0.5">Unlimited Plan Price</p>
                        <p class="text-lg font-bold text-amber-700 dark:text-amber-300">₹{{ number_format($unlimitedPrice, 2) }}</p>
                    </div>
                @endif
                
                @if(isset($pendingPlanPayment))
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-900/50 text-yellow-800 dark:text-yellow-300 p-3 rounded-xl text-xs leading-relaxed flex gap-2">
                        <i class="fas fa-clock mt-0.5 shrink-0"></i>
                        <p>Unlimited plan payment submitted <span class="font-mono text-[0.65rem] opacity-70 block">(Txn: {{ $pendingPlanPayment->transaction_id }})</span> Waiting for verification.</p>
                    </div>
                @endif
            </div>

            @if($plan === 'free' || $plan === 'payg')
                <div class="p-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700/80 flex flex-col gap-2 mt-auto">
                    @if(Auth::user()->free_event_credits > 0)
                        <form method="POST" action="{{ route('client.events.plan.update', $event->id) }}">
                            @csrf
                            <input type="hidden" name="pricing_plan" value="unlimited">
                            <input type="hidden" name="use_credit" value="1">
                            <button type="submit" class="cb-btn w-full justify-center text-sm min-h-[2.5rem] bg-teal-600 hover:bg-teal-700 text-white shadow-sm border-0 transition-transform active:scale-[0.98]">
                                <i class="fas fa-ticket text-teal-200"></i>
                                Use Free Credit ({{ Auth::user()->free_event_credits }} left)
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('client.events.plan.payment', ['id' => $event->id, 'plan' => 'unlimited']) }}" class="cb-btn w-full justify-center text-sm min-h-[2.5rem] bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm border-0 transition-transform active:scale-[0.98]">
                        <i class="fas fa-unlock-keyhole text-emerald-200"></i>
                        Buy Unlimited (₹{{ number_format($unlimitedPrice, 2) }})
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Stats Strip (Full-width) --}}
<div class="cb-stat-strip-6 w-full shadow-sm border border-slate-200/90 dark:border-slate-700/80 mt-6">
    <div class="cb-stat-strip-6__cell">
        <div class="cb-stat-strip-6__icon cb-stat-strip-6__icon--sky">
            <i class="fas fa-users"></i>
        </div>
        <div class="cb-stat-strip-6__body">
            <div class="cb-stat-strip-6__val text-cb-navy dark:text-white !overflow-visible !whitespace-normal !text-base">{{ $event->chandlas->count() }}</div>
            <div class="cb-stat-strip-6__label">Total Chandlas</div>
        </div>
    </div>
    
    <div class="cb-stat-strip-6__cell">
        <div class="cb-stat-strip-6__icon cb-stat-strip-6__icon--gold">
            <i class="fas fa-rupee-sign"></i>
        </div>
        <div class="cb-stat-strip-6__body">
            <div class="cb-stat-strip-6__val text-cb-navy dark:text-white !overflow-visible !whitespace-normal !text-base">₹{{ number_format($event->chandlas->sum('amount'), 2) }}</div>
            <div class="cb-stat-strip-6__label">Collected</div>
        </div>
    </div>
    
    <div class="cb-stat-strip-6__cell">
        <div class="cb-stat-strip-6__icon cb-stat-strip-6__icon--slate">
            <i class="fas fa-clock"></i>
        </div>
        <div class="cb-stat-strip-6__body">
            <div class="cb-stat-strip-6__val text-cb-navy dark:text-white !overflow-visible !whitespace-normal !text-sm">{{ $event->created_at->format('d/m/Y') }}</div>
            <div class="cb-stat-strip-6__label">Created</div>
        </div>
    </div>
</div>

{{-- Chandlas table — full width --}}
<div class="cb-card p-4 sm:p-6 mt-6">
    <div class="flex flex-col gap-3 sm:gap-4 mb-4">
        <h2 class="text-lg font-bold text-cb-navy">Chandlas ({{ $event->chandlas->count() }})</h2>
        <div class="flex flex-wrap gap-2 items-center">
            <a href="{{ route('client.qrcode.show', $event->id) }}" class="cb-btn cb-btn--sm text-white bg-violet-600 hover:opacity-95 border-0">
                <i class="fas fa-qrcode"></i>QR
            </a>
            <a href="{{ route('client.events.chandlas.pdf', $event->id) }}" data-no-loader class="cb-btn cb-btn--sm text-white bg-slate-600 hover:opacity-95 border-0">
                <i class="fas fa-chart-bar"></i>Ledger PDF
            </a>
            <a href="{{ route('client.expenses.pdf', ['event_id' => $event->id]) }}" data-no-loader class="cb-btn cb-btn--sm text-white bg-rose-600 hover:opacity-95 border-0" target="_blank">
                <i class="fas fa-file-pdf"></i>Expense PDF
            </a>
            <a href="{{ route('client.cash-inventory.show', $event->id) }}" class="cb-btn cb-btn--sm text-white bg-amber-600 hover:opacity-95 border-0">
                <i class="fas fa-coins"></i>Cash
            </a>
            <a href="{{ route('client.gpay.upload', ['event_id' => $event->id]) }}" class="cb-btn cb-btn--sm text-white bg-emerald-600 hover:opacity-95 border-0">
                <i class="fas fa-camera"></i>GPay
            </a>
            <a href="{{ route('client.chandlas.create', ['event_id' => $event->id]) }}" class="cb-btn cb-btn-gold cb-btn--sm">
                <i class="fas fa-plus"></i>Add
            </a>
            @if($event->chandlas->count() > 0)
                <div class="relative max-w-xs w-full sm:w-60">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm" aria-hidden="true">
                        <i class="fas fa-search"></i>
                    </span>
                    <input
                        id="chandla-table-search"
                        type="search"
                        placeholder="Search chandlas…"
                        autocomplete="off"
                        class="cb-field w-full pl-9 pr-3 min-h-[2.25rem] text-sm"
                        oninput="chandlaTableFilter(this.value)"
                    >
                </div>
            @endif
        </div>
    </div>
    @if($event->chandlas->count() > 0)
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table id="chandla-table" class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giver</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody id="chandla-tbody" class="divide-y divide-gray-200">
                    @foreach($event->chandlas as $chandla)
                        <tr class="chandla-row hover:bg-slate-50/60 transition-colors {{ $chandla->payment_method === 'gpay' ? 'bg-teal-50/60' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-slate-800">{{ $chandla->received_date->format('d/m/Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $chandla->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-slate-800 font-semibold">{{ $chandla->giver_name }}</div>
                                @if($chandla->giver_phone)
                                    <div class="text-xs text-slate-500">{{ $chandla->giver_phone }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800">
                                    {{ $chandla->category_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($chandla->category === 'gift')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                                        {{ $chandla->gift_item_name ?: '—' }}
                                    </span>
                                @elseif($chandla->payment_method === 'gpay')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800">
                                        <i class="fas fa-mobile-screen-button text-[0.6rem]"></i> GPay
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        {{ $chandla->payment_method_label }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-cb-navy">
                                @if($chandla->category === 'gift')
                                    @if($chandla->gift_received)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-check text-[0.6rem]"></i> Given
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            <i class="fas fa-xmark text-[0.6rem]"></i> Not Given
                                        </span>
                                    @endif
                                @elseif($chandla->payment_method === 'gpay')
                                    <span class="text-teal-700">₹{{ number_format($chandla->amount, 2) }}</span>
                                @else
                                    ₹{{ number_format($chandla->amount, 2) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-gray-900">Total:</td>
                        <td class="px-4 py-3 text-sm font-bold text-gray-900">₹{{ number_format($event->chandlas->sum('amount'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            <p id="chandla-no-results" class="hidden text-center text-sm text-slate-500 py-6">No results found.</p>
        </div>
        <script>
        function chandlaTableFilter(q) {
            var rows = document.querySelectorAll('#chandla-tbody .chandla-row');
            var term = q.toLowerCase().trim();
            var visible = 0;
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                var show = term === '' || text.includes(term);
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            var noRes = document.getElementById('chandla-no-results');
            if (noRes) noRes.classList.toggle('hidden', visible > 0);
        }
        </script>
    @else
        <p class="text-gray-500 text-center py-4">No chandlas recorded yet</p>
    @endif
</div>  </div>
</div>
@endsection

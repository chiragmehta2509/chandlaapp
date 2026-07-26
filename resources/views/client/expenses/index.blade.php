@extends('layouts.client')

@section('title', 'Expenses')

@section('content')

{{-- Header --}}
<div class="mb-5 sm:mb-6 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-start">
    <div>
        <h1 class="cb-page-title">Expense Management</h1>
        <p class="cb-subtitle">Track event-wise expenses — food, decoration, music and more</p>
    </div>
    @canEdit
    <a href="{{ route('client.expenses.create') }}" class="cb-btn cb-btn-gold w-full sm:w-auto justify-center shrink-0">
        <i class="fas fa-plus"></i>Add Expense
    </a>
    @endcanEdit
</div>

@php
    $totalAmount   = $expenses->sum('amount');
    $cashAmount    = $expenses->where('payment_method', 'cash')->sum('amount');
    $gpayAmount    = $expenses->where('payment_method', 'gpay')->sum('amount');
    $totalEntries  = $expenses->count();
    $topCategory   = $categoryTotals->sortDesc()->keys()->first();
    $topCatAmount  = $categoryTotals->sortDesc()->first() ?? 0;
@endphp

{{-- Cash Flow Summary Card --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    {{-- Cash In --}}
    <div class="cb-card p-5 flex items-center gap-4 border-l-4 border-emerald-500">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
            <i class="fas fa-arrow-down text-xl text-emerald-600"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-0.5">Cash In</p>
            <p class="text-2xl font-bold text-emerald-600">₹{{ number_format($cashIn, 0) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Cash received (chandlas)</p>
        </div>
    </div>
    {{-- Cash Out --}}
    <div class="cb-card p-5 flex items-center gap-4 border-l-4 border-red-500">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
            <i class="fas fa-arrow-up text-xl text-red-500"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-0.5">Cash Out</p>
            <p class="text-2xl font-bold text-red-500">₹{{ number_format($cashOut, 0) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Cash expenses paid</p>
        </div>
    </div>
    {{-- Net Balance --}}
    <div class="cb-card p-5 flex items-center gap-4 border-l-4 {{ $cashBalance >= 0 ? 'border-cb-gold' : 'border-red-600' }}">
        <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $cashBalance >= 0 ? 'bg-amber-50' : 'bg-red-50' }} flex items-center justify-center">
            <i class="fas fa-scale-balanced text-xl {{ $cashBalance >= 0 ? 'text-cb-gold' : 'text-red-600' }}"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-0.5">Net Cash Balance</p>
            <p class="text-2xl font-bold {{ $cashBalance >= 0 ? 'text-cb-gold' : 'text-red-600' }}">
                {{ $cashBalance >= 0 ? '+' : '' }}₹{{ number_format(abs($cashBalance), 0) }}
            </p>
            <p class="text-xs text-slate-400 mt-0.5">{{ $cashBalance >= 0 ? 'Cash surplus' : 'Cash deficit' }}</p>
        </div>
    </div>
</div>

{{-- Stat Strip --}}
<div class="cb-stat-strip-6 mb-6">
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon cb-stat-strip-6__icon--gold" aria-hidden="true">
            <i class="fas fa-indian-rupee-sign"></i>
        </span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val text-cb-gold">₹{{ number_format($totalAmount, 0) }}</p>
            <p class="cb-stat-strip-6__label">Total spent</p>
        </div>
    </div>
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon cb-stat-strip-6__icon--green" aria-hidden="true">
            <i class="fas fa-money-bill-wave"></i>
        </span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val text-emerald-600">₹{{ number_format($cashAmount, 0) }}</p>
            <p class="cb-stat-strip-6__label">Cash</p>
        </div>
    </div>
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon" style="background:#d1fae5;color:#065f46;" aria-hidden="true">
            <i class="fas fa-mobile-screen-button"></i>
        </span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val" style="color:#065f46;">₹{{ number_format($gpayAmount, 0) }}</p>
            <p class="cb-stat-strip-6__label">GPay</p>
        </div>
    </div>
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon cb-stat-strip-6__icon--sky" aria-hidden="true">
            <i class="fas fa-receipt"></i>
        </span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val text-cb-navy">{{ $totalEntries }}</p>
            <p class="cb-stat-strip-6__label">Entries</p>
        </div>
    </div>
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon cb-stat-strip-6__icon--rose" aria-hidden="true">
            <i class="fas fa-tag"></i>
        </span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val text-cb-navy capitalize">{{ $topCategory ?? '—' }}</p>
            <p class="cb-stat-strip-6__label">Top category</p>
        </div>
    </div>
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon" style="background:#ede9fe;color:#5b21b6;" aria-hidden="true">
            <i class="fas fa-calendar-days"></i>
        </span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val" style="color:#5b21b6;">
                {{ $expenses->pluck('event_id')->unique()->count() }}
            </p>
            <p class="cb-stat-strip-6__label">Events</p>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="cb-card p-4 sm:p-6 mb-6">
    <form method="GET" action="{{ route('client.expenses.index') }}"
          class="flex flex-wrap items-end gap-3 sm:gap-4">
        <div class="w-full sm:w-auto flex-1 min-w-[160px] max-w-[220px]">
            <span class="cb-label mb-1 block text-xs">Event</span>
            <select name="event_id" class="cb-field w-full">
                <option value="">All events</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                        {{ $event->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-auto flex-1 min-w-[140px] max-w-[180px]">
            <span class="cb-label mb-1 block text-xs">Category</span>
            <select name="category" class="cb-field w-full">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                        {{ ucfirst($cat) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-auto flex-1 min-w-[140px] max-w-[180px]">
            <span class="cb-label mb-1 block text-xs">Payment</span>
            <select name="payment_method" class="cb-field w-full">
                <option value="">All payments</option>
                <option value="cash"          {{ request('payment_method') === 'cash'          ? 'selected' : '' }}>Cash</option>
                <option value="gpay"          {{ request('payment_method') === 'gpay'          ? 'selected' : '' }}>GPay</option>
                <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="cheque"        {{ request('payment_method') === 'cheque'        ? 'selected' : '' }}>Cheque</option>
                <option value="other"         {{ request('payment_method') === 'other'         ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div class="w-full sm:w-auto flex-1 min-w-[130px] max-w-[160px]">
            <span class="cb-label mb-1 block text-xs">From date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="cb-field w-full">
        </div>
        <div class="w-full sm:w-auto flex-1 min-w-[130px] max-w-[160px]">
            <span class="cb-label mb-1 block text-xs">To date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="cb-field w-full">
        </div>
        <div class="w-full sm:w-auto shrink-0 flex gap-2">
            <button type="submit" class="cb-btn cb-btn-navy justify-center">
                <i class="fas fa-filter"></i>Apply
            </button>
            <a href="{{ route('client.expenses.pdf', request()->only(['event_id','category','payment_method','from_date','to_date'])) }}"
               class="cb-btn cb-btn--outline justify-center border-slate-200" target="_blank" title="Download PDF">
                <i class="fas fa-file-pdf text-red-500"></i> PDF
            </a>
            @if(request()->hasAny(['event_id','category','payment_method','from_date','to_date']))
                <a href="{{ route('client.expenses.index') }}" class="cb-btn cb-btn--outline justify-center">
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

{{-- DataTables styles --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    #expenseTable_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 6px 12px; font-size: 0.875rem; outline: none;
        transition: border-color .2s; min-width: 240px; max-width: 100%;
    }
    #expenseTable_wrapper .dataTables_filter input:focus { border-color: #b8860b; }
    #expenseTable_wrapper .dataTables_length select {
        border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 4px 8px; font-size: 0.875rem;
    }
    #expenseTable_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important; padding: 4px 10px !important;
        margin: 0 2px; font-size: 0.8rem;
    }
    #expenseTable_wrapper .dataTables_paginate .paginate_button.current {
        background: #1a3646 !important; border-color: #1a3646 !important; color: #fff !important;
    }
    #expenseTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
        background: #f1f5f9 !important; border-color: #e2e8f0 !important; color: #1a3646 !important;
    }
    #expenseTable_wrapper .dataTables_info { font-size: 0.8rem; color: #64748b; }
    table#expenseTable thead th { cursor: pointer; user-select: none; }
</style>

<div class="cb-table-wrap">
    <div class="overflow-x-auto">
        <table id="expenseTable" class="cb-table min-w-full" style="width:100%">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Event</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Payment</th>
                    <th>Payee</th>
                    <th>Amount</th>
                    <th class="no-sort" style="width:110px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                <tr>
                    <td data-order="{{ $expense->expense_date?->format('Y-m-d') }}" class="whitespace-nowrap">
                        <div class="text-sm text-slate-800">{{ $expense->expense_date?->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        <div class="text-sm font-medium text-cb-navy max-w-[140px] truncate"
                             title="{{ $expense->event?->title ?? '—' }}">
                            {{ $expense->event?->title ?? '—' }}
                        </div>
                    </td>
                    <td>
                        <div class="text-sm text-slate-800">{{ $expense->title }}</div>
                        @if($expense->description)
                            <div class="text-xs text-slate-400 truncate max-w-[160px]">{{ $expense->description }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 capitalize">
                            {{ $expense->category }}
                        </span>
                    </td>
                    <td>
                        @php
                            $pmColors = [
                                'cash'          => 'bg-emerald-100 text-emerald-800',
                                'gpay'          => 'bg-teal-100 text-teal-800',
                                'bank_transfer' => 'bg-blue-100 text-blue-800',
                                'cheque'        => 'bg-amber-100 text-amber-800',
                                'other'         => 'bg-slate-100 text-slate-600',
                            ];
                            $pmColor = $pmColors[$expense->payment_method] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pmColor }} capitalize">
                            {{ str_replace('_', ' ', $expense->payment_method) }}
                        </span>
                    </td>
                    <td>
                        @if($expense->payee_name)
                            <div class="text-sm text-slate-800">{{ $expense->payee_name }}</div>
                            @if($expense->payee_phone)
                                <div class="text-xs text-slate-400">{{ $expense->payee_phone }}</div>
                            @endif
                        @else
                            <span class="text-slate-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap text-sm font-bold text-cb-navy" data-order="{{ $expense->amount }}">
                        ₹{{ number_format($expense->amount, 2) }}
                    </td>
                    <td class="whitespace-nowrap">
                        <div class="flex items-center justify-center gap-3">
                            <a href="{{ route('client.expenses.show', $expense->id) }}"
                               class="text-cb-gold hover:opacity-80" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @canEdit
                            <a href="{{ route('client.expenses.edit', $expense->id) }}"
                               class="text-sky-600 hover:opacity-80" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcanEdit
                            @canDelete
                            <form action="{{ route('client.expenses.destroy', $expense->id) }}" method="POST"
                                  class="inline" onsubmit="return confirm('Delete this expense?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:opacity-80" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endcanDelete
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     CASH LEDGER — Cash In vs Cash Out
═══════════════════════════════════════════════════════════════════════ --}}
<div class="mt-8 mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
            <i class="fas fa-book-open text-slate-500 text-sm"></i>
        </div>
        <h2 class="text-base font-bold text-cb-navy">Cash Ledger</h2>
        <span class="text-xs text-slate-400 font-normal">Cash In vs Cash Out — all cash transactions</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- ── CASH IN TABLE ──────────────────────────────────────────── --}}
        <div class="cb-card overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100"
                 style="background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%)">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-500 flex items-center justify-center">
                        <i class="fas fa-arrow-down text-white text-xs"></i>
                    </span>
                    <span class="font-bold text-emerald-700 text-sm">Cash In</span>
                    <span class="text-xs text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full font-medium">
                        {{ $cashInEntries->count() }} entries
                    </span>
                </div>
                <span class="text-lg font-extrabold text-emerald-700">
                    ₹{{ number_format($cashIn, 0) }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50">
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Event</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">From (Giver)</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-emerald-600 uppercase tracking-wide">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($cashInEntries as $ci)
                        <tr class="hover:bg-emerald-50/40 transition-colors">
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-600">
                                {{ $ci->received_date?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="text-cb-navy font-medium text-xs max-w-[110px] block truncate"
                                      title="{{ $ci->event?->title ?? '—' }}">
                                    {{ $ci->event?->title ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="text-slate-700 font-medium">{{ $ci->giver_name ?? '—' }}</div>
                                @if($ci->giver_phone)
                                    <div class="text-xs text-slate-400">{{ $ci->giver_phone }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right font-bold text-emerald-600 whitespace-nowrap">
                                +₹{{ number_format($ci->amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 text-sm">
                                <i class="fas fa-inbox text-slate-300 text-2xl mb-2 block"></i>
                                No cash received yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($cashInEntries->count())
                    <tfoot>
                        <tr class="border-t-2 border-emerald-200 bg-emerald-50">
                            <td colspan="3" class="px-4 py-2.5 text-xs font-bold text-emerald-700 uppercase">Total Cash In</td>
                            <td class="px-4 py-2.5 text-right font-extrabold text-emerald-700 text-base">
                                ₹{{ number_format($cashIn, 0) }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- ── CASH OUT TABLE ──────────────────────────────────────────── --}}
        @php $cashOutExpenses = $expenses->where('payment_method', 'cash'); @endphp
        <div class="cb-card overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100"
                 style="background:linear-gradient(135deg,#fff1f2 0%,#ffe4e6 100%)">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-red-500 flex items-center justify-center">
                        <i class="fas fa-arrow-up text-white text-xs"></i>
                    </span>
                    <span class="font-bold text-red-600 text-sm">Cash Out</span>
                    <span class="text-xs text-red-600 bg-red-100 px-2 py-0.5 rounded-full font-medium">
                        {{ $cashOutExpenses->count() }} entries
                    </span>
                </div>
                <span class="text-lg font-extrabold text-red-600">
                    ₹{{ number_format($cashOut, 0) }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50">
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Event</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Title / Payee</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-red-500 uppercase tracking-wide">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($cashOutExpenses as $co)
                        <tr class="hover:bg-red-50/40 transition-colors">
                            <td class="px-4 py-2.5 whitespace-nowrap text-slate-600">
                                {{ $co->expense_date?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-2.5">
                                <span class="text-cb-navy font-medium text-xs max-w-[110px] block truncate"
                                      title="{{ $co->event?->title ?? '—' }}">
                                    {{ $co->event?->title ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="text-slate-700 font-medium">{{ $co->title }}</div>
                                @if($co->payee_name)
                                    <div class="text-xs text-slate-400">{{ $co->payee_name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right font-bold text-red-500 whitespace-nowrap">
                                -₹{{ number_format($co->amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 text-sm">
                                <i class="fas fa-inbox text-slate-300 text-2xl mb-2 block"></i>
                                No cash expenses yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($cashOutExpenses->count())
                    <tfoot>
                        <tr class="border-t-2 border-red-200 bg-red-50">
                            <td colspan="3" class="px-4 py-2.5 text-xs font-bold text-red-600 uppercase">Total Cash Out</td>
                            <td class="px-4 py-2.5 text-right font-extrabold text-red-600 text-base">
                                ₹{{ number_format($cashOut, 0) }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>{{-- /grid --}}

    {{-- Net Balance Banner --}}
    <div class="mt-4 rounded-xl px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3
                {{ $cashBalance >= 0 ? 'bg-amber-50 border border-amber-200' : 'bg-red-50 border border-red-200' }}">
        <div class="flex items-center gap-3">
            <i class="fas fa-scale-balanced text-xl {{ $cashBalance >= 0 ? 'text-amber-500' : 'text-red-500' }}"></i>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide {{ $cashBalance >= 0 ? 'text-amber-600' : 'text-red-600' }}">
                    Net Cash Balance
                </p>
                <p class="text-xs text-slate-500 mt-0.5">
                    Cash In (₹{{ number_format($cashIn, 0) }}) − Cash Out (₹{{ number_format($cashOut, 0) }})
                </p>
            </div>
        </div>
        <p class="text-2xl font-extrabold {{ $cashBalance >= 0 ? 'text-amber-600' : 'text-red-600' }}">
            {{ $cashBalance >= 0 ? '+' : '' }}₹{{ number_format(abs($cashBalance), 0) }}
            <span class="text-xs font-semibold ml-1">{{ $cashBalance >= 0 ? 'surplus' : 'deficit' }}</span>
        </p>
    </div>
</div>

{{-- FAB --}}
@canEdit
<a href="{{ route('client.expenses.create') }}" class="cb-fab" title="Add expense" aria-label="Add expense">
    <i class="fas fa-plus"></i>
</a>
@endcanEdit

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    $('#expenseTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        language: {
            search: '<i class="fas fa-search" style="margin-right:6px;color:#94a3b8;"></i>',
            searchPlaceholder: 'Search title, event, payee…',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ – _END_ of _TOTAL_ entries',
            infoEmpty: 'No expenses found',
            infoFiltered: '(filtered from _MAX_ total)',
            paginate: { previous: '‹', next: '›' }
        },
        columnDefs: [
            { orderable: false, targets: -1 }
        ],
        dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 sm:px-6 py-4"lf>rt<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-4 sm:px-6 py-4 border-t border-slate-100"ip>',
    });
});
</script>
@endpush

@endsection

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

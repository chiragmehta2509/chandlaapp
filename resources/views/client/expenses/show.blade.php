@extends('layouts.client')
@section('title', 'Expense Detail')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('client.expenses.index') }}" class="cb-btn cb-btn--outline cb-btn--sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 class="cb-page-title">Expense Detail</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('client.expenses.edit', $expense->id) }}" class="cb-btn cb-btn--outline cb-btn--sm">
                <i class="fa-solid fa-pencil mr-1"></i> Edit
            </a>
            <form action="{{ route('client.expenses.destroy', $expense->id) }}" method="POST"
                  onsubmit="return confirm('Delete this expense?')">
                @csrf @method('DELETE')
                <button type="submit" class="cb-btn cb-btn--sm bg-red-50 text-red-600 hover:bg-red-100 border border-red-200">
                    <i class="fa-solid fa-trash mr-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="cb-card divide-y divide-slate-100">
        {{-- Amount Hero --}}
        <div class="p-6 text-center bg-gradient-to-br from-slate-50 to-white">
            <p class="text-sm text-slate-500 mb-1">{{ $expense->expense_date?->format('d M Y') }}</p>
            <p class="text-4xl font-bold text-[var(--cb-navy)]">₹{{ number_format($expense->amount, 2) }}</p>
            <p class="text-lg font-semibold text-slate-700 mt-1">{{ $expense->title }}</p>
            <div class="flex items-center justify-center gap-2 mt-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 capitalize">
                    {{ $expense->category }}
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 capitalize">
                    {{ str_replace('_', ' ', $expense->payment_method) }}
                </span>
            </div>
        </div>

        {{-- Details --}}
        <div class="p-5 space-y-3">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Details</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Event</dt>
                    <dd class="font-medium text-slate-800">{{ $expense->event->title ?? '—' }}</dd>
                </div>
                @if($expense->transaction_id)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Transaction / Ref No.</dt>
                    <dd class="font-medium text-slate-800">{{ $expense->transaction_id }}</dd>
                </div>
                @endif
                @if($expense->receipt_number)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Receipt No.</dt>
                    <dd class="font-medium text-slate-800">{{ $expense->receipt_number }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Payee --}}
        @if($expense->payee_name || $expense->payee_phone || $expense->payee_upi)
        <div class="p-5 space-y-3">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide">Payee</h2>
            <dl class="space-y-2 text-sm">
                @if($expense->payee_name)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Name</dt>
                    <dd class="font-medium text-slate-800">{{ $expense->payee_name }}</dd>
                </div>
                @endif
                @if($expense->payee_phone)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Phone</dt>
                    <dd class="font-medium text-slate-800">{{ $expense->payee_phone }}</dd>
                </div>
                @endif
                @if($expense->payee_upi)
                <div class="flex justify-between">
                    <dt class="text-slate-500">UPI ID</dt>
                    <dd class="font-medium text-slate-800">{{ $expense->payee_upi }}</dd>
                </div>
                @endif
            </dl>
        </div>
        @endif

        {{-- Receipt Image --}}
        @if($expense->receipt_image)
        <div class="p-5">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-3">Receipt</h2>
            <a href="{{ Storage::disk('public')->url($expense->receipt_image) }}" target="_blank">
                <img src="{{ Storage::disk('public')->url($expense->receipt_image) }}"
                     alt="Receipt" class="rounded-lg max-h-64 object-contain border border-slate-200">
            </a>
        </div>
        @endif

        {{-- Notes --}}
        @if($expense->notes)
        <div class="p-5">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-2">Notes</h2>
            <p class="text-sm text-slate-700 whitespace-pre-line">{{ $expense->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection

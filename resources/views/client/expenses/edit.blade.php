@extends('layouts.client')

@section('title', 'Edit Expense')

@section('content')
<div class="w-full max-w-2xl mx-auto">
    <header class="mb-6 sm:mb-8">
        <a href="{{ route('client.expenses.index') }}"
           class="cb-link text-sm inline-flex items-center gap-2 py-2 -ml-0.5 sm:py-1 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--cb-gold)]/40">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
            <span>Back to expenses</span>
        </a>
        <h1 class="cb-page-title mt-3 sm:mt-4">Edit Expense</h1>
        <p class="cb-subtitle max-w-prose">Update expense details, payee, and payment information.</p>
    </header>

    <div class="cb-card overflow-hidden">
        <form method="POST" action="{{ route('client.expenses.update', $expense->id) }}" enctype="multipart/form-data"
              class="p-4 sm:p-6 lg:p-8">
            @csrf
            @method('PUT')

            <div class="space-y-6 sm:space-y-8">

                {{-- ── Section: Event ──────────────────────────────────── --}}
                <section aria-labelledby="exp-event-heading">
                    <h2 id="exp-event-heading" class="cb-section-label">Event</h2>
                    <div>
                        <label class="cb-label cb-label--classic" for="exp-event-select">Event *</label>
                        <select id="exp-event-select" name="event_id" required class="cb-field min-h-[48px] w-full">
                            <option value="">Select event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}"
                                    {{ old('event_id', $expense->event_id) == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }} — {{ $event->event_date?->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                        @error('event_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </section>

                {{-- ── Section: Expense Details ─────────────────────────── --}}
                <section aria-labelledby="exp-detail-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                    <h2 id="exp-detail-heading" class="cb-section-label">Expense Details</h2>
                    <div class="space-y-4 sm:space-y-5">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="cb-label cb-label--classic" for="exp-title">Title *</label>
                                <input type="text" id="exp-title" name="title"
                                       value="{{ old('title', $expense->title) }}" required maxlength="255"
                                       placeholder="e.g. Stage Decoration"
                                       class="cb-field min-h-[48px] w-full @error('title') border-red-400 @enderror">
                                @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="cb-label cb-label--classic" for="exp-category">Category *</label>
                                <select id="exp-category" name="category" required
                                        class="cb-field min-h-[48px] w-full @error('category') border-red-400 @enderror">
                                    <option value="">Select category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}"
                                            {{ old('category', $expense->category) == $cat ? 'selected' : '' }}>
                                            {{ ucfirst($cat) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="cb-label cb-label--classic" for="exp-amount">Amount (₹) *</label>
                                <input type="number" id="exp-amount" name="amount"
                                       value="{{ old('amount', $expense->amount) }}" required min="0" step="0.01"
                                       placeholder="0.00"
                                       class="cb-field min-h-[48px] w-full @error('amount') border-red-400 @enderror">
                                @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="cb-label cb-label--classic" for="exp-date">Expense Date *</label>
                                <input type="date" id="exp-date" name="expense_date"
                                       value="{{ old('expense_date', $expense->expense_date?->format('Y-m-d')) }}" required
                                       class="cb-field min-h-[48px] w-full @error('expense_date') border-red-400 @enderror">
                                @error('expense_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="cb-label cb-label--classic" for="exp-description">Description
                                <span class="text-slate-400 font-normal normal-case">(optional)</span>
                            </label>
                            <textarea id="exp-description" name="description" rows="2"
                                      placeholder="Brief description of this expense"
                                      class="cb-field w-full resize-none">{{ old('description', $expense->description) }}</textarea>
                        </div>
                    </div>
                </section>

                {{-- ── Section: Payment ─────────────────────────────────── --}}
                <section aria-labelledby="exp-payment-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                    <h2 id="exp-payment-heading" class="cb-section-label">Payment</h2>
                    <div class="space-y-4 sm:space-y-5">

                        <div>
                            <span class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                                Payment Method *
                            </span>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['cash' => 'Cash', 'gpay' => 'GPay / UPI', 'bank_transfer' => 'Bank Transfer', 'cheque' => 'Cheque', 'other' => 'Other'] as $val => $lbl)
                                <label class="gp-method-label">
                                    <input type="radio" name="payment_method" value="{{ $val }}"
                                           class="sr-only"
                                           {{ old('payment_method', $expense->payment_method) === $val ? 'checked' : '' }}>
                                    {{ $lbl }}
                                </label>
                                @endforeach
                            </div>
                            @error('payment_method') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="cb-label cb-label--classic" for="exp-txn">Transaction / Cheque No.
                                    <span class="text-slate-400 font-normal normal-case">(optional)</span>
                                </label>
                                <input type="text" id="exp-txn" name="transaction_id"
                                       value="{{ old('transaction_id', $expense->transaction_id) }}" maxlength="255"
                                       placeholder="Reference number"
                                       class="cb-field min-h-[48px] w-full">
                            </div>
                            <div>
                                <label class="cb-label cb-label--classic" for="exp-receipt-no">Receipt No.
                                    <span class="text-slate-400 font-normal normal-case">(optional)</span>
                                </label>
                                <input type="text" id="exp-receipt-no" name="receipt_number"
                                       value="{{ old('receipt_number', $expense->receipt_number) }}" maxlength="100"
                                       placeholder="Optional receipt number"
                                       class="cb-field min-h-[48px] w-full">
                            </div>
                        </div>

                        <div>
                            <label class="cb-label cb-label--classic" for="exp-receipt-img">Receipt Image
                                <span class="text-slate-400 font-normal normal-case">(optional, max 5 MB)</span>
                            </label>
                            @if($expense->receipt_image)
                                <div class="mb-2 flex items-center gap-2">
                                    <a href="{{ Storage::disk('public')->url($expense->receipt_image) }}" target="_blank"
                                       class="cb-link text-xs inline-flex items-center gap-1">
                                        <i class="fa-solid fa-image" aria-hidden="true"></i>
                                        View current receipt
                                    </a>
                                    <span class="text-xs text-slate-400">— upload a new one to replace it</span>
                                </div>
                            @endif
                            <input type="file" id="exp-receipt-img" name="receipt_image"
                                   accept="image/jpeg,image/png,image/jpg"
                                   class="cb-field w-full file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200">
                            @error('receipt_image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                {{-- ── Section: Payee / Vendor ───────────────────────────── --}}
                <section aria-labelledby="exp-payee-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                    <h2 id="exp-payee-heading" class="cb-section-label">Payee / Vendor
                        <span class="text-slate-400 font-normal normal-case text-xs">(optional)</span>
                    </h2>
                    <div class="space-y-4 sm:space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="cb-label cb-label--classic" for="exp-payee-name">Payee Name</label>
                                <input type="text" id="exp-payee-name" name="payee_name"
                                       value="{{ old('payee_name', $expense->payee_name) }}" maxlength="255"
                                       placeholder="Vendor / person paid"
                                       class="cb-field min-h-[48px] w-full">
                            </div>
                            <div>
                                <label class="cb-label cb-label--classic" for="exp-payee-phone">Payee Phone</label>
                                <input type="tel" id="exp-payee-phone" name="payee_phone"
                                       value="{{ old('payee_phone', $expense->payee_phone) }}" maxlength="30"
                                       placeholder="e.g. 98765 43210"
                                       class="cb-field min-h-[48px] w-full">
                            </div>
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="exp-payee-upi">Payee UPI ID</label>
                            <input type="text" id="exp-payee-upi" name="payee_upi"
                                   value="{{ old('payee_upi', $expense->payee_upi) }}" maxlength="255"
                                   placeholder="e.g. vendor@upi"
                                   class="cb-field min-h-[48px] w-full">
                        </div>
                    </div>
                </section>

                {{-- ── Section: Notes ───────────────────────────────────── --}}
                <section aria-labelledby="exp-notes-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                    <h2 id="exp-notes-heading" class="cb-section-label">Notes
                        <span class="text-slate-400 font-normal normal-case text-xs">(optional)</span>
                    </h2>
                    <textarea id="exp-notes" name="notes" rows="3"
                              placeholder="Any extra details…"
                              class="cb-field w-full resize-none">{{ old('notes', $expense->notes) }}</textarea>
                </section>

                {{-- ── Submit ───────────────────────────────────────────── --}}
                <div class="pt-2 sm:pt-4 border-t border-slate-200/80 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="cb-btn cb-btn--navy flex-1 justify-center">
                        <i class="fa-solid fa-save mr-2" aria-hidden="true"></i> Update Expense
                    </button>
                    <a href="{{ route('client.expenses.index') }}"
                       class="cb-btn cb-btn--outline flex-1 justify-center text-center">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

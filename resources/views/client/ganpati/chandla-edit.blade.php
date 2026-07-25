@extends('layouts.client')

@section('title', 'Edit Entry — ' . $event->title)

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('client.ganpati.show', $event->id) }}" class="gp-back-btn">
            <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
        </a>
        <div class="min-w-0">
            <p class="gp-page-label">🪔 Ganpati Special</p>
            <h1 class="gp-page-title">Edit Chanda Entry</h1>
            <p class="text-xs cb-subtitle truncate">{{ $chandla->giver_name }} · {{ $event->title }}</p>
        </div>
    </div>

    <div class="gp-form-card">
        <form method="POST" action="{{ route('client.ganpati.chandla.update', [$event->id, $chandla->id]) }}"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="giver_name" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Donor Name <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="text" id="giver_name" name="giver_name" value="{{ old('giver_name', $chandla->giver_name) }}"
                       required maxlength="255"
                       class="cb-field w-full @error('giver_name') border-red-400 @enderror">
                @error('giver_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="giver_phone" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Phone <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <input type="tel" id="giver_phone" name="giver_phone" value="{{ old('giver_phone', $chandla->giver_phone) }}"
                       maxlength="30" class="cb-field w-full @error('giver_phone') border-red-400 @enderror">
                @error('giver_phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="giver_address" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Address <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <textarea id="giver_address" name="giver_address" rows="2"
                          class="cb-field w-full resize-none @error('giver_address') border-red-400 @enderror">{{ old('giver_address', $chandla->giver_address) }}</textarea>
                @error('giver_address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <span class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Payment Method <span class="text-red-500" aria-hidden="true">*</span>
                </span>
                <div class="flex flex-wrap gap-2">
                    @foreach(['cash' => 'Cash', 'gpay' => 'GPay / UPI', 'other' => 'Other'] as $val => $lbl)
                    <label class="gp-method-label">
                        <input type="radio" name="payment_method" value="{{ $val }}" class="sr-only"
                               {{ old('payment_method', $chandla->payment_method) === $val ? 'checked' : '' }}
                               onchange="togglePaymentFields(this.value)">
                        {{ $lbl }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label for="amount" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Amount (₹) <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="number" id="amount" name="amount"
                       value="{{ old('amount', $chandla->amount) }}"
                       min="0" step="1" required
                       class="cb-field w-full @error('amount') border-red-400 @enderror">
                @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div id="gpay-section" class="mb-4 gp-gpay-section {{ old('payment_method', $chandla->payment_method) !== 'gpay' ? 'hidden' : '' }}">
                <p class="gp-gpay-section__title">GPay / UPI Details</p>
                <div class="mb-3">
                    <label for="gpay_transaction_id" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Transaction ID <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <input type="text" id="gpay_transaction_id" name="gpay_transaction_id"
                           value="{{ old('gpay_transaction_id', $chandla->gpay_transaction_id) }}"
                           maxlength="255" class="cb-field w-full">
                </div>
                <div>
                    <label for="gpay_image" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Screenshot <span class="text-slate-400 font-normal">(replace existing)</span>
                    </label>
                    @if($chandla->gpay_image)
                        <img src="{{ asset('storage/' . $chandla->gpay_image) }}" alt="GPay screenshot"
                             class="h-16 rounded-lg mb-2 object-contain bg-white dark:bg-slate-800"
                             style="border:1px solid var(--gp-border);">
                    @endif
                    <input type="file" id="gpay_image" name="gpay_image" accept="image/*" class="cb-field w-full text-sm">
                </div>
            </div>

            <div class="mb-4">
                <label for="received_date" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Date <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="date" id="received_date" name="received_date"
                       value="{{ old('received_date', optional($chandla->received_date)->format('Y-m-d')) }}"
                       required class="cb-field w-full @error('received_date') border-red-400 @enderror">
                @error('received_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label for="notes" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Notes <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <textarea id="notes" name="notes" rows="2"
                          class="cb-field w-full resize-none">{{ old('notes', $chandla->notes) }}</textarea>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('client.ganpati.show', $event->id) }}"
                   class="flex-1 flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Cancel
                </a>
                <button type="submit" class="gp-btn flex-1 py-2.5">
                    <i class="fas fa-check" aria-hidden="true"></i> Update Entry
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function togglePaymentFields(method) {
    document.getElementById('gpay-section').classList.toggle('hidden', method !== 'gpay');
}
document.addEventListener('DOMContentLoaded', function () {
    const checked = document.querySelector('input[name="payment_method"]:checked');
    if (checked) togglePaymentFields(checked.value);
});
</script>
@endpush
@endsection

@extends('layouts.client')

@section('title', 'Edit Chandla')

@section('content')
<div class="w-full max-w-2xl mx-auto">
    <header class="mb-6 sm:mb-8">
        <a
            href="{{ route('client.chandlas.show', $chandla->id) }}"
            class="cb-link text-sm inline-flex items-center gap-2 py-2 -ml-0.5 sm:py-1 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--cb-gold)]/40 focus-visible:ring-offset-2 focus-visible:ring-offset-[var(--cb-cream-2)]"
        >
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
            <span>Back to entry</span>
        </a>
        <h1 class="cb-page-title mt-3 sm:mt-4">Edit chandla</h1>
        <p class="cb-subtitle max-w-prose">Update the details for this entry.</p>
    </header>

    <div class="cb-card overflow-hidden">
        <form method="POST" action="{{ route('client.chandlas.update', $chandla->id) }}" enctype="multipart/form-data" class="p-4 sm:p-6 lg:p-8">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="space-y-6 sm:space-y-8">

                {{-- Event --}}
                <section aria-labelledby="edit-event-heading">
                    <h2 id="edit-event-heading" class="cb-section-label">Event</h2>
                    <div>
                        <label class="cb-label cb-label--classic" for="edit-event-select">Event *</label>
                        <select id="edit-event-select" name="event_id" required
                            class="cb-field min-h-[48px] w-full"
                        >
                            <option value="">Select event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}"
                                    data-upi-id="{{ $event->upi_id }}"
                                    data-gpay-qr="{{ $event->gpay_qr_image ? asset('storage/' . $event->gpay_qr_image) : '' }}"
                                    {{ old('event_id', $chandla->event_id) == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }} — {{ $event->event_date->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </section>

                {{-- Giver --}}
                <section aria-labelledby="edit-giver-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                    <h2 id="edit-giver-heading" class="cb-section-label">Giver</h2>
                    <div class="space-y-4 sm:space-y-5">
                        <div>
                            <label class="cb-label cb-label--classic" for="edit-giver-name">Giver name *</label>
                            <input type="text" name="giver_name" id="edit-giver-name"
                                value="{{ old('giver_name', $chandla->giver_name) }}"
                                required class="cb-field min-h-[48px] w-full" placeholder="Full name">
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="edit-giver-address">Address <span class="font-normal text-slate-400">(optional)</span></label>
                            <textarea name="giver_address" id="edit-giver-address" rows="2"
                                class="cb-field min-h-[4.5rem] resize-y w-full"
                                placeholder="Street, area, city…">{{ old('giver_address', $chandla->giver_address) }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label class="cb-label cb-label--classic" for="edit-giver-phone">Phone <span class="font-normal text-slate-400">(optional)</span></label>
                                <input type="text" name="giver_phone" id="edit-giver-phone"
                                    value="{{ old('giver_phone', $chandla->giver_phone) }}"
                                    class="cb-field min-h-[48px] w-full" inputmode="tel" placeholder="Phone">
                            </div>
                            <div>
                                <label class="cb-label cb-label--classic" for="edit-giver-email">Email <span class="font-normal text-slate-400">(optional)</span></label>
                                <input type="email" name="giver_email" id="edit-giver-email"
                                    value="{{ old('giver_email', $chandla->giver_email) }}"
                                    class="cb-field min-h-[48px] w-full" placeholder="Email">
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Type & Payment --}}
                <section aria-labelledby="edit-type-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                    <h2 id="edit-type-heading" class="cb-section-label">Type &amp; payment</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-4">
                        <div>
                            <label class="cb-label cb-label--classic" for="category">Type *</label>
                            <select name="category" id="category" required class="cb-field min-h-[48px] w-full">
                                <option value="chandla" {{ old('category', $chandla->category) === 'chandla' ? 'selected' : '' }}>Cash</option>
                                <option value="cover"   {{ old('category', $chandla->category) === 'cover'   ? 'selected' : '' }}>Cover</option>
                                <option value="gift"    {{ old('category', $chandla->category) === 'gift'    ? 'selected' : '' }}>Gift</option>
                            </select>
                        </div>
                        <div id="payment_method_section">
                            <label class="cb-label cb-label--classic" for="payment_method">Payment method *</label>
                            <select name="payment_method" id="payment_method" class="cb-field min-h-[48px] w-full">
                                <option value="cash" {{ old('payment_method', $chandla->payment_method) === 'cash'  ? 'selected' : '' }}>Cash</option>
                                <option value="gpay" {{ old('payment_method', $chandla->payment_method) === 'gpay'  ? 'selected' : '' }}>GPay</option>
                                <option value="other" {{ old('payment_method', $chandla->payment_method) === 'other' ? 'selected' : '' }}>N/A</option>
                            </select>
                        </div>
                    </div>

                    {{-- GPay fields --}}
                    <div class="mb-4 sm:mb-6 p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 space-y-4" id="gpay_fields"
                        style="display: {{ old('payment_method', $chandla->payment_method) === 'gpay' ? 'block' : 'none' }};">
                        <div>
                            <label class="cb-label cb-label--classic" for="gpay_image">GPay screenshot</label>
                            @if($chandla->gpay_image)
                                <div class="mb-3">
                                    <p class="text-xs text-slate-500 mb-1">Current image:</p>
                                    <img src="{{ route('client.gpay.view-image', $chandla->id) }}" alt="GPay screenshot"
                                        class="max-w-xs h-auto rounded-xl border border-slate-200 shadow-sm">
                                </div>
                            @endif
                            <input type="file" name="gpay_image" id="gpay_image" accept="image/*"
                                class="cb-field min-h-[48px] w-full file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-slate-800 file:text-white hover:file:bg-slate-700 dark:file:bg-indigo-600 dark:file:text-white dark:hover:file:bg-indigo-500 cursor-pointer">
                            <p class="text-xs text-slate-500 mt-1.5">Upload a new screenshot to replace the existing one.</p>
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="gpay_transaction_id">GPay transaction ID *</label>
                            <input type="text" name="gpay_transaction_id" id="gpay_transaction_id"
                                value="{{ old('gpay_transaction_id', $chandla->gpay_transaction_id) }}"
                                class="cb-field min-h-[48px] w-full" placeholder="UPI ref / transaction ID">
                        </div>
                        <div class="pt-2" id="gpay_qr_section" style="display: none;">
                            <label class="cb-label cb-label--classic">Pay with UPI (scan)</label>
                            <div class="inline-block p-3 rounded-xl border border-slate-200 bg-white shadow-sm">
                                <img id="gpay_qr_image" src="" alt="UPI QR code" class="h-40 w-40 sm:h-48 sm:w-48 object-contain mx-auto">
                            </div>
                            <p class="text-xs text-slate-600 mt-2">UPI ID: <span id="gpay_upi_id_text" class="font-medium text-[var(--cb-navy)]">-</span></p>
                        </div>
                    </div>

                    {{-- Gift fields --}}
                    <div id="gift_fields" class="mb-4 sm:mb-6 p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 space-y-4"
                        style="display: {{ old('category', $chandla->category) === 'gift' ? 'block' : 'none' }};">
                        <div>
                            <label class="cb-label cb-label--classic" for="gift_item_name">Gift item name *</label>
                            <input type="text" name="gift_item_name" id="gift_item_name"
                                value="{{ old('gift_item_name', $chandla->gift_item_name) }}"
                                class="cb-field min-h-[48px] w-full">
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="gift_received">Gift given to receiver? *</label>
                            <select name="gift_received" id="gift_received" class="cb-field min-h-[48px] w-full">
                                <option value="">Select</option>
                                <option value="1" {{ old('gift_received', (string) $chandla->gift_received) === '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('gift_received', (string) $chandla->gift_received) === '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    {{-- Cash notes --}}
                    <div id="cash_notes" class="mb-4 sm:mb-6 p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white" style="display: none;">
                        <label class="cb-label cb-label--classic mb-3 block">Cash notes (quantity)</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
                            @foreach([1,2,5,10,20,50,100,200,500] as $note)
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">₹{{ $note }}</label>
                                <input type="number" name="cash_note_{{ $note }}" min="0"
                                    value="{{ old('cash_note_'.$note, $chandla->{'cash_note_'.$note}) }}"
                                    class="cb-field cash-note-input min-h-[44px] w-full">
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-200/80 space-y-1">
                            <p class="text-sm font-medium text-[var(--cb-navy)]">Received total: ₹<span id="cash_received_total">0</span></p>
                            <p class="text-sm text-slate-600">Change due: ₹<span id="cash_change_due">0</span></p>
                        </div>
                    </div>

                    {{-- Amount & Date --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 pt-2" id="amount_section">
                        <div>
                            <label class="cb-label cb-label--classic" for="amount">Amount *</label>
                            <input type="number" name="amount" id="amount"
                                value="{{ old('amount', $chandla->amount) }}"
                                step="0.01" min="0" class="cb-field min-h-[48px] w-full" placeholder="0.00">
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="received_date">Received date *</label>
                            <input type="date" name="received_date" id="received_date"
                                value="{{ old('received_date', $chandla->received_date->format('Y-m-d')) }}"
                                required class="cb-field min-h-[48px] w-full">
                        </div>
                    </div>
                </section>

                {{-- Receipt & Notes --}}
                <section aria-labelledby="edit-extra-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                    <h2 id="edit-extra-heading" class="cb-section-label">Receipt &amp; notes</h2>
                    <div class="space-y-4 sm:space-y-5">
                        <div>
                            <label class="cb-label cb-label--classic" for="receipt_number">Receipt number <span class="font-normal text-slate-400">(optional)</span></label>
                            <input type="text" name="receipt_number" id="receipt_number"
                                value="{{ old('receipt_number', $chandla->receipt_number) }}"
                                class="cb-field min-h-[48px] w-full">
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="description">Description <span class="font-normal text-slate-400">(optional)</span></label>
                            <textarea name="description" id="description" rows="3"
                                class="cb-field min-h-[5.5rem] resize-y w-full">{{ old('description', $chandla->description) }}</textarea>
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="notes">Notes <span class="font-normal text-slate-400">(optional)</span></label>
                            <textarea name="notes" id="notes" rows="2"
                                class="cb-field min-h-[4rem] resize-y w-full">{{ old('notes', $chandla->notes) }}</textarea>
                        </div>
                    </div>
                </section>

            </div>{{-- /space-y --}}

            <div class="mt-8 sm:mt-10 pt-5 sm:pt-6 border-t border-slate-200/80 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
                <a href="{{ route('client.chandlas.show', $chandla->id) }}"
                    class="cb-btn cb-btn--ghost w-full sm:w-auto min-h-[48px] px-6 justify-center order-2 sm:order-none">
                    Cancel
                </a>
                <button type="submit" class="cb-btn cb-btn-gold w-full sm:w-auto min-h-[48px] px-6 justify-center shadow-sm">
                    <i class="fas fa-save text-sm" aria-hidden="true"></i>
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const category = document.getElementById('category');
    const paymentMethod = document.getElementById('payment_method');
    const paymentSection = document.getElementById('payment_method_section');
    const gpayFields = document.getElementById('gpay_fields');
    const gpayQrSection = document.getElementById('gpay_qr_section');
    const gpayQrImage = document.getElementById('gpay_qr_image');
    const gpayUpiIdText = document.getElementById('gpay_upi_id_text');
    const giftFields = document.getElementById('gift_fields');
    const cashNotes = document.getElementById('cash_notes');
    const amountSection = document.getElementById('amount_section');
    const amountInput = document.getElementById('amount');
    const cashNoteInputs = document.querySelectorAll('.cash-note-input');
    const cashReceivedTotal = document.getElementById('cash_received_total');
    const cashChangeDue = document.getElementById('cash_change_due');
    const eventSelect = document.querySelector('select[name="event_id"]');

    const noteValues = {
        cash_note_1: 1,
        cash_note_2: 2,
        cash_note_5: 5,
        cash_note_10: 10,
        cash_note_20: 20,
        cash_note_50: 50,
        cash_note_100: 100,
        cash_note_200: 200,
        cash_note_500: 500,
    };

    const updateCashSummary = () => {
        let total = 0;
        cashNoteInputs.forEach((input) => {
            const value = parseInt(input.value || '0', 10);
            total += value * (noteValues[input.name] || 0);
        });

        cashReceivedTotal.textContent = total.toFixed(2);

        if (category.value === 'chandla' && paymentMethod.value === 'cash' && amountInput.dataset.manual !== 'true') {
            amountInput.value = total.toFixed(2);
        }

        const amountValue = parseFloat(amountInput.value || '0');
        const changeDue = total - (isNaN(amountValue) ? 0 : amountValue);
        cashChangeDue.textContent = changeDue >= 0 ? changeDue.toFixed(2) : '0.00';
    };

    const updateVisibility = () => {
        const isCashType = category.value === 'chandla';
        const isGiftType = category.value === 'gift';
        const isCoverType = category.value === 'cover';

        paymentSection.style.display = isCashType ? 'block' : 'none';
        amountSection.style.display = (isCashType || isCoverType) ? 'grid' : 'none';
        giftFields.style.display = isGiftType ? 'block' : 'none';
        gpayQrSection.style.display = 'none';

        if (isGiftType) {
            paymentMethod.value = 'other';
            gpayFields.style.display = 'none';
            cashNotes.style.display = 'none';
            amountInput.readOnly = true;
            amountInput.required = false;
            amountInput.value = 0;
            updateCashSummary();
            return;
        }

        if (isCoverType) {
            paymentMethod.value = 'other';
            gpayFields.style.display = 'none';
            cashNotes.style.display = 'none';
            amountInput.readOnly = false;
            amountInput.required = false;
            updateCashSummary();
            return;
        }

        if (paymentMethod.value === 'cash') {
            gpayFields.style.display = 'none';
            cashNotes.style.display = 'block';
            amountInput.readOnly = false;
            amountInput.required = true;
            updateCashSummary();
        } else if (paymentMethod.value === 'gpay') {
            gpayFields.style.display = 'block';
            cashNotes.style.display = 'none';
            amountInput.readOnly = false;
            amountInput.required = true;
            updateGpayQr();
        } else {
            gpayFields.style.display = 'none';
            cashNotes.style.display = 'none';
            amountInput.readOnly = false;
            amountInput.required = false;
        }
    };

    const updateGpayQr = () => {
        if (paymentMethod.value !== 'gpay' || !eventSelect.value) {
            gpayQrSection.style.display = 'none';
            return;
        }
        const selected = eventSelect.options[eventSelect.selectedIndex];
        const upiId = selected?.dataset?.upiId || '';
        const qrPath = selected?.dataset?.gpayQr || '';
        if (!upiId && !qrPath) {
            gpayQrSection.style.display = 'none';
            return;
        }
        gpayUpiIdText.textContent = upiId || 'N/A';
        if (qrPath) {
            gpayQrImage.src = qrPath;
        } else if (upiId) {
            gpayQrImage.src = `{{ url('/client/gpay/upi-qr') }}/${eventSelect.value}`;
        }
        gpayQrSection.style.display = 'block';
    };

    category.addEventListener('change', () => {
        amountInput.dataset.manual = 'false';
        updateVisibility();
    });
    paymentMethod.addEventListener('change', () => {
        amountInput.dataset.manual = 'false';
        updateVisibility();
    });
    eventSelect.addEventListener('change', updateGpayQr);
    cashNoteInputs.forEach((input) => input.addEventListener('input', updateCashSummary));
    amountInput.addEventListener('input', () => {
        amountInput.dataset.manual = 'true';
        updateCashSummary();
    });

    updateVisibility();
    updateGpayQr();
});
</script>
@endsection

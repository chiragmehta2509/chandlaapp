

<?php $__env->startSection('title', 'Add Chandla'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-2xl mx-auto">
    <header class="mb-6 sm:mb-8">
        <a
            href="<?php echo e(route('client.chandlas.index')); ?>"
            class="cb-link text-sm inline-flex items-center gap-2 py-2 -ml-0.5 sm:py-1 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--cb-gold)]/40 focus-visible:ring-offset-2 focus-visible:ring-offset-[var(--cb-cream-2)]"
        >
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
            <span>Back to ledger</span>
        </a>
        <h1 class="cb-page-title mt-3 sm:mt-4">Add chandla</h1>
        <p class="cb-subtitle max-w-prose">Record cash, cover, or a gift for an event.</p>
    </header>

    <div class="cb-card overflow-hidden">
        <form method="POST" action="<?php echo e(route('client.chandlas.store')); ?>" enctype="multipart/form-data" class="p-4 sm:p-6 lg:p-8">
            <?php echo csrf_field(); ?>

            <div class="space-y-6 sm:space-y-8">
                <section aria-labelledby="chandla-event-heading">
                    <h2 id="chandla-event-heading" class="cb-section-label">Event</h2>
                    <div>
                        <label class="cb-label cb-label--classic" for="chandla-event-select">Event *</label>
                        <?php if($lockCashMode ?? false): ?>
                            <input type="hidden" name="update_existing_cover" value="1">
                            <input type="hidden" name="from_lock_cash" value="1">
                        <?php endif; ?>
                        <?php if($eventId): ?>
                            <input type="hidden" name="event_id" value="<?php echo e(old('event_id', $eventId)); ?>">
                        <?php endif; ?>
                        <select
                            id="chandla-event-select"
                            name="event_id"
                            required
                            <?php echo e($eventId ? 'disabled' : ''); ?>

                            class="cb-field min-h-[48px] w-full <?php echo e($eventId ? '!bg-slate-100 !text-slate-600 cursor-not-allowed' : ''); ?>"
                        >
                            <option value="">Select event</option>
                            <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option
                                    value="<?php echo e($event->id); ?>"
                                    data-upi-id="<?php echo e($event->upi_id); ?>"
                                    data-gpay-qr="<?php echo e($event->gpay_qr_image ? asset('storage/' . $event->gpay_qr_image) : ''); ?>"
                                    <?php echo e(old('event_id', $eventId) == $event->id ? 'selected' : ''); ?>

                                >
                                    <?php echo e($event->title); ?> — <?php echo e($event->event_date->format('d/m/Y')); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if($eventId): ?>
                            <p class="text-xs text-slate-500 mt-2">Event is fixed because you opened this form from that event.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section aria-labelledby="chandla-giver-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                    <h2 id="chandla-giver-heading" class="cb-section-label">Giver</h2>
                    <div class="space-y-4 sm:space-y-5">
                        <div>
                            <label class="cb-label cb-label--classic" for="giver_name">Giver name *</label>
                            <?php if(($lockCashMode ?? false) && isset($giverNameOptions) && $giverNameOptions->count() > 0): ?>
                                <div class="mb-3">
                                    <label class="cb-label cb-label--classic text-xs !font-medium !text-slate-500" for="giver_name_select">Pick from past givers</label>
                                    <select id="giver_name_select" class="cb-field min-h-[48px] w-full">
                                        <option value="">Select name</option>
                                        <?php $__currentLoopData = $giverNameOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $giverOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($giverOption); ?>"><?php echo e($giverOption); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="relative">
                                <input
                                    type="text"
                                    name="giver_name"
                                    id="giver_name"
                                    value="<?php echo e(old('giver_name', $defaultGiverName ?? '')); ?>"
                                    required
                                    autocomplete="off"
                                    class="cb-field min-h-[48px] w-full"
                                    placeholder="Full name"
                                >
                                <div id="giverSuggestions" class="hidden absolute z-20 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-auto"></div>
                            </div>
                            <div id="previousGiverBox" class="hidden mt-3 p-4 rounded-xl border border-amber-200/90 bg-amber-50/90 text-sm text-[var(--cb-navy)]">
                                <p class="font-semibold mb-2">Previous record for this event</p>
                                <p class="text-slate-700"><span class="text-slate-500">Name:</span> <span id="previous_name">-</span></p>
                                <p class="text-slate-700"><span class="text-slate-500">Amount:</span> ₹<span id="previous_amount">0</span></p>
                                <p class="text-slate-700"><span class="text-slate-500">Address:</span> <span id="previous_address">-</span></p>
                                <p class="text-xs text-amber-900/80 mt-2">Tap the button to copy these into the form.</p>
                                <button type="button" id="usePreviousBtn" class="mt-3 cb-btn cb-btn--navy cb-btn--sm">
                                    Use this detail
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="cb-label cb-label--classic" for="giver_address">Address <span class="font-normal text-slate-400">(optional)</span></label>
                            <textarea name="giver_address" id="giver_address" rows="2" class="cb-field min-h-[4.5rem] resize-y w-full" placeholder="Street, area, city…"><?php echo e(old('giver_address')); ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                            <div>
                                <label class="cb-label cb-label--classic" for="giver_phone">Phone <span class="font-normal text-slate-400">(optional)</span></label>
                                <input type="text" name="giver_phone" id="giver_phone" value="<?php echo e(old('giver_phone')); ?>" class="cb-field min-h-[48px] w-full" inputmode="tel" placeholder="Phone">
                            </div>
                            <div>
                                <label class="cb-label cb-label--classic" for="giver_email">Email <span class="font-normal text-slate-400">(optional)</span></label>
                                <input type="email" name="giver_email" id="giver_email" value="<?php echo e(old('giver_email')); ?>" class="cb-field min-h-[48px] w-full" placeholder="Email">
                            </div>
                        </div>
                    </div>
                </section>

                <section aria-labelledby="chandla-type-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                    <h2 id="chandla-type-heading" class="cb-section-label">Type &amp; payment</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-4">
                        <div>
                            <label class="cb-label cb-label--classic" for="category">Type *</label>
                            <?php if($lockCashMode ?? false): ?>
                                <input type="hidden" name="category" value="cover">
                            <?php endif; ?>
                            <select
                                name="category"
                                id="category"
                                required
                                <?php echo e(($lockCashMode ?? false) ? 'disabled' : ''); ?>

                                class="cb-field min-h-[48px] w-full <?php echo e(($lockCashMode ?? false) ? '!bg-slate-100 !text-slate-600 cursor-not-allowed' : ''); ?>"
                            >
                                <option value="">Select type</option>
                                <option value="chandla" <?php echo e(old('category', $defaultCategory ?? '') === 'chandla' ? 'selected' : ''); ?>>Cash</option>
                                <option value="cover" <?php echo e(old('category', $defaultCategory ?? '') === 'cover' ? 'selected' : ''); ?>>Cover</option>
                                <option value="gift" <?php echo e(old('category', $defaultCategory ?? '') === 'gift' ? 'selected' : ''); ?>>Gift</option>
                            </select>
                        </div>

                        <div id="payment_method_section">
                            <label class="cb-label cb-label--classic" for="payment_method">Payment method *</label>
                            <?php if($lockCashMode ?? false): ?>
                                <input type="hidden" name="payment_method" value="cash">
                            <?php endif; ?>
                            <select
                                name="payment_method"
                                id="payment_method"
                                <?php echo e(($lockCashMode ?? false) ? 'disabled' : ''); ?>

                                class="cb-field min-h-[48px] w-full <?php echo e(($lockCashMode ?? false) ? '!bg-slate-100 !text-slate-600 cursor-not-allowed' : ''); ?>"
                            >
                                <option value="">Select method</option>
                                <option value="cash" <?php echo e(old('payment_method', $defaultPaymentMethod ?? '') === 'cash' ? 'selected' : ''); ?>>Cash</option>
                                <option value="gpay" <?php echo e(old('payment_method', $defaultPaymentMethod ?? '') === 'gpay' ? 'selected' : ''); ?>>GPay</option>
                                <option value="other" <?php echo e(old('payment_method', $defaultPaymentMethod ?? '') === 'other' ? 'selected' : ''); ?>>N/A</option>
                            </select>
                        </div>
                    </div>
                    <?php if($lockCashMode ?? false): ?>
                        <p class="text-xs text-slate-500 mb-4 -mt-1">Type is Cover and payment is Cash. Enter note quantities; amount updates automatically.</p>
                    <?php endif; ?>

                    <div class="mb-4 sm:mb-6 p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 space-y-4" id="gpay_fields" style="display: none;">
                        <div>
                            <label class="cb-label cb-label--classic" for="gpay_image">GPay screenshot</label>
                            <input type="file" name="gpay_image" id="gpay_image" accept="image/*" class="cb-field min-h-[48px] w-full file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-slate-800 file:text-white hover:file:bg-slate-700 dark:file:bg-indigo-600 dark:file:text-white dark:hover:file:bg-indigo-500 cursor-pointer">
                            <p class="text-xs text-slate-500 mt-1.5">Upload a screenshot of the transaction.</p>
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="gpay_transaction_id">GPay transaction ID *</label>
                            <input type="text" name="gpay_transaction_id" id="gpay_transaction_id" value="<?php echo e(old('gpay_transaction_id')); ?>" class="cb-field min-h-[48px] w-full" placeholder="UPI ref / transaction ID">
                        </div>
                        <div class="pt-2" id="gpay_qr_section" style="display: none;">
                            <label class="cb-label cb-label--classic">Pay with UPI (scan)</label>
                            <div class="inline-block p-3 rounded-xl border border-slate-200 bg-white shadow-sm">
                                <img id="gpay_qr_image" src="" alt="UPI QR code" class="h-40 w-40 sm:h-48 sm:w-48 object-contain mx-auto">
                            </div>
                            <p class="text-xs text-slate-600 mt-2">UPI ID: <span id="gpay_upi_id_text" class="font-medium text-[var(--cb-navy)]">-</span></p>
                        </div>
                    </div>

                    <div id="gift_fields" style="display: none;" class="mb-4 sm:mb-6 p-4 rounded-xl border border-slate-200/90 bg-slate-50/50 space-y-4">
                        <div>
                            <label class="cb-label cb-label--classic" for="gift_item_name">Gift item name *</label>
                            <input type="text" name="gift_item_name" id="gift_item_name" value="<?php echo e(old('gift_item_name')); ?>" class="cb-field min-h-[48px] w-full">
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="gift_received">Gift given to receiver? *</label>
                            <select name="gift_received" id="gift_received" class="cb-field min-h-[48px] w-full">
                                <option value="">Select</option>
                                <option value="1" <?php echo e(old('gift_received') === '1' ? 'selected' : ''); ?>>Yes</option>
                                <option value="0" <?php echo e(old('gift_received') === '0' ? 'selected' : ''); ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <div id="cash_notes" style="display: none;" class="mb-4 sm:mb-6 p-4 sm:p-5 rounded-xl border border-slate-200/90 bg-white">
                        <label class="cb-label cb-label--classic mb-3 block">Cash notes (quantity)</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5" for="cash_note_1">₹1</label>
                                <input type="number" name="cash_note_1" id="cash_note_1" value="<?php echo e(old('cash_note_1', 0)); ?>" min="0" class="cb-field cash-note-input min-h-[44px] w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5" for="cash_note_2">₹2</label>
                                <input type="number" name="cash_note_2" id="cash_note_2" value="<?php echo e(old('cash_note_2', 0)); ?>" min="0" class="cb-field cash-note-input min-h-[44px] w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5" for="cash_note_5">₹5</label>
                                <input type="number" name="cash_note_5" id="cash_note_5" value="<?php echo e(old('cash_note_5', 0)); ?>" min="0" class="cb-field cash-note-input min-h-[44px] w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5" for="cash_note_10">₹10</label>
                                <input type="number" name="cash_note_10" id="cash_note_10" value="<?php echo e(old('cash_note_10', 0)); ?>" min="0" class="cb-field cash-note-input min-h-[44px] w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5" for="cash_note_20">₹20</label>
                                <input type="number" name="cash_note_20" id="cash_note_20" value="<?php echo e(old('cash_note_20', 0)); ?>" min="0" class="cb-field cash-note-input min-h-[44px] w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5" for="cash_note_50">₹50</label>
                                <input type="number" name="cash_note_50" id="cash_note_50" value="<?php echo e(old('cash_note_50', 0)); ?>" min="0" class="cb-field cash-note-input min-h-[44px] w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5" for="cash_note_100">₹100</label>
                                <input type="number" name="cash_note_100" id="cash_note_100" value="<?php echo e(old('cash_note_100', 0)); ?>" min="0" class="cb-field cash-note-input min-h-[44px] w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5" for="cash_note_200">₹200</label>
                                <input type="number" name="cash_note_200" id="cash_note_200" value="<?php echo e(old('cash_note_200', 0)); ?>" min="0" class="cb-field cash-note-input min-h-[44px] w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5" for="cash_note_500">₹500</label>
                                <input type="number" name="cash_note_500" id="cash_note_500" value="<?php echo e(old('cash_note_500', 0)); ?>" min="0" class="cb-field cash-note-input min-h-[44px] w-full">
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-200/80 space-y-1">
                            <p class="text-sm font-medium text-[var(--cb-navy)]">Received total: ₹<span id="cash_received_total">0</span></p>
                            <p class="text-sm text-slate-600">Change due: ₹<span id="cash_change_due">0</span></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 pt-2" id="amount_section">
                        <div>
                            <label class="cb-label cb-label--classic" for="amount">Amount *</label>
                            <input type="number" name="amount" id="amount" value="<?php echo e(old('amount')); ?>" step="0.01" min="0" class="cb-field min-h-[48px] w-full" placeholder="0.00">
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="received_date">Received date *</label>
                            <input type="date" name="received_date" id="received_date" value="<?php echo e(old('received_date', date('Y-m-d'))); ?>" required class="cb-field min-h-[48px] w-full">
                        </div>
                    </div>
                </section>

                <section aria-labelledby="chandla-extra-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                    <h2 id="chandla-extra-heading" class="cb-section-label">Receipt &amp; notes</h2>
                    <div class="space-y-4 sm:space-y-5">
                        <div>
                            <label class="cb-label cb-label--classic" for="receipt_number">Receipt number <span class="font-normal text-slate-400">(optional)</span></label>
                            <input type="text" name="receipt_number" id="receipt_number" value="<?php echo e(old('receipt_number')); ?>" class="cb-field min-h-[48px] w-full">
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="description">Description <span class="font-normal text-slate-400">(optional)</span></label>
                            <textarea name="description" id="description" rows="3" class="cb-field min-h-[5.5rem] resize-y w-full"><?php echo e(old('description')); ?></textarea>
                        </div>
                        <div>
                            <label class="cb-label cb-label--classic" for="notes">Notes <span class="font-normal text-slate-400">(optional)</span></label>
                            <textarea name="notes" id="notes" rows="2" class="cb-field min-h-[4rem] resize-y w-full"><?php echo e(old('notes')); ?></textarea>
                        </div>
                    </div>
                </section>
            </div>

            <div class="mt-8 sm:mt-10 pt-5 sm:pt-6 border-t border-slate-200/80 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
                <a href="<?php echo e(route('client.chandlas.index')); ?>" class="cb-btn cb-btn--ghost w-full sm:w-auto min-h-[48px] px-6 justify-center order-2 sm:order-none">
                    Cancel
                </a>
                <div class="flex flex-col-reverse sm:flex-row gap-3 sm:justify-end sm:items-center w-full sm:w-auto order-1 sm:order-none">
                    <button type="submit" name="submit_action" value="another" class="cb-btn cb-btn--gold w-full sm:w-auto min-h-[48px] px-6 justify-center shadow-sm">
                        <i class="fas fa-plus text-sm" aria-hidden="true"></i>
                        Save &amp; add another
                    </button>
                    <button type="submit" name="submit_action" value="done" class="cb-btn cb-btn--ghost w-full sm:w-auto min-h-[48px] px-6 justify-center border border-slate-200/90">
                        <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                        Save &amp; view
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lockCashMode = <?php echo json_encode((bool) ($lockCashMode ?? false), 15, 512) ?>;
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
    const giverNameInput = document.getElementById('giver_name');
    const giverAddressInput = document.querySelector('textarea[name="giver_address"]');
    const giverPhoneInput = document.querySelector('input[name="giver_phone"]');
    const giverEmailInput = document.querySelector('input[name="giver_email"]');
    const giverNameSelect = document.getElementById('giver_name_select');
    const giverSuggestions = document.getElementById('giverSuggestions');
    const previousGiverBox = document.getElementById('previousGiverBox');
    const previousName = document.getElementById('previous_name');
    const previousAmount = document.getElementById('previous_amount');
    const previousAddress = document.getElementById('previous_address');
    const usePreviousBtn = document.getElementById('usePreviousBtn');
    let previousGiverData = null;
    let giverLookupTimer = null;
    let suggestionItems = [];

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

        if (((category.value === 'chandla') || (lockCashMode && category.value === 'cover')) && paymentMethod.value === 'cash' && (lockCashMode || amountInput.dataset.manual !== 'true')) {
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
            gpayFields.style.display = 'none';
            if (lockCashMode) {
                paymentMethod.value = 'cash';
                cashNotes.style.display = 'block';
                amountInput.readOnly = true;
                amountInput.required = true;
            } else {
                paymentMethod.value = 'other';
                cashNotes.style.display = 'none';
                amountInput.readOnly = false;
                amountInput.required = false;
            }
            updateCashSummary();
            return;
        }

        if (paymentMethod.value === 'cash') {
            gpayFields.style.display = 'none';
            cashNotes.style.display = 'block';
            amountInput.readOnly = lockCashMode;
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
            gpayQrImage.src = `<?php echo e(url('/client/gpay/upi-qr')); ?>/${eventSelect.value}`;
        }
        gpayQrSection.style.display = 'block';
    };

    const hidePreviousGiver = () => {
        previousGiverData = null;
        previousGiverBox.classList.add('hidden');
    };

    const hideSuggestions = () => {
        suggestionItems = [];
        giverSuggestions.innerHTML = '';
        giverSuggestions.classList.add('hidden');
    };

    const escapeHtml = (value) => {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    };

    const applyGiverData = (data) => {
        previousGiverData = data;
        previousName.textContent = data.giver_name || '-';
        previousAmount.textContent = Number(data.amount || 0).toFixed(2);
        previousAddress.textContent = data.giver_address || '-';
        previousGiverBox.classList.remove('hidden');

        giverNameInput.value = data.giver_name || giverNameInput.value;
        if (!amountInput.value) {
            amountInput.value = Number(data.amount || 0).toFixed(2);
        }
        if (!giverAddressInput.value) {
            giverAddressInput.value = data.giver_address || '';
        }
        if (!giverPhoneInput.value) {
            giverPhoneInput.value = data.giver_phone || '';
        }
        if (!giverEmailInput.value) {
            giverEmailInput.value = data.giver_email || '';
        }
    };

    const renderSuggestions = () => {
        if (suggestionItems.length === 0) {
            hideSuggestions();
            return;
        }

        giverSuggestions.innerHTML = suggestionItems.map((item, index) => `
            <button type="button" class="w-full text-left px-3 py-2.5 hover:bg-amber-50/90 border-b border-slate-100 last:border-b-0 transition-colors" data-index="${index}">
                <p class="font-medium text-slate-800">${escapeHtml(item.giver_name)}</p>
                <p class="text-xs text-slate-500 mt-0.5">${escapeHtml(item.giver_phone || '')}${item.giver_phone && item.giver_address ? ' · ' : ''}${escapeHtml(item.giver_address || '—')}</p>
            </button>
        `).join('');
        giverSuggestions.classList.remove('hidden');
    };

    const fetchGiverSuggestions = async () => {
        const query = giverNameInput.value.trim();
        if (query.length < 1) {
            hideSuggestions();
            return;
        }

        try {
            const response = await fetch(`<?php echo e(route('client.chandlas.search-givers')); ?>?q=${encodeURIComponent(query)}`);
            if (!response.ok) {
                hideSuggestions();
                return;
            }
            const data = await response.json();
            suggestionItems = Array.isArray(data.items) ? data.items : [];
            renderSuggestions();
        } catch (error) {
            hideSuggestions();
        }
    };

    const fetchPreviousGiver = async () => {
        const giverName = giverNameInput.value.trim();
        if (giverName.length < 2) {
            hidePreviousGiver();
            return;
        }

        const eventId = eventSelect ? eventSelect.value : '';
        const selectedCategory = category.value || '';
        const params = new URLSearchParams({ giver_name: giverName });
        if (eventId) params.set('event_id', eventId);
        if (selectedCategory) params.set('category', selectedCategory);

        try {
            const response = await fetch(`<?php echo e(route('client.chandlas.lookup-giver')); ?>?${params.toString()}`);
            if (!response.ok) {
                hidePreviousGiver();
                return;
            }

            const data = await response.json();
            if (!data.found) {
                hidePreviousGiver();
                return;
            }

            previousGiverData = data;
            previousName.textContent = data.giver_name || '-';
            previousAmount.textContent = Number(data.amount || 0).toFixed(2);
            previousAddress.textContent = data.giver_address || '-';
            previousGiverBox.classList.remove('hidden');
        } catch (error) {
            hidePreviousGiver();
        }
    };

    category.addEventListener('change', () => {
        amountInput.dataset.manual = 'false';
        updateVisibility();
    });
    paymentMethod.addEventListener('change', () => {
        amountInput.dataset.manual = 'false';
        updateVisibility();
    });
    eventSelect.addEventListener('change', () => {
        updateGpayQr();
        hideSuggestions();
        fetchPreviousGiver();
    });
    cashNoteInputs.forEach((input) => input.addEventListener('input', updateCashSummary));
    if (!lockCashMode) {
        amountInput.addEventListener('input', () => {
            amountInput.dataset.manual = 'true';
            updateCashSummary();
        });
    } else {
        amountInput.dataset.manual = 'false';
        amountInput.readOnly = true;
    }
    giverNameInput.addEventListener('input', () => {
        clearTimeout(giverLookupTimer);
        giverLookupTimer = setTimeout(() => {
            fetchGiverSuggestions();
            fetchPreviousGiver();
        }, 250);
    });
    giverNameInput.addEventListener('focus', fetchGiverSuggestions);
    giverNameInput.addEventListener('blur', () => {
        setTimeout(hideSuggestions, 150);
        fetchPreviousGiver();
    });
    giverSuggestions.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-index]');
        if (!button) {
            return;
        }
        const idx = Number(button.dataset.index);
        const selected = suggestionItems[idx];
        if (!selected) {
            return;
        }
        // Fill name and contact details from the suggestion
        giverNameInput.value = selected.giver_name || '';
        if (!giverAddressInput.value) {
            giverAddressInput.value = selected.giver_address || '';
        }
        if (!giverPhoneInput.value) {
            giverPhoneInput.value = selected.giver_phone || '';
        }
        if (!giverEmailInput.value) {
            giverEmailInput.value = selected.giver_email || '';
        }
        hideSuggestions();
        fetchPreviousGiver();
    });
    document.addEventListener('click', (event) => {
        if (!giverSuggestions.contains(event.target) && event.target !== giverNameInput) {
            hideSuggestions();
        }
    });
    usePreviousBtn.addEventListener('click', () => {
        if (!previousGiverData) {
            return;
        }
        applyGiverData(previousGiverData);
    });
    if (giverNameSelect) {
        giverNameSelect.addEventListener('change', () => {
            if (!giverNameSelect.value) {
                return;
            }
            giverNameInput.value = giverNameSelect.value;
            hideSuggestions();
            fetchPreviousGiver();
        });
    }

    updateVisibility();
    updateGpayQr();
    fetchPreviousGiver();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/chandlas/create.blade.php ENDPATH**/ ?>
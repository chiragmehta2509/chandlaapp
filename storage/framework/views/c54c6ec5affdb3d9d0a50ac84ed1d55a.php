<?php $__env->startSection('title', 'Add Chanda Entry — ' . $event->title); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="<?php echo e(route('client.ganpati.show', $event->id)); ?>" class="gp-back-btn">
            <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
        </a>
        <div class="min-w-0">
            <p class="gp-page-label">🪔 Ganpati Special</p>
            <h1 class="gp-page-title">Add Chanda Entry</h1>
            <div class="flex items-center gap-2 mt-0.5">
                <p class="text-xs cb-subtitle truncate"><?php echo e($event->title); ?></p>
                <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
                <a href="<?php echo e(route('client.ganpati.edit', $event->id)); ?>" class="text-sky-500 hover:text-sky-700" title="Edit Event">
                    <i class="fas fa-pencil text-[0.7rem]" aria-hidden="true"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="gp-form-card">
        <form method="POST" action="<?php echo e(route('client.ganpati.chandla.store', $event->id)); ?>"
              enctype="multipart/form-data" id="ganpati-chanda-form">
            <?php echo csrf_field(); ?>

            <div class="mb-4">
                <label for="giver_name" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Donor Name <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="text" id="giver_name" name="giver_name" value="<?php echo e(old('giver_name')); ?>"
                       required maxlength="255" autocomplete="off" placeholder="e.g. Ramesh Sharma"
                       class="cb-field w-full <?php $__errorArgs = ['giver_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['giver_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-4">
                <label for="giver_phone" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Phone <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <input type="tel" id="giver_phone" name="giver_phone" value="<?php echo e(old('giver_phone')); ?>"
                       maxlength="30" placeholder="e.g. 98765 43210"
                       class="cb-field w-full <?php $__errorArgs = ['giver_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['giver_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-4">
                <label for="giver_address" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Address <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <textarea id="giver_address" name="giver_address" rows="2"
                          placeholder="e.g. House No. 5, Ward 3"
                          class="cb-field w-full resize-none <?php $__errorArgs = ['giver_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('giver_address')); ?></textarea>
                <?php $__errorArgs = ['giver_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="mb-4">
                <span class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Payment Method <span class="text-red-500" aria-hidden="true">*</span>
                </span>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = ['cash' => 'Cash', 'gpay' => 'GPay / UPI', 'other' => 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="gp-method-label">
                        <input type="radio" name="payment_method" value="<?php echo e($val); ?>" class="sr-only"
                               <?php echo e(old('payment_method', 'cash') === $val ? 'checked' : ''); ?>

                               onchange="togglePaymentFields(this.value)">
                        <?php echo e($lbl); ?>

                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-4">
                <label for="amount" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Amount (₹) <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="number" id="amount" name="amount" value="<?php echo e(old('amount')); ?>"
                       min="0" step="1" required placeholder="0"
                       class="cb-field w-full <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div id="cash-notes-section" class="mb-4 gp-cash-notes">
                <div class="flex items-center justify-between mb-2">
                    <p class="gp-cash-notes__title mb-0">Cash Notes Received</p>
                    <p class="text-xs font-bold text-green-700 dark:text-green-400">
                        Total Cash: ₹<span id="cash_received_total">0</span>
                    </p>
                </div>
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                    <?php $__currentLoopData = [500, 200, 100, 50, 20, 10, 5, 2, 1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $denom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <label for="cn_<?php echo e($denom); ?>" class="block text-[0.6rem] font-bold text-slate-500 dark:text-slate-400 mb-1">₹<?php echo e($denom); ?></label>
                        <input type="number" id="cn_<?php echo e($denom); ?>" name="cash_note_<?php echo e($denom); ?>"
                               value="<?php echo e(old('cash_note_' . $denom, 0)); ?>" min="0" step="1"
                               class="cb-field cash-note-input w-full text-center px-1 py-1.5 text-sm">
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div id="gpay-section" class="mb-4 gp-gpay-section hidden">
                <p class="gp-gpay-section__title">GPay / UPI Payment</p>

                
                <?php if($event->upi_id || $event->gpay_qr_image): ?>
                <div class="rounded-xl border mb-4 overflow-hidden" style="border-color:var(--gp-border); background:var(--gp-bg-accent);">
                    <div class="px-3 py-2 flex items-center gap-2" style="border-bottom:1px solid var(--gp-border-soft);">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg shrink-0" style="background:rgba(249,115,22,0.12);">
                            <i class="fas fa-qrcode text-xs" style="color:var(--gp-orange);" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold" style="color:var(--gp-text);">Scan &amp; Pay via UPI</p>
                            <?php if($event->upi_id): ?>
                            <p class="text-[0.65rem] font-mono truncate" style="color:var(--gp-muted);"><?php echo e($event->upi_id); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 p-4">
                        <?php if($event->upi_id): ?>
                        <div class="flex flex-col items-center gap-1.5 shrink-0">
                            <div class="p-2 bg-white dark:bg-slate-900 rounded-xl shadow-sm" style="border:1.5px solid var(--gp-border);">
                                <img src="<?php echo e(route('client.ganpati.qr', $event->id)); ?>"
                                     alt="UPI QR Code"
                                     class="w-32 h-32 sm:w-36 sm:h-36 block">
                            </div>
                            <p class="text-[0.65rem] font-semibold text-center" style="color:var(--gp-text);">UPI QR Code</p>
                        </div>
                        <?php endif; ?>

                        <?php if($event->gpay_qr_image): ?>
                        <div class="flex flex-col items-center gap-1.5 shrink-0">
                            <div class="p-2 bg-white dark:bg-slate-900 rounded-xl shadow-sm" style="border:1.5px solid var(--gp-border);">
                                <img src="<?php echo e(asset('storage/' . $event->gpay_qr_image)); ?>"
                                     alt="Scanner Image"
                                     class="w-32 h-32 sm:w-36 sm:h-36 object-contain block">
                            </div>
                            <p class="text-[0.65rem] font-semibold text-center" style="color:var(--gp-text);">Scanner Image</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="mb-4 rounded-lg px-3 py-2 flex items-center gap-2 text-xs" style="background:rgba(234,88,12,0.08); border:1px solid var(--gp-border-soft); color:var(--gp-text);">
                    <i class="fas fa-info-circle shrink-0" style="color:var(--gp-orange);" aria-hidden="true"></i>
                    <span>No UPI scanner added yet.
                        <a href="<?php echo e(route('client.ganpati.scanner', $event->id)); ?>" class="font-semibold underline" style="color:var(--gp-muted);" target="_blank">
                            Add UPI ID / Scanner image
                        </a>
                    </span>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="gpay_transaction_id" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Transaction ID <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <input type="text" id="gpay_transaction_id" name="gpay_transaction_id"
                           value="<?php echo e(old('gpay_transaction_id')); ?>" maxlength="255"
                           placeholder="e.g. 421512345678" class="cb-field w-full">
                </div>
                <div>
                    <label for="gpay_image" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Screenshot <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <input type="file" id="gpay_image" name="gpay_image" accept="image/*" class="cb-field w-full text-sm">
                </div>
            </div>

            <div class="mb-4">
                <label for="received_date" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Date <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="date" id="received_date" name="received_date"
                       value="<?php echo e(old('received_date', now()->toDateString())); ?>" required
                       class="cb-field w-full <?php $__errorArgs = ['received_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['received_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="mb-5">
                <label for="notes" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Notes <span class="text-slate-400 font-normal normal-case">(optional)</span>
                </label>
                <textarea id="notes" name="notes" rows="2" class="cb-field w-full resize-none"><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" name="submit_action" value="save" class="gp-btn flex-1 py-3">
                    <i class="fas fa-check" aria-hidden="true"></i> Save Entry
                </button>
                <button type="submit" name="submit_action" value="another" class="gp-btn gp-btn--outline flex-1 py-3">
                    <i class="fas fa-plus" aria-hidden="true"></i> Save &amp; Add Another
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let isManualAmount = false;

const noteValues = {
    'cash_note_500': 500,
    'cash_note_200': 200,
    'cash_note_100': 100,
    'cash_note_50': 50,
    'cash_note_20': 20,
    'cash_note_10': 10,
    'cash_note_5': 5,
    'cash_note_2': 2,
    'cash_note_1': 1
};

function calculateCashNotes() {
    let total = 0;
    const inputs = document.querySelectorAll('.cash-note-input');
    inputs.forEach(input => {
        const qty = parseInt(input.value || '0', 10);
        const multiplier = noteValues[input.name] || 0;
        total += (qty > 0 ? qty : 0) * multiplier;
    });

    const totalDisplay = document.getElementById('cash_received_total');
    if (totalDisplay) {
        totalDisplay.textContent = total;
    }

    const amountInput = document.getElementById('amount');
    const checkedMethod = document.querySelector('input[name="payment_method"]:checked')?.value;

    if (checkedMethod === 'cash' && !isManualAmount) {
        amountInput.value = total > 0 ? total : '';
    }
}

function togglePaymentFields(method) {
    const cashSection = document.getElementById('cash-notes-section');
    const gpaySection = document.getElementById('gpay-section');
    if (cashSection) cashSection.classList.toggle('hidden', method !== 'cash');
    if (gpaySection) gpaySection.classList.toggle('hidden', method !== 'gpay');

    if (method === 'cash') {
        calculateCashNotes();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const checked = document.querySelector('input[name="payment_method"]:checked');
    if (checked) togglePaymentFields(checked.value);

    const cashInputs = document.querySelectorAll('.cash-note-input');
    cashInputs.forEach(input => {
        input.addEventListener('input', calculateCashNotes);
        input.addEventListener('change', calculateCashNotes);
    });

    const amountInput = document.getElementById('amount');
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            if (this.value !== '') {
                isManualAmount = true;
            } else {
                isManualAmount = false;
                calculateCashNotes();
            }
        });
    }

    calculateCashNotes();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/ganpati/chandla-create.blade.php ENDPATH**/ ?>
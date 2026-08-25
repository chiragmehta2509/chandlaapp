<?php $__env->startSection('title', 'Edit Entry — ' . $event->title); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="<?php echo e(route('client.ganpati.show', $event->id)); ?>" class="gp-back-btn">
            <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
        </a>
        <div class="min-w-0">
            <p class="gp-page-label">🪔 Ganpati Special</p>
            <h1 class="gp-page-title">Edit Chanda Entry</h1>
            <p class="text-xs cb-subtitle truncate"><?php echo e($chandla->giver_name); ?> · <?php echo e($event->title); ?></p>
        </div>
    </div>

    <div class="gp-form-card">
        <form method="POST" action="<?php echo e(route('client.ganpati.chandla.update', [$event->id, $chandla->id])); ?>"
              enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="mb-4">
                <label for="giver_name" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Donor Name <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="text" id="giver_name" name="giver_name" value="<?php echo e(old('giver_name', $chandla->giver_name)); ?>"
                       required maxlength="255"
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
                <input type="tel" id="giver_phone" name="giver_phone" value="<?php echo e(old('giver_phone', $chandla->giver_phone)); ?>"
                       maxlength="30" class="cb-field w-full <?php $__errorArgs = ['giver_phone'];
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
                          class="cb-field w-full resize-none <?php $__errorArgs = ['giver_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('giver_address', $chandla->giver_address)); ?></textarea>
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
                               <?php echo e(old('payment_method', $chandla->payment_method) === $val ? 'checked' : ''); ?>

                               onchange="togglePaymentFields(this.value)">
                        <?php echo e($lbl); ?>

                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="mb-4">
                <label for="amount" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Amount (₹) <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="number" id="amount" name="amount"
                       value="<?php echo e(old('amount', $chandla->amount)); ?>"
                       min="0" step="1" required
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

            <div id="gpay-section" class="mb-4 gp-gpay-section <?php echo e(old('payment_method', $chandla->payment_method) !== 'gpay' ? 'hidden' : ''); ?>">
                <p class="gp-gpay-section__title">GPay / UPI Details</p>
                <div class="mb-3">
                    <label for="gpay_transaction_id" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Transaction ID <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <input type="text" id="gpay_transaction_id" name="gpay_transaction_id"
                           value="<?php echo e(old('gpay_transaction_id', $chandla->gpay_transaction_id)); ?>"
                           maxlength="255" class="cb-field w-full">
                </div>
                <div>
                    <label for="gpay_image" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Screenshot <span class="text-slate-400 font-normal">(replace existing)</span>
                    </label>
                    <?php if($chandla->gpay_image): ?>
                        <img src="<?php echo e(asset('storage/' . $chandla->gpay_image)); ?>" alt="GPay screenshot"
                             class="h-16 rounded-lg mb-2 object-contain bg-white dark:bg-slate-800"
                             style="border:1px solid var(--gp-border);">
                    <?php endif; ?>
                    <input type="file" id="gpay_image" name="gpay_image" accept="image/*" class="cb-field w-full text-sm">
                </div>
            </div>

            <div class="mb-4">
                <label for="received_date" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                    Date <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="date" id="received_date" name="received_date"
                       value="<?php echo e(old('received_date', optional($chandla->received_date)->format('Y-m-d'))); ?>"
                       required class="cb-field w-full <?php $__errorArgs = ['received_date'];
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
                <textarea id="notes" name="notes" rows="2"
                          class="cb-field w-full resize-none"><?php echo e(old('notes', $chandla->notes)); ?></textarea>
            </div>

            <div class="flex gap-3">
                <a href="<?php echo e(route('client.ganpati.show', $event->id)); ?>"
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

<?php $__env->startPush('scripts'); ?>
<script>
function togglePaymentFields(method) {
    document.getElementById('gpay-section').classList.toggle('hidden', method !== 'gpay');
}
document.addEventListener('DOMContentLoaded', function () {
    const checked = document.querySelector('input[name="payment_method"]:checked');
    if (checked) togglePaymentFields(checked.value);
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/ganpati/chandla-edit.blade.php ENDPATH**/ ?>
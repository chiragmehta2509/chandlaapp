<?php $__env->startSection('title', 'Edit Expense'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-2xl mx-auto">
    <header class="mb-6 sm:mb-8">
        <a href="<?php echo e(route('client.expenses.index')); ?>"
           class="cb-link text-sm inline-flex items-center gap-2 py-2 -ml-0.5 sm:py-1 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--cb-gold)]/40">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
            <span>Back to expenses</span>
        </a>
        <h1 class="cb-page-title mt-3 sm:mt-4">Edit Expense</h1>
        <p class="cb-subtitle max-w-prose">Update expense details, payee, and payment information.</p>
    </header>

    <div class="cb-card overflow-hidden">
        <form method="POST" action="<?php echo e(route('client.expenses.update', $expense->id)); ?>" enctype="multipart/form-data"
              class="p-4 sm:p-6 lg:p-8">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <?php
                $tab1HasErrors = $errors->hasAny(['event_id', 'title', 'category', 'amount', 'expense_date', 'payment_method']);
                $tab2HasErrors = $errors->hasAny([
                    'description', 'transaction_id', 'receipt_number', 'receipt_image',
                    'payee_name', 'payee_phone', 'payee_upi', 'notes'
                ]);
            ?>

            <div class="space-y-6 sm:space-y-8">

                
                <div class="flex border-b border-slate-200 dark:border-slate-800 mb-6 bg-slate-50/50 dark:bg-slate-900/30 p-1.5 rounded-xl gap-2">
                    <button type="button" id="tab-basic-btn" 
                            class="flex-1 py-2 px-3 text-sm font-semibold rounded-lg flex items-center justify-center gap-2 transition-all">
                        <i class="fas fa-circle-info text-cb-gold"></i> Basic Info
                        <?php if($tab1HasErrors): ?>
                            <span class="h-2 w-2 rounded-full bg-red-500 inline-block animate-pulse"></span>
                        <?php endif; ?>
                    </button>
                    <button type="button" id="tab-advance-btn" 
                            class="flex-1 py-2 px-3 text-sm font-semibold rounded-lg flex items-center justify-center gap-2 transition-all">
                        <i class="fas fa-folder-plus text-slate-400"></i> Advance Details
                        <?php if($tab2HasErrors): ?>
                            <span class="h-2 w-2 rounded-full bg-red-500 inline-block animate-pulse"></span>
                        <?php endif; ?>
                    </button>
                </div>

                
                <div id="pane-basic" class="space-y-6 sm:space-y-8">
                    
                    <section aria-labelledby="exp-event-heading">
                        <h2 id="exp-event-heading" class="cb-section-label">Event</h2>
                        <div>
                            <label class="cb-label cb-label--classic" for="exp-event-select">Event *</label>
                            <select id="exp-event-select" name="event_id" required class="cb-field min-h-[48px] w-full">
                                <option value="">Select event</option>
                                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($event->id); ?>"
                                        <?php echo e(old('event_id', $expense->event_id) == $event->id ? 'selected' : ''); ?>>
                                        <?php echo e($event->title); ?> — <?php echo e($event->event_date?->format('d/m/Y')); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['event_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </section>

                    
                    <section aria-labelledby="exp-detail-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                        <h2 id="exp-detail-heading" class="cb-section-label">Expense Details</h2>
                        <div class="space-y-4 sm:space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="cb-label cb-label--classic" for="exp-title">Title *</label>
                                    <input type="text" id="exp-title" name="title"
                                           value="<?php echo e(old('title', $expense->title)); ?>" required maxlength="255"
                                           placeholder="e.g. Stage Decoration"
                                           class="cb-field min-h-[48px] w-full <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div>
                                    <label class="cb-label cb-label--classic" for="exp-category">Category *</label>
                                    <select id="exp-category" name="category" required
                                            class="cb-field min-h-[48px] w-full <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Select category</option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($cat); ?>"
                                                <?php echo e(old('category', $expense->category) == $cat ? 'selected' : ''); ?>>
                                                <?php echo e(ucfirst($cat)); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="cb-label cb-label--classic" for="exp-amount">Amount (₹) *</label>
                                    <input type="number" id="exp-amount" name="amount"
                                           value="<?php echo e(old('amount', $expense->amount)); ?>" required min="0" step="0.01"
                                           placeholder="0.00"
                                           class="cb-field min-h-[48px] w-full <?php $__errorArgs = ['amount'];
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
                                <div>
                                    <label class="cb-label cb-label--classic" for="exp-date">Expense Date *</label>
                                    <input type="date" id="exp-date" name="expense_date"
                                           value="<?php echo e(old('expense_date', $expense->expense_date?->format('Y-m-d'))); ?>" required
                                           class="cb-field min-h-[48px] w-full <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <?php $__errorArgs = ['expense_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                    </section>

                    
                    <section aria-labelledby="exp-payment-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                        <h2 id="exp-payment-heading" class="cb-section-label">Payment</h2>
                        <div class="space-y-4 sm:space-y-5">
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400 mb-1.5">
                                    Payment Method *
                                </span>
                                <div class="flex flex-wrap gap-2">
                                    <?php $__currentLoopData = ['cash' => 'Cash', 'gpay' => 'GPay / UPI', 'bank_transfer' => 'Bank Transfer', 'cheque' => 'Cheque', 'other' => 'Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="gp-method-label">
                                        <input type="radio" name="payment_method" value="<?php echo e($val); ?>"
                                               class="sr-only"
                                               <?php echo e(old('payment_method', $expense->payment_method) === $val ? 'checked' : ''); ?>>
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
                        </div>
                    </section>

                    
                    <div class="pt-4 border-t border-slate-200/80 flex justify-end">
                        <button type="button" id="go-to-advance-btn" class="cb-btn cb-btn-navy w-full sm:w-auto justify-center">
                            Continue to Advance Details <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                
                <div id="pane-advance" class="hidden space-y-6 sm:space-y-8">
                    
                    <section aria-labelledby="exp-optional-desc-heading">
                        <h2 id="exp-optional-desc-heading" class="cb-section-label">Description</h2>
                        <div>
                            <label class="cb-label cb-label--classic" for="exp-description">Description
                                <span class="text-slate-400 font-normal normal-case">(optional)</span>
                            </label>
                            <textarea id="exp-description" name="description" rows="2"
                                      placeholder="Brief description of this expense"
                                      class="cb-field w-full resize-none"><?php echo e(old('description', $expense->description)); ?></textarea>
                        </div>
                    </section>

                    
                    <section aria-labelledby="exp-payment-extra-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                        <h2 id="exp-payment-extra-heading" class="cb-section-label">Transaction &amp; Receipt</h2>
                        <div class="space-y-4 sm:space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="cb-label cb-label--classic" for="exp-txn">Transaction / Cheque No.
                                        <span class="text-slate-400 font-normal normal-case">(optional)</span>
                                    </label>
                                    <input type="text" id="exp-txn" name="transaction_id"
                                           value="<?php echo e(old('transaction_id', $expense->transaction_id)); ?>" maxlength="255"
                                           placeholder="Reference number"
                                           class="cb-field min-h-[48px] w-full">
                                </div>
                                <div>
                                    <label class="cb-label cb-label--classic" for="exp-receipt-no">Receipt No.
                                        <span class="text-slate-400 font-normal normal-case">(optional)</span>
                                    </label>
                                    <input type="text" id="exp-receipt-no" name="receipt_number"
                                           value="<?php echo e(old('receipt_number', $expense->receipt_number)); ?>" maxlength="100"
                                           placeholder="Optional receipt number"
                                           class="cb-field min-h-[48px] w-full">
                                </div>
                            </div>

                            <div>
                                <label class="cb-label cb-label--classic" for="exp-receipt-img">Receipt Image
                                    <span class="text-slate-400 font-normal normal-case">(optional, max 5 MB)</span>
                                </label>
                                <?php if($expense->receipt_image): ?>
                                    <div class="mb-2 flex items-center gap-2">
                                        <a href="<?php echo e(Storage::disk('public')->url($expense->receipt_image)); ?>" target="_blank"
                                           class="cb-link text-xs inline-flex items-center gap-1">
                                            <i class="fa-solid fa-image" aria-hidden="true"></i>
                                            View current receipt
                                        </a>
                                        <span class="text-xs text-slate-400">— upload a new one to replace it</span>
                                    </div>
                                <?php endif; ?>
                                <input type="file" id="exp-receipt-img" name="receipt_image"
                                       accept="image/jpeg,image/png,image/jpg"
                                       class="cb-field w-full file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200">
                                <?php $__errorArgs = ['receipt_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </section>

                    
                    <section aria-labelledby="exp-payee-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                        <h2 id="exp-payee-heading" class="cb-section-label">Payee / Vendor
                            <span class="text-slate-400 font-normal normal-case text-xs">(optional)</span>
                        </h2>
                        <div class="space-y-4 sm:space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="cb-label cb-label--classic" for="exp-payee-name">Payee Name</label>
                                    <input type="text" id="exp-payee-name" name="payee_name"
                                           value="<?php echo e(old('payee_name', $expense->payee_name)); ?>" maxlength="255"
                                           placeholder="Vendor / person paid"
                                           class="cb-field min-h-[48px] w-full">
                                </div>
                                <div>
                                    <label class="cb-label cb-label--classic" for="exp-payee-phone">Payee Phone</label>
                                    <input type="tel" id="exp-payee-phone" name="payee_phone"
                                           value="<?php echo e(old('payee_phone', $expense->payee_phone)); ?>" maxlength="30"
                                           placeholder="e.g. 98765 43210"
                                           class="cb-field min-h-[48px] w-full">
                                </div>
                            </div>
                            <div>
                                <label class="cb-label cb-label--classic" for="exp-payee-upi">Payee UPI ID</label>
                                <input type="text" id="exp-payee-upi" name="payee_upi"
                                       value="<?php echo e(old('payee_upi', $expense->payee_upi)); ?>" maxlength="255"
                                       placeholder="e.g. vendor@upi"
                                       class="cb-field min-h-[48px] w-full">
                            </div>
                        </div>
                    </section>

                    
                    <section aria-labelledby="exp-notes-heading" class="pt-2 sm:pt-4 border-t border-slate-200/80">
                        <h2 id="exp-notes-heading" class="cb-section-label">Notes
                            <span class="text-slate-400 font-normal normal-case text-xs">(optional)</span>
                        </h2>
                        <textarea id="exp-notes" name="notes" rows="3"
                                  placeholder="Any extra details…"
                                  class="cb-field w-full resize-none"><?php echo e(old('notes', $expense->notes)); ?></textarea>
                    </section>
                </div>

                
                <div class="pt-2 sm:pt-4 border-t border-slate-200/80 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="cb-btn cb-btn--navy flex-1 justify-center">
                        <i class="fa-solid fa-save mr-2" aria-hidden="true"></i> Update Expense
                    </button>
                    <a href="<?php echo e(route('client.expenses.index')); ?>"
                       class="cb-btn cb-btn--outline flex-1 justify-center text-center">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBasicBtn = document.getElementById('tab-basic-btn');
    const tabAdvanceBtn = document.getElementById('tab-advance-btn');
    const paneBasic = document.getElementById('pane-basic');
    const paneAdvance = document.getElementById('pane-advance');
    const goToAdvanceBtn = document.getElementById('go-to-advance-btn');

    const activeClasses = ['bg-white', 'dark:bg-slate-800', 'text-slate-800', 'dark:text-white', 'shadow-sm', 'border', 'border-slate-200/80', 'dark:border-slate-700'];
    const inactiveClasses = ['text-slate-500', 'hover:text-slate-700', 'dark:text-slate-400', 'dark:hover:text-slate-200', 'border', 'border-transparent'];

    function showTab(tabName) {
        if (tabName === 'basic') {
            // Buttons
            tabBasicBtn.classList.add(...activeClasses);
            tabBasicBtn.classList.remove(...inactiveClasses);
            tabAdvanceBtn.classList.add(...inactiveClasses);
            tabAdvanceBtn.classList.remove(...activeClasses);
            
            // Icon colors
            tabBasicBtn.querySelector('i').className = 'fas fa-circle-info text-cb-gold';
            tabAdvanceBtn.querySelector('i').className = 'fas fa-folder-plus text-slate-400';

            // Panes
            paneBasic.classList.remove('hidden');
            paneAdvance.classList.add('hidden');
        } else {
            // Buttons
            tabAdvanceBtn.classList.add(...activeClasses);
            tabAdvanceBtn.classList.remove(...inactiveClasses);
            tabBasicBtn.classList.add(...inactiveClasses);
            tabBasicBtn.classList.remove(...activeClasses);

            // Icon colors
            tabAdvanceBtn.querySelector('i').className = 'fas fa-folder-plus text-cb-gold';
            tabBasicBtn.querySelector('i').className = 'fas fa-circle-info text-slate-400';

            // Panes
            paneAdvance.classList.remove('hidden');
            paneBasic.classList.add('hidden');
        }
    }

    if (tabBasicBtn && tabAdvanceBtn) {
        tabBasicBtn.addEventListener('click', () => showTab('basic'));
        tabAdvanceBtn.addEventListener('click', () => showTab('advance'));
    }

    if (goToAdvanceBtn) {
        goToAdvanceBtn.addEventListener('click', () => showTab('advance'));
    }

    // Default to the tab with errors
    <?php if($tab2HasErrors && !$tab1HasErrors): ?>
        showTab('advance');
    <?php else: ?>
        showTab('basic');
    <?php endif; ?>
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/expenses/edit.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'Transaction history'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="<?php echo e(route('client.dashboard')); ?>" class="cb-link text-sm font-medium inline-flex items-center gap-1">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i> Back to dashboard
        </a>
    </div>

    <div class="cb-card p-5 sm:p-6 mb-6">
        <div class="flex items-start gap-3">
            <span class="hidden sm:inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800" aria-hidden="true">
                <i class="fas fa-receipt"></i>
            </span>
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-cb-navy">Transaction history</h1>
                <p class="text-sm text-slate-600 mt-1 leading-relaxed">All your plan purchases, event payments, and Razorpay receipts in one place.</p>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
        <div class="rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100/40 border border-amber-200/80 p-5">
            <p class="text-[0.65rem] font-bold uppercase tracking-wider text-amber-800/80">Total spent (completed)</p>
            <p class="text-2xl sm:text-3xl font-black text-cb-navy mt-1 tabular-nums">₹<?php echo e(number_format($totalSpent, 0)); ?></p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-violet-50 to-violet-100/40 border border-violet-200/80 p-5">
            <p class="text-[0.65rem] font-bold uppercase tracking-wider text-violet-800/80">Pack purchases</p>
            <p class="text-2xl sm:text-3xl font-black text-cb-navy mt-1 tabular-nums"><?php echo e($packCount); ?></p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-sky-50 to-sky-100/40 border border-sky-200/80 p-5">
            <p class="text-[0.65rem] font-bold uppercase tracking-wider text-sky-800/80">Event payments</p>
            <p class="text-2xl sm:text-3xl font-black text-cb-navy mt-1 tabular-nums"><?php echo e($upiCount); ?></p>
        </div>
    </div>

    
    <div class="cb-card p-4 sm:p-5 mb-6">
        <form method="GET" action="<?php echo e(route('client.transactions.index')); ?>" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[160px]">
                <label class="cb-label">Type</label>
                <select name="kind" class="cb-field">
                    <option value="">All types</option>
                    <option value="pack" <?php echo e($filterKind === 'pack' ? 'selected' : ''); ?>>Pack purchases</option>
                    <option value="upi" <?php echo e($filterKind === 'upi' ? 'selected' : ''); ?>>Event / UPI payments</option>
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="cb-label">Status</label>
                <select name="status" class="cb-field">
                    <option value="">All statuses</option>
                    <option value="completed" <?php echo e($filterStatus === 'completed' || $filterStatus === 'success' ? 'selected' : ''); ?>>Success</option>
                    <option value="pending" <?php echo e($filterStatus === 'pending' || $filterStatus === 'in_process' || $filterStatus === 'process' ? 'selected' : ''); ?>>In Process</option>
                    <option value="failed" <?php echo e($filterStatus === 'failed' ? 'selected' : ''); ?>>Failed</option>
                    <option value="refunded" <?php echo e($filterStatus === 'refunded' ? 'selected' : ''); ?>>Refunded</option>
                </select>
            </div>
            <button type="submit" class="cb-btn cb-btn-navy">
                <i class="fas fa-filter mr-1.5" aria-hidden="true"></i>Apply
            </button>
            <?php if($filterKind || $filterStatus): ?>
                <a href="<?php echo e(route('client.transactions.index')); ?>" class="cb-btn cb-btn--ghost">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    
    <div class="cb-card overflow-hidden">
        <?php if($transactions->isEmpty()): ?>
            <div class="p-10 text-center text-sm text-slate-500">
                <i class="fas fa-receipt text-4xl text-slate-300 mb-3 block" aria-hidden="true"></i>
                <p class="font-medium">No transactions yet.</p>
                <p class="text-xs mt-1">Plan purchases and event payments will appear here.</p>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-slate-100">
                <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $statusClass = match($tx->status) {
                            'completed', 'success' => 'bg-emerald-100 text-emerald-800',
                            'pending', 'in_process', 'process' => 'bg-amber-100 text-amber-800',
                            'failed'    => 'bg-rose-100 text-rose-800',
                            'refunded'  => 'bg-slate-200 text-slate-700',
                            default     => 'bg-slate-100 text-slate-700',
                        };
                        $statusLabel = match($tx->status) {
                            'completed', 'success' => 'Success',
                            'pending', 'in_process', 'process' => 'In Process',
                            'failed'    => 'Failed',
                            'refunded'  => 'Refunded',
                            default     => ucfirst($tx->status),
                        };
                        $kindClass = $tx->kind === 'pack'
                            ? 'bg-violet-100 text-violet-700'
                            : 'bg-sky-100 text-sky-700';
                        $kindIcon = $tx->kind === 'pack' ? 'fa-box' : 'fa-mobile-screen';
                    ?>
                    <li class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 sm:p-5">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl <?php echo e($kindClass); ?>" aria-hidden="true">
                            <i class="fas <?php echo e($kindIcon); ?>"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <?php if(!empty($tx->txn_number)): ?>
                                <a href="<?php echo e(route('client.transactions.show', $tx->txn_number)); ?>" class="font-semibold text-cb-navy leading-snug break-words hover:text-amber-600 transition-colors inline-block mb-0.5">
                                    <?php echo e($tx->title); ?>

                                </a>
                            <?php else: ?>
                                <p class="font-semibold text-cb-navy leading-snug break-words"><?php echo e($tx->title); ?></p>
                            <?php endif; ?>
                            <p class="text-xs text-slate-500 mt-0.5"><?php echo e($tx->subtitle); ?></p>
                            <div class="text-xs text-slate-500 mt-1.5 flex flex-wrap gap-x-3 gap-y-0.5">
                                <span><i class="fas fa-clock mr-1 text-slate-400" aria-hidden="true"></i><?php echo e(optional($tx->date)->format('d/m/Y · g:i A') ?? '—'); ?></span>
                                <?php if($tx->reference): ?>
                                    <span class="font-mono text-[0.7rem]"><i class="fas fa-hashtag mr-1 text-slate-400" aria-hidden="true"></i><?php echo e($tx->reference); ?></span>
                                <?php endif; ?>
                                <?php if($tx->method): ?>
                                    <span class="uppercase tracking-wide text-[0.65rem] font-semibold text-slate-500"><?php echo e($tx->method); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex sm:flex-col items-center sm:items-end gap-2 sm:gap-1 shrink-0">
                            <span class="text-lg font-bold text-cb-navy tabular-nums">₹<?php echo e(number_format($tx->amount, 2)); ?></span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wider <?php echo e($statusClass); ?>">
                                <?php echo e($statusLabel); ?>

                            </span>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>

    <?php if($transactions->hasPages()): ?>
        <div class="mt-6 flex justify-center">
            <?php echo e($transactions->withQueryString()->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/transactions/index.blade.php ENDPATH**/ ?>
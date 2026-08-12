

<?php $__env->startSection('title', 'Ledger'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-5 sm:mb-6 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-start">
    <div>
        <h1 class="cb-page-title">Ledger</h1>
        <p class="cb-subtitle">Gifts and contributions across your events</p>
    </div>
    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
        <a href="<?php echo e(route('client.chandlas.pdf')); ?>" target="_blank" class="cb-btn cb-btn-outline w-full sm:w-auto justify-center shrink-0">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
        <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
        <a href="<?php echo e(route('client.chandlas.create')); ?>" class="cb-btn cb-btn-gold w-full sm:w-auto justify-center shrink-0">
            <i class="fas fa-plus"></i>Add chandla
        </a>
        <?php endif; ?>
    </div>
</div>

<?php
    $totalAmount   = $chandlas->sum('amount');
    $chandlaAmount = $chandlas->where('category', 'chandla')->sum('amount');
    $coverCount    = $chandlas->where('category', 'cover')->count();
    $giftCount     = $chandlas->where('category', 'gift')->count();
    $gpayCount     = $chandlas->where('payment_method', 'gpay')->count();
    $gpayAmount    = $chandlas->where('payment_method', 'gpay')->sum('amount');
?>

<div class="cb-stat-strip-6 mb-6">
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon cb-stat-strip-6__icon--gold" aria-hidden="true"><i class="fas fa-indian-rupee-sign"></i></span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val text-cb-gold">₹<?php echo e(number_format($totalAmount, 0)); ?></p>
            <p class="cb-stat-strip-6__label">Total (all)</p>
        </div>
    </div>
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon cb-stat-strip-6__icon--green" aria-hidden="true"><i class="fas fa-money-bill-wave"></i></span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val text-emerald-600">₹<?php echo e(number_format($chandlaAmount, 0)); ?></p>
            <p class="cb-stat-strip-6__label">Cash</p>
        </div>
    </div>
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon cb-stat-strip-6__icon--sky" aria-hidden="true"><i class="fas fa-envelope-open"></i></span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val text-cb-navy"><?php echo e($coverCount); ?></p>
            <p class="cb-stat-strip-6__label">Cover count</p>
        </div>
    </div>
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon cb-stat-strip-6__icon--rose" aria-hidden="true"><i class="fas fa-gift"></i></span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val text-cb-navy"><?php echo e($giftCount); ?></p>
            <p class="cb-stat-strip-6__label">Gift count</p>
        </div>
    </div>
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon" style="background:#d1fae5;color:#065f46;" aria-hidden="true"><i class="fas fa-mobile-screen-button"></i></span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val" style="color:#065f46;"><?php echo e($gpayCount); ?></p>
            <p class="cb-stat-strip-6__label">GPay txns</p>
        </div>
    </div>
    <div class="cb-stat-strip-6__cell">
        <span class="cb-stat-strip-6__icon" style="background:#ccfbf1;color:#0f766e;" aria-hidden="true"><i class="fas fa-indian-rupee-sign"></i></span>
        <div class="cb-stat-strip-6__body">
            <p class="cb-stat-strip-6__val" style="color:#0f766e;">₹<?php echo e(number_format($gpayAmount, 0)); ?></p>
            <p class="cb-stat-strip-6__label">GPay total</p>
        </div>
    </div>
</div>

<div class="cb-card p-4 sm:p-6 mb-6">
    <form method="GET" action="<?php echo e(route('client.chandlas.index')); ?>" class="flex flex-wrap items-end gap-3 sm:gap-4">
        <div class="w-full sm:w-auto flex-1 min-w-[160px] max-w-[200px]">
            <span class="cb-label mb-1 block text-xs">Event</span>
            <select name="event_id" class="cb-field w-full">
                <option value="">All events</option>
                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($event->id); ?>" <?php echo e(request('event_id') == $event->id ? 'selected' : ''); ?>>
                        <?php echo e($event->title); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="w-full sm:w-auto flex-1 min-w-[140px] max-w-[180px]">
            <span class="cb-label mb-1 block text-xs">Category</span>
            <select name="category" class="cb-field w-full">
                <option value="">All categories</option>
                <option value="chandla" <?php echo e(request('category') === 'chandla' ? 'selected' : ''); ?>>Cash</option>
                <option value="cover" <?php echo e(request('category') === 'cover' ? 'selected' : ''); ?>>Cover</option>
                <option value="gift" <?php echo e(request('category') === 'gift' ? 'selected' : ''); ?>>Gift</option>
            </select>
        </div>
        <div class="w-full sm:w-auto flex-1 min-w-[160px] max-w-[200px]">
            <span class="cb-label mb-1 block text-xs">Payment</span>
            <select name="payment_method" class="cb-field w-full">
                <option value="">All payment methods</option>
                <option value="gpay" <?php echo e(request('payment_method') === 'gpay' ? 'selected' : ''); ?>>GPay</option>
                <option value="cash" <?php echo e(request('payment_method') === 'cash' ? 'selected' : ''); ?>>Cash</option>
            </select>
        </div>
        <div class="w-full sm:w-auto flex-1 min-w-[130px] max-w-[160px]">
            <span class="cb-label mb-1 block text-xs">Start date</span>
            <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="cb-field w-full">
        </div>
        <div class="w-full sm:w-auto flex-1 min-w-[130px] max-w-[160px]">
            <span class="cb-label mb-1 block text-xs">End date</span>
            <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="cb-field w-full">
        </div>
        <div class="w-full sm:w-auto shrink-0 pb-[1px]">
            <button type="submit" class="cb-btn cb-btn-navy w-full sm:w-auto justify-center">
                <i class="fas fa-filter"></i>Apply
            </button>
        </div>
    </form>
</div>


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    #chandlaTable_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.875rem;
        outline: none;
        transition: border-color .2s;
        min-width: 240px;
        max-width: 100%;
    }
    #chandlaTable_wrapper .dataTables_filter input:focus { border-color: #b8860b; }
    #chandlaTable_wrapper .dataTables_length select {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px 8px;
        font-size: 0.875rem;
    }
    #chandlaTable_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        padding: 4px 10px !important;
        margin: 0 2px;
        font-size: 0.8rem;
    }
    #chandlaTable_wrapper .dataTables_paginate .paginate_button.current {
        background: #1a3646 !important;
        border-color: #1a3646 !important;
        color: #fff !important;
    }
    #chandlaTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
        background: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
        color: #1a3646 !important;
    }
    #chandlaTable_wrapper .dataTables_info { font-size: 0.8rem; color: #64748b; }
    table#chandlaTable thead th { cursor: pointer; user-select: none; }
</style>

<div class="cb-table-wrap">
    <div class="overflow-x-auto">
        <table id="chandlaTable" class="cb-table min-w-full" style="width:100%">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Event</th>
                    <th>Giver</th>
                    <th>Category</th>
                    <th>Payment</th>
                    <th>Amount</th>
                    <th class="no-sort" style="width:120px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $chandlas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chandla): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="<?php echo e($chandla->payment_method === 'gpay' ? 'bg-teal-50/60' : ''); ?>">
                        <td data-order="<?php echo e($chandla->received_date->format('Y-m-d')); ?> <?php echo e($chandla->created_at->format('H:i:s')); ?>" class="whitespace-nowrap">
                            <div class="text-sm text-slate-800"><?php echo e($chandla->received_date->format('d/m/Y')); ?></div>
                            <div class="text-xs text-slate-500"><?php echo e($chandla->created_at->format('h:i A')); ?></div>
                        </td>
                        <td>
                            <div class="text-sm font-medium text-cb-navy max-w-[140px] sm:max-w-xs truncate" title="<?php echo e(optional($chandla->event)->title ?? 'Unknown Event'); ?>"><?php echo e(optional($chandla->event)->title ?? 'Unknown Event'); ?></div>
                        </td>
                        <td>
                            <div class="text-sm text-slate-800"><?php echo e($chandla->giver_name); ?></div>
                            <?php if($chandla->giver_phone): ?>
                                <div class="text-xs text-slate-500"><?php echo e($chandla->giver_phone); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800">
                                <?php echo e($chandla->category_label); ?>

                            </span>
                        </td>
                        <td>
                            <?php if($chandla->category === 'gift'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                                    <?php echo e($chandla->gift_item_name ?: '—'); ?>

                                </span>
                            <?php elseif($chandla->payment_method === 'gpay'): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800">
                                    <i class="fas fa-mobile-screen-button text-[0.6rem]"></i> GPay
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    <?php echo e($chandla->payment_method_label); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="whitespace-nowrap text-sm font-bold text-cb-navy" data-order="<?php echo e($chandla->amount); ?>">
                            <?php if($chandla->category === 'gift'): ?>
                                <?php if($chandla->gift_received): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-check text-[0.6rem]"></i> Given
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <i class="fas fa-xmark text-[0.6rem]"></i> Not Given
                                    </span>
                                <?php endif; ?>
                            <?php elseif($chandla->payment_method === 'gpay'): ?>
                                <span class="text-teal-700">₹<?php echo e(number_format($chandla->amount, 2)); ?></span>
                            <?php else: ?>
                                ₹<?php echo e(number_format($chandla->amount, 2)); ?>

                            <?php endif; ?>
                        </td>
                        <td class="whitespace-nowrap">
                            <div class="flex items-center justify-center gap-3">
                                <a href="<?php echo e(route('client.chandlas.show', $chandla->id)); ?>" class="text-cb-gold hover:opacity-80" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
                                <a href="<?php echo e(route('client.chandlas.edit', $chandla->id)); ?>" class="text-sky-600 hover:opacity-80" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('client.chandlas.clone', $chandla->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Clone this entry? A duplicate will be created with today\'s date.')">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="text-indigo-500 hover:opacity-80" title="Clone">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php if (\Illuminate\Support\Facades\Blade::check('canDelete')): ?>
                                <form action="<?php echo e(route('client.chandlas.destroy', $chandla->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:opacity-80" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
<a href="<?php echo e(route('client.chandlas.create')); ?>" class="cb-fab" title="Add chandla" aria-label="Add chandla">
    <i class="fas fa-plus"></i>
</a>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    var table = $('#chandlaTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100, 250],
        language: {
            search:       '<i class="fas fa-search" style="margin-right:6px;color:#94a3b8;"></i>',
            searchPlaceholder: 'Search name, event, amount…',
            lengthMenu:   'Show _MENU_ entries',
            info:         'Showing _START_ – _END_ of _TOTAL_ entries',
            infoEmpty:    'No entries found',
            infoFiltered: '(filtered from _MAX_ total)',
            paginate: {
                previous: '‹',
                next:     '›',
            }
        },
        columnDefs: [
            { orderable: false, targets: -1 }   // Actions column not sortable
        ],
        dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 sm:px-6 py-4"lf>rt<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-4 sm:px-6 py-4 border-t border-slate-100"ip>',
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/chandlas/index.blade.php ENDPATH**/ ?>
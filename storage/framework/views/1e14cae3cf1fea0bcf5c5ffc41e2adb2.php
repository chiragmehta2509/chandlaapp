<?php $__env->startSection('title', $event->title . ' — Ganpati Special'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    #ganpatiTable_wrapper .dataTables_filter input {
        border: 1px solid var(--gp-border);
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.875rem;
        outline: none;
        background: var(--cb-input-bg);
        color: var(--cb-navy);
        transition: border-color .2s;
        min-width: 200px;
        max-width: 100%;
    }
    #ganpatiTable_wrapper .dataTables_filter input:focus {
        border-color: var(--gp-orange);
    }
    #ganpatiTable_wrapper .dataTables_length select {
        border: 1px solid var(--gp-border);
        border-radius: 8px;
        padding: 4px 8px;
        font-size: 0.875rem;
        background: var(--cb-input-bg);
        color: var(--cb-navy);
    }
    #ganpatiTable_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        padding: 4px 10px !important;
        margin: 0 2px;
        font-size: 0.8rem;
        color: var(--cb-navy) !important;
    }
    #ganpatiTable_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--gp-btn-bg) !important;
        border-color: var(--gp-orange) !important;
        color: #fff !important;
    }
    #ganpatiTable_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
        background: var(--gp-bg-accent) !important;
        border-color: var(--gp-border) !important;
        color: var(--gp-text) !important;
    }
    #ganpatiTable_wrapper .dataTables_info {
        font-size: 0.8rem;
        color: var(--cb-muted);
    }
    table#ganpatiTable thead th { cursor: pointer; user-select: none; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-5">

    
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-start gap-3">
            <div class="min-w-0">
                <p class="gp-page-label mb-0.5">🪔 Ganpati Special</p>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-bold text-cb-navy leading-tight"><?php echo e($event->title); ?></h1>
                    <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
                    <a href="<?php echo e(route('client.ganpati.edit', $event->id)); ?>" class="text-sky-500 hover:text-sky-700 mt-1" title="Edit Event">
                        <i class="fas fa-pencil text-sm" aria-hidden="true"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <p class="text-sm cb-subtitle mt-1">
                    <i class="fas fa-calendar-day mr-1" aria-hidden="true"></i>
                    <?php echo e(optional($event->event_date)->format('d M Y') ?? '—'); ?>

                    <?php if($event->venue): ?>
                        &nbsp;·&nbsp;<i class="fas fa-map-marker-alt mr-1" aria-hidden="true"></i><?php echo e($event->venue); ?>

                    <?php endif; ?>
                </p>
            </div>
        </div>
        <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="<?php echo e(route('client.ganpati.chandla.create', $event->id)); ?>" class="gp-btn min-h-[2.5rem]">
                <i class="fas fa-plus" aria-hidden="true"></i> Add Entry
            </a>
            <a href="<?php echo e(route('client.ganpati.pdf', $event->id)); ?>"
               class="gp-btn gp-btn--outline min-h-[2.5rem]">
                <i class="fas fa-file-pdf" aria-hidden="true"></i> Download PDF
            </a>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <?php
        $statCards = [
            ['label' => 'Total Amount', 'value' => '₹' . number_format($totalAmount, 2), 'icon' => 'fa-indian-rupee-sign', 'bg' => 'rgba(249,115,22,0.12)', 'color' => 'var(--gp-orange)'],
            ['label' => 'Cash', 'value' => '₹' . number_format($cashAmount, 2), 'icon' => 'fa-money-bill-wave', 'bg' => 'rgba(34,197,94,0.12)', 'color' => '#16a34a'],
            ['label' => 'GPay', 'value' => '₹' . number_format($gpayAmount, 2), 'icon' => 'fa-mobile-screen-button', 'bg' => 'rgba(59,130,246,0.12)', 'color' => '#2563eb'],
            ['label' => 'Entries', 'value' => $totalEntries, 'icon' => 'fa-list-check', 'bg' => 'rgba(147,51,234,0.12)', 'color' => '#9333ea'],
        ];
        ?>
        <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="gp-stat">
            <div class="flex items-center justify-between gap-2 mb-2">
                <p class="gp-stat__label"><?php echo e($s['label']); ?></p>
                <div class="gp-stat__icon" style="background:<?php echo e($s['bg']); ?>; color:<?php echo e($s['color']); ?>;">
                    <i class="fas <?php echo e($s['icon']); ?> text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="gp-stat__value"><?php echo e($s['value']); ?></p>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <a href="<?php echo e(route('client.ganpati.scanner', $event->id)); ?>" class="gp-action">
            <div class="gp-action__icon gp-action__icon--orange">
                <i class="fas fa-qrcode text-lg" style="color:var(--gp-orange);" aria-hidden="true"></i>
            </div>
            <p class="gp-action__label">UPI Scanner</p>
            <p class="gp-action__sub">Upload / view QR</p>
        </a>
        <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
        <a href="<?php echo e(route('client.ganpati.edit', $event->id)); ?>" class="gp-action">
            <div class="gp-action__icon gp-action__icon--sky">
                <i class="fas fa-pencil text-lg" style="color:#0ea5e9;" aria-hidden="true"></i>
            </div>
            <p class="gp-action__label">Edit Event</p>
            <p class="gp-action__sub">Update details</p>
        </a>
        <form method="POST" action="<?php echo e(route('client.ganpati.destroy', $event->id)); ?>"
              onsubmit="return confirm('Delete this Ganpati event and ALL its entries? This cannot be undone.')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="gp-action">
                <div class="gp-action__icon gp-action__icon--red">
                    <i class="fas fa-trash text-lg" style="color:#ef4444;" aria-hidden="true"></i>
                </div>
                <p class="gp-action__label">Delete</p>
                <p class="gp-action__sub">Remove event</p>
            </button>
        </form>
        <?php endif; ?>
    </div>

    
    <?php if($event->upi_id || $event->gpay_qr_image): ?>
    <div class="gp-qr-box">
        <?php if($event->gpay_qr_image): ?>
        <img src="<?php echo e(asset('storage/' . $event->gpay_qr_image)); ?>" alt="UPI QR Scanner"
             class="h-20 w-20 rounded-lg object-contain bg-white shadow-sm"
             style="border:1.5px solid var(--gp-border);">
        <?php endif; ?>
        <div class="min-w-0 flex-1">
            <p class="gp-qr-box__label">UPI Scanner</p>
            <?php if($event->upi_id): ?>
                <p class="gp-qr-box__value"><?php echo e($event->upi_id); ?></p>
                <a href="<?php echo e(route('client.ganpati.qr', $event->id)); ?>" target="_blank" class="gp-qr-box__link">
                    <i class="fas fa-qrcode" aria-hidden="true"></i> View UPI QR
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="cb-card overflow-hidden" style="border-color:var(--gp-border-soft);">
        <div class="gp-table-header">
            <div>
                <p class="gp-table-header__title">Chanda Entries</p>
                <p class="gp-table-header__sub"><?php echo e($totalEntries); ?> total records</p>
            </div>
            <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
            <a href="<?php echo e(route('client.ganpati.chandla.create', $event->id)); ?>" class="gp-btn" style="padding:.375rem .875rem; font-size:.8rem;">
                <i class="fas fa-plus" aria-hidden="true"></i> Add
            </a>
            <?php endif; ?>
        </div>

        <?php if($event->chandlas->isEmpty()): ?>
        <div class="p-8 text-center">
            <span style="font-size:2.5rem; display:block; margin-bottom:.75rem;">📋</span>
            <p class="text-sm cb-subtitle mb-4">No entries yet. Add the first chanda entry!</p>
            <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
            <a href="<?php echo e(route('client.ganpati.chandla.create', $event->id)); ?>" class="gp-btn">
                <i class="fas fa-plus" aria-hidden="true"></i> Add First Entry
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table id="ganpatiTable" class="w-full gp-table">
                <thead>
                    <tr>
                        <th style="width:2.5rem;">#</th>
                        <th>Name</th>
                        <th class="hidden sm:table-cell">Phone</th>
                        <th>Date & Time</th>
                        <th>Method</th>
                        <th class="text-right">Amount</th>
                        <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?> <th style="text-align:center;">Actions</th> <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $event->chandlas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $chandla): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="font-size:.7rem; color:var(--gp-serial-color);"><?php echo e($i + 1); ?></td>
                        <td>
                            <span class="font-semibold text-cb-navy"><?php echo e($chandla->giver_name); ?></span>
                            <?php if($chandla->giver_address): ?>
                                <span class="block text-xs cb-subtitle truncate max-w-[14rem]"><?php echo e($chandla->giver_address); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="hidden sm:table-cell text-xs"><?php echo e($chandla->giver_phone ?? '—'); ?></td>
                        <td class="text-xs font-variant-numeric">
                            <span class="block font-medium text-cb-navy"><?php echo e(optional($chandla->created_at)->format('d M Y') ?? '—'); ?></span>
                            <span class="block text-[0.65rem] text-slate-400 mt-0.5"><?php echo e(optional($chandla->created_at)->format('h:i A') ?? ''); ?></span>
                        </td>
                        <td>
                            <?php
                            $methodBadge = match($chandla->payment_method) {
                                'cash' => ['label' => 'Cash', 'bg' => 'rgba(34,197,94,0.12)', 'color' => '#166534'],
                                'gpay' => ['label' => 'GPay', 'bg' => 'rgba(59,130,246,0.12)', 'color' => '#1e40af'],
                                default => ['label' => ucfirst($chandla->payment_method ?? 'Other'), 'bg' => 'rgba(100,116,139,0.12)', 'color' => 'var(--cb-muted)'],
                            };
                            ?>
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                  style="background:<?php echo e($methodBadge['bg']); ?>; color:<?php echo e($methodBadge['color']); ?>;">
                                <?php echo e($methodBadge['label']); ?>

                            </span>
                        </td>
                        <td style="text-align:right; font-weight:600; font-variant-numeric:tabular-nums;">
                            ₹<?php echo e(number_format((float)$chandla->amount, 2)); ?>

                        </td>
                        <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
                        <td style="text-align:center;">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?php echo e(route('client.ganpati.chandla.edit', [$event->id, $chandla->id])); ?>"
                                   class="gp-back-btn h-7 w-7 rounded-md hover:text-sky-600" title="Edit">
                                    <i class="fas fa-pencil text-xs" aria-hidden="true"></i>
                                </a>
                                <form method="POST"
                                      action="<?php echo e(route('client.ganpati.chandla.destroy', [$event->id, $chandla->id])); ?>"
                                      onsubmit="return confirm('Delete this entry?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit"
                                            class="gp-back-btn h-7 w-7 rounded-md hover:text-red-500 hover:border-red-200"
                                            title="Delete">
                                        <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="gp-total-label">Total Collection</td>
                        <td class="gp-total-value">₹<?php echo e(number_format($totalAmount, 2)); ?></td>
                        <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?> <td></td> <?php endif; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    var tableEl = $('#ganpatiTable');
    if (tableEl.length && tableEl.find('tbody tr').length > 0 && !tableEl.find('tbody td').hasClass('dataTables_empty')) {
        tableEl.DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100, 250],
            language: {
                search: '<i class="fas fa-search" style="margin-right:6px;color:var(--gp-orange);"></i>',
                searchPlaceholder: 'Search name, phone, amount…',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ – _END_ of _TOTAL_ entries',
                infoEmpty: 'No entries found',
                infoFiltered: '(filtered from _MAX_ total)',
                paginate: {
                    previous: '‹',
                    next: '›',
                }
            },
            columnDefs: [
                { orderable: false, targets: -1 }
            ],
            dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 sm:px-5 py-3 border-b border-slate-100 dark:border-slate-800"lf>rt<"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-4 sm:px-5 py-3 border-t border-slate-100 dark:border-slate-800"ip>',
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/ganpati/show.blade.php ENDPATH**/ ?>
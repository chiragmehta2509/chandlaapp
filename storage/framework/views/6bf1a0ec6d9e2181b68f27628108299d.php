<?php $__env->startSection('title', 'Ganpati Special'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 sm:mb-8 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between lg:gap-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-5 min-w-0 flex-1">
        <div class="gp-event-card__icon shrink-0 h-12 w-12 sm:h-14 sm:w-14 rounded-2xl shadow-sm" aria-hidden="true">
            <span style="font-size:1.6rem;">🪔</span>
        </div>
        <div class="min-w-0">
            <h1 class="cb-page-title text-2xl sm:text-3xl leading-tight gp-page-title">Ganpati Special</h1>
            <p class="cb-subtitle mt-1.5 max-w-xl text-sm sm:text-base leading-relaxed">
                Collect Ganpati Utsav chanda entries — <strong class="gp-page-label" style="text-transform:none; letter-spacing:normal; font-size:inherit;">free &amp; unlimited</strong> for all users.
            </p>
        </div>
    </div>
    <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
    <?php if($events->isEmpty()): ?>
    <a href="<?php echo e(route('client.ganpati.create')); ?>" class="gp-btn w-full lg:w-auto shrink-0 min-h-[2.75rem] px-5 touch-manipulation">
        <i class="fas fa-plus text-sm" aria-hidden="true"></i>
        <span>New Ganpati Event</span>
    </a>
    <?php endif; ?>
    <?php endif; ?>
</div>


<div class="gp-banner">
    <div class="gp-banner__inner">
        <div class="flex-1 min-w-0">
            <p class="gp-banner__title">🙏 Ganpati Bappa Morya!</p>
            <p class="gp-banner__body">
                This section is dedicated to Ganpati Utsav chanda collection. Create your single dedicated event for Ganpati, add your UPI scanner, and record all chanda entries. (Use the main Events module for other events).
                <a href="<?php echo e(route('client.ganpati.index')); ?>" class="gp-qr-box__link" style="display:inline; margin-left:4px;">Download the PDF</a>
                at any time — completely free.
            </p>
        </div>
        <div class="gp-banner__badge">
            <i class="fas fa-infinity" aria-hidden="true"></i>
            Unlimited entries
        </div>
    </div>
</div>


<form method="GET" action="<?php echo e(route('client.ganpati.index')); ?>" class="mb-6">
    <div class="relative max-w-sm">
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm" aria-hidden="true">
            <i class="fas fa-search"></i>
        </span>
        <input type="search" name="search" value="<?php echo e(request('search')); ?>"
               placeholder="Search events…"
               class="cb-field w-full min-h-[2.75rem] pl-10 pr-3 text-sm">
    </div>
</form>

<?php if($events->isEmpty()): ?>
    <div class="gp-empty">
        <div class="gp-empty__icon">
            <span style="font-size:2rem;">🪔</span>
        </div>
        <h2 class="text-base font-bold text-cb-navy mb-2">No Ganpati events yet</h2>
        <p class="text-sm cb-subtitle mb-5 max-w-xs mx-auto">Create your first Ganpati event to start collecting chanda entries.</p>
        <?php if (\Illuminate\Support\Facades\Blade::check('canEdit')): ?>
        <a href="<?php echo e(route('client.ganpati.create')); ?>" class="gp-btn">
            <i class="fas fa-plus" aria-hidden="true"></i> Create First Event
        </a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
        <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('client.ganpati.show', $event->id)); ?>" class="gp-event-card">
            <div class="gp-event-card__accent"></div>
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="gp-event-card__icon">
                    <span style="font-size:1.25rem;">🪔</span>
                </div>
                <span class="gp-event-card__badge ml-auto"><?php echo e($event->chandlas_count ?? 0); ?> entries</span>
            </div>
            <h2 class="gp-event-card__title"><?php echo e($event->title); ?></h2>
            <p class="gp-event-card__meta">
                <i class="fas fa-calendar-day mr-1" aria-hidden="true"></i>
                <?php echo e(optional($event->event_date)->format('d M Y') ?? '—'); ?>

                <?php if($event->venue): ?>
                    · <i class="fas fa-map-marker-alt mr-1" aria-hidden="true"></i><?php echo e($event->venue); ?>

                <?php endif; ?>
            </p>
            <div class="gp-event-card__footer">
                <span class="gp-event-card__footer-text">View entries &amp; download PDF</span>
                <i class="fas fa-arrow-right gp-event-card__arrow" aria-hidden="true"></i>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/chandlabook/public_html/resources/views/client/ganpati/index.blade.php ENDPATH**/ ?>
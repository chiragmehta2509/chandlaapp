
<?php
    $wrapClass = $wrapClass ?? '';
    $titleClass = $titleClass ?? '';
    $heading = $heading ?? 'Schedule of events';
    $rowClass = $rowClass ?? '';
    $titleCellClass = $titleCellClass ?? '';
    $dotsClass = $dotsClass ?? '';
    $whenClass = $whenClass ?? '';
?>
<?php if(!empty($d['schedule_events']) && is_array($d['schedule_events'])): ?>
<div class="<?php echo e($wrapClass); ?>">
    <h3 class="<?php echo e($titleClass); ?>"><?php echo e($heading); ?></h3>
    <?php $__currentLoopData = $d['schedule_events']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(empty($ev['title'])): ?> <?php continue; ?> <?php endif; ?>
        <?php
            $schedDate = '';
            if (!empty($ev['date'])) {
                try { $schedDate = \Carbon\Carbon::parse($ev['date'])->format('d/m/Y'); } catch (\Throwable $e) { $schedDate = (string) $ev['date']; }
            }
            $schedTime = trim((string) ($ev['time'] ?? ''));
            if ($schedTime !== '') {
                try {
                    $schedTime = \Carbon\Carbon::parse($schedTime)->format('g:i A');
                } catch (\Throwable $e) {
                    // keep $schedTime as-is if it can't be parsed
                }
            }
            $schedRight = $schedDate;
            if ($schedTime !== '') {
                $schedRight .= ($schedRight !== '' ? ' · ' : '') . $schedTime;
            }
        ?>
        <div class="<?php echo e($rowClass); ?>">
            <span class="<?php echo e($titleCellClass); ?>"><?php echo e($ev['title']); ?></span>
            <span class="<?php echo e($dotsClass); ?>" aria-hidden="true"></span>
            <span class="<?php echo e($whenClass); ?>"><?php echo e($schedRight !== '' ? $schedRight : '—'); ?></span>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<?php /**PATH /home/chandlabook/public_html/resources/views/client/marriage-invitations/partials/schedule-block.blade.php ENDPATH**/ ?>
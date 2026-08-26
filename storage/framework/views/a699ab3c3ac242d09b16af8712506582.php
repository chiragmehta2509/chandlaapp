<?php
    $scheduleRows = $scheduleOld ?? [];
    if (!is_array($scheduleRows)) {
        $scheduleRows = [];
    }
    $scheduleRows = array_values($scheduleRows);
    while (count($scheduleRows) < 8) {
        $scheduleRows[] = ['title' => '', 'date' => '', 'time' => ''];
    }
    $scheduleRows = array_slice($scheduleRows, 0, 8);
?>
<div class="rounded-xl sm:rounded-lg border border-slate-200 bg-slate-50/80 p-3 sm:p-4">
    <p class="text-xs text-slate-500 mb-3 max-w-2xl">Add functions such as <em>Grah Shanti</em>, dinner, or barat. One row per event. Pick the date and time using the controls below.</p>

    <div class="hidden md:grid md:grid-cols-12 gap-2 px-1 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-200/80">
        <div class="md:col-span-5">Event name</div>
        <div class="md:col-span-3">Date</div>
        <div class="md:col-span-4">Time</div>
    </div>

    <div class="space-y-3 sm:space-y-0 sm:divide-y sm:divide-slate-200/60">
    <?php $__currentLoopData = $scheduleRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="md:grid md:grid-cols-12 md:gap-2 md:items-end md:py-2.5 rounded-lg sm:rounded-none bg-white sm:bg-transparent border border-slate-200/80 sm:border-0 p-3 sm:p-0 shadow-sm sm:shadow-none">
            <div class="md:col-span-5 mb-2 md:mb-0">
                <label class="md:sr-only block text-xs font-medium text-slate-600 mb-1" for="sched_<?php echo e($fieldKey); ?>_<?php echo e($idx); ?>_title">Event name <span class="text-slate-400 font-normal">(<?php echo e($idx + 1); ?>)</span></label>
                <input type="text" name="<?php echo e($fieldKey); ?>[<?php echo e($idx); ?>][title]" id="sched_<?php echo e($fieldKey); ?>_<?php echo e($idx); ?>_title"
                       value="<?php echo e($row['title'] ?? ''); ?>" class="cb-field w-full" placeholder="e.g. Grah Shanti" autocomplete="off">
            </div>
            <div class="md:col-span-3 mb-2 md:mb-0">
                <label class="md:sr-only block text-xs font-medium text-slate-600 mb-1" for="sched_<?php echo e($fieldKey); ?>_<?php echo e($idx); ?>_date">Date</label>
                <input type="date" name="<?php echo e($fieldKey); ?>[<?php echo e($idx); ?>][date]" id="sched_<?php echo e($fieldKey); ?>_<?php echo e($idx); ?>_date"
                       value="<?php echo e($row['date'] ?? ''); ?>" class="cb-field w-full min-h-[2.75rem]" autocomplete="off" data-min-today="1">
            </div>
            <?php
                $rawTime = trim((string) ($row['time'] ?? ''));
                $timePickerVal = '';
                if ($rawTime !== '') {
                    try {
                        $timePickerVal = \Carbon\Carbon::parse($rawTime)->format('H:i');
                    } catch (\Throwable $e) {
                        $timePickerVal = '';
                    }
                }
            ?>
            <div class="md:col-span-4">
                <label class="md:sr-only block text-xs font-medium text-slate-600 mb-1" for="sched_<?php echo e($fieldKey); ?>_<?php echo e($idx); ?>_time">Time</label>
                <input type="time" name="<?php echo e($fieldKey); ?>[<?php echo e($idx); ?>][time]" id="sched_<?php echo e($fieldKey); ?>_<?php echo e($idx); ?>_time"
                       value="<?php echo e($timePickerVal); ?>" step="60" class="cb-field w-full min-h-[2.75rem]" autocomplete="off">
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<script>
(function () {
    function todayLocal() {
        var d = new Date();
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var dd = String(d.getDate()).padStart(2, '0');
        return d.getFullYear() + '-' + mm + '-' + dd;
    }

    function enforceMin(inp) {
        var today = todayLocal();
        inp.min = today;
        if (inp.value && inp.value < today) {
            inp.value = today;
        }
    }

    document.querySelectorAll('input[type="date"][data-min-today]').forEach(function (inp) {
        enforceMin(inp);
        inp.addEventListener('change', function () { enforceMin(inp); });
        inp.addEventListener('blur',   function () { enforceMin(inp); });
        inp.addEventListener('input',  function () {
            var today = todayLocal();
            if (inp.value && inp.value < today) { inp.value = today; }
        });
    });
})();
</script>
<?php /**PATH /home/chandlabook/public_html/resources/views/client/marriage-invitations/partials/schedule-fields.blade.php ENDPATH**/ ?>
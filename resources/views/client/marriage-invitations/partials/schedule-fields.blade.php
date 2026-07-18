@php
    $scheduleRows = $scheduleOld ?? [];
    if (!is_array($scheduleRows)) {
        $scheduleRows = [];
    }
    $scheduleRows = array_values($scheduleRows);
    while (count($scheduleRows) < 8) {
        $scheduleRows[] = ['title' => '', 'date' => '', 'time' => ''];
    }
    $scheduleRows = array_slice($scheduleRows, 0, 8);
@endphp
<div class="rounded-xl sm:rounded-lg border border-slate-200 bg-slate-50/80 p-3 sm:p-4">
    <p class="text-xs text-slate-500 mb-3 max-w-2xl">Add functions such as <em>Grah Shanti</em>, dinner, or barat. One row per event. Pick the date and time using the controls below.</p>

    <div class="hidden md:grid md:grid-cols-12 gap-2 px-1 pb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-200/80">
        <div class="md:col-span-5">Event name</div>
        <div class="md:col-span-3">Date</div>
        <div class="md:col-span-4">Time</div>
    </div>

    <div class="space-y-3 sm:space-y-0 sm:divide-y sm:divide-slate-200/60">
    @foreach($scheduleRows as $idx => $row)
        <div class="md:grid md:grid-cols-12 md:gap-2 md:items-end md:py-2.5 rounded-lg sm:rounded-none bg-white sm:bg-transparent border border-slate-200/80 sm:border-0 p-3 sm:p-0 shadow-sm sm:shadow-none">
            <div class="md:col-span-5 mb-2 md:mb-0">
                <label class="md:sr-only block text-xs font-medium text-slate-600 mb-1" for="sched_{{ $fieldKey }}_{{ $idx }}_title">Event name <span class="text-slate-400 font-normal">({{ $idx + 1 }})</span></label>
                <input type="text" name="{{ $fieldKey }}[{{ $idx }}][title]" id="sched_{{ $fieldKey }}_{{ $idx }}_title"
                       value="{{ $row['title'] ?? '' }}" class="cb-field w-full" placeholder="e.g. Grah Shanti" autocomplete="off">
            </div>
            <div class="md:col-span-3 mb-2 md:mb-0">
                <label class="md:sr-only block text-xs font-medium text-slate-600 mb-1" for="sched_{{ $fieldKey }}_{{ $idx }}_date">Date</label>
                <input type="date" name="{{ $fieldKey }}[{{ $idx }}][date]" id="sched_{{ $fieldKey }}_{{ $idx }}_date"
                       value="{{ $row['date'] ?? '' }}" class="cb-field w-full min-h-[2.75rem]" autocomplete="off" data-min-today="1">
            </div>
            @php
                $rawTime = trim((string) ($row['time'] ?? ''));
                $timePickerVal = '';
                if ($rawTime !== '') {
                    try {
                        $timePickerVal = \Carbon\Carbon::parse($rawTime)->format('H:i');
                    } catch (\Throwable $e) {
                        $timePickerVal = '';
                    }
                }
            @endphp
            <div class="md:col-span-4">
                <label class="md:sr-only block text-xs font-medium text-slate-600 mb-1" for="sched_{{ $fieldKey }}_{{ $idx }}_time">Time</label>
                <input type="time" name="{{ $fieldKey }}[{{ $idx }}][time]" id="sched_{{ $fieldKey }}_{{ $idx }}_time"
                       value="{{ $timePickerVal }}" step="60" class="cb-field w-full min-h-[2.75rem]" autocomplete="off">
            </div>
        </div>
    @endforeach
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

@php
    $type = $field['type'] ?? 'text';
    $required = !empty($field['required']);
    $id = 'f_' . $fieldKey;
    $hasFormDefaults = isset($formDefaults) && is_array($formDefaults);
    if ($type === 'schedule') {
        $scheduleForOld = $hasFormDefaults
            ? old($fieldKey, $formDefaults[$fieldKey] ?? [])
            : old($fieldKey);
    } else {
        $textOld = $hasFormDefaults
            ? old($fieldKey, (string) (data_get($formDefaults, $fieldKey) ?? ''))
            : old($fieldKey);
    }
@endphp
<div class="min-w-0">
    @if($type !== 'schedule')
    <label class="cb-label cb-label--classic block mb-1" for="{{ $id }}">
        {{ $field['label'] }}
        @if($required)
            <span class="text-red-500" aria-hidden="true">*</span>
        @endif
    </label>
    @endif
    @if($type === 'textarea')
        <textarea name="{{ $fieldKey }}" id="{{ $id }}" rows="{{ $field['rows'] ?? 3 }}" class="cb-field w-full mt-1.5" {{ $required ? 'required' : '' }} placeholder="{{ $field['placeholder'] ?? '' }}">{{ $textOld }}</textarea>
    @elseif($type === 'date')
        <input type="date" name="{{ $fieldKey }}" id="{{ $id }}" value="{{ $textOld }}" class="cb-field w-full mt-1.5 min-h-[2.75rem]" {{ $required ? 'required' : '' }} autocomplete="off"@if($fieldKey === 'wedding_date') data-min-today="1"@endif>
        @if($fieldKey === 'wedding_date')
        <script>
        (function () {
            var inp = document.getElementById('{{ $id }}');
            if (!inp) return;

            function todayLocal() {
                var d = new Date();
                var mm = String(d.getMonth() + 1).padStart(2, '0');
                var dd = String(d.getDate()).padStart(2, '0');
                return d.getFullYear() + '-' + mm + '-' + dd;
            }

            function enforce() {
                var today = todayLocal();
                inp.min = today;
                if (inp.value && inp.value < today) {
                    inp.value = today;
                }
            }

            // Set min from client clock on load
            enforce();

            // Re-check whenever the user changes the field
            inp.addEventListener('change', enforce);
            inp.addEventListener('blur', enforce);

            // Also block manual keyboard entry in real-time
            inp.addEventListener('input', function () {
                var today = todayLocal();
                if (inp.value && inp.value < today) {
                    inp.value = today;
                }
            });
        })();
        </script>
        @endif
    @elseif($type === 'time')
        @php
            $timeVal = '';
            if ($textOld !== '') {
                try {
                    $timeVal = \Carbon\Carbon::parse(trim($textOld))->format('H:i');
                } catch (\Throwable) {
                    $timeVal = '';
                }
            }
        @endphp
        <input type="time" name="{{ $fieldKey }}" id="{{ $id }}" value="{{ $timeVal }}" step="60" class="cb-field w-full mt-1.5 min-h-[2.75rem]" {{ $required ? 'required' : '' }} autocomplete="off">
    @elseif($type === 'image')
        @if(isset($editCoupleImage) && is_array($editCoupleImage) && $fieldKey === 'couple_image' && !empty($editCoupleImage['had_path']))
            @if(!empty($editCoupleImage['ok']) && !empty($editCoupleImage['url']))
            <div class="mb-2 rounded-lg border border-slate-200 overflow-hidden max-w-xs bg-slate-50" id="edit-cur-img-wrap">
                <img
                    src="{{ $editCoupleImage['url'] }}"
                    alt="Current photo"
                    class="w-full h-auto object-cover block"
                    width="300"
                    height="300"
                    loading="lazy"
                    onerror="this.classList.add('hidden'); var el=document.getElementById('edit-cur-img-fb'); if(el){el.classList.remove('hidden');}"
                >
                <p id="edit-cur-img-fb" class="hidden text-sm text-amber-800 px-3 py-2 bg-amber-50">Preview could not be loaded. Run <code class="text-xs">php artisan storage:link</code> or re-upload the image.</p>
            </div>
            @else
            <p class="mb-2 text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">Previous photo file is missing. Choose a new file below to replace it.</p>
            @endif
        @endif
        <div class="mt-1.5">
            <input type="file" name="{{ $fieldKey }}" id="{{ $id }}" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-600 file:mr-2 sm:file:mr-3 file:py-2.5 file:px-3 sm:file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-900 hover:file:bg-amber-200 cursor-pointer border border-slate-200 rounded-lg bg-white w-full min-w-0">
        </div>
        <p class="text-xs text-slate-500 mt-1.5">JPG, PNG, or WebP. Shown on the printed card.@if($hasFormDefaults) <span class="whitespace-nowrap sm:whitespace-normal"> Leave empty to keep the current image.</span>@endif</p>
    @elseif($type === 'schedule')
        @include('client.marriage-invitations.partials.schedule-fields', [
            'fieldKey' => $fieldKey,
            'field' => $field,
            'scheduleOld' => $scheduleForOld,
        ])
    @else
        <input type="text" name="{{ $fieldKey }}" id="{{ $id }}" value="{{ $textOld }}" class="cb-field w-full mt-1.5" {{ $required ? 'required' : '' }} placeholder="{{ $field['placeholder'] ?? '' }}" autocomplete="off">
    @endif
    @error($fieldKey)
        <p class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
    @enderror
</div>

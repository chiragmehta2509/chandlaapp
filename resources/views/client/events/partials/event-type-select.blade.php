@php
    $typesList = $eventTypes ?? collect();
    $selectedRaw = old('event_type_id', $selectedId ?? '');
    $selectedIdStr = $selectedRaw !== null && $selectedRaw !== '' ? (string) $selectedRaw : '';
    $selectedLabel = 'Select type';
    if ($selectedIdStr !== '') {
        $match = $typesList->first(fn ($t) => (string) $t->id === $selectedIdStr);
        if ($match) {
            $selectedLabel = $match->name;
        }
    }
    $typeCount = $typesList->count();
    $listScrollable = $typeCount > 10;
@endphp
<div class="relative z-10 isolate" id="event-type-widget">
    <select id="event-type-select" name="event_type_id" class="sr-only" tabindex="-1" aria-labelledby="event-type-label">
        <option value="" {{ $selectedIdStr === '' ? 'selected' : '' }}>Select type</option>
        @foreach($typesList as $eventType)
            <option value="{{ $eventType->id }}" {{ $selectedIdStr === (string) $eventType->id ? 'selected' : '' }}>
                {{ $eventType->name }}
            </option>
        @endforeach
    </select>
    <button
        type="button"
        id="event-type-trigger"
        class="group flex w-full items-center justify-between gap-3 min-h-[48px] rounded-[var(--cb-radius)] border border-[rgba(26,54,70,0.12)] bg-[var(--cb-input-bg)] px-4 text-left text-[0.95rem] font-medium text-[var(--cb-navy-soft)] shadow-[inset_0_1px_0_rgba(255,255,255,0.6)] transition hover:border-[rgba(184,134,11,0.35)] hover:bg-[#faf8f4] focus:outline-none focus-visible:border-[rgba(184,134,11,0.45)] focus-visible:ring-[3px] focus-visible:ring-[rgba(184,134,11,0.2)] focus-visible:bg-white {{ $selectedIdStr === '' ? 'cb-event-type-trigger--placeholder' : '' }}"
        aria-haspopup="listbox"
        aria-expanded="false"
        aria-controls="event-type-listbox"
        aria-labelledby="event-type-label"
    >
        <span class="js-event-type-text min-w-0 truncate">{{ $selectedLabel }}</span>
        <i class="fas fa-chevron-down js-event-type-chev inline-block shrink-0 text-[0.7rem] text-slate-500 transition-[transform,color] duration-200 group-hover:text-[var(--cb-navy)]" style="transform: rotate(0deg)" aria-hidden="true"></i>
    </button>
    <ul
        id="event-type-listbox"
        role="listbox"
        class="absolute left-0 right-0 top-full z-[80] mt-1.5 hidden rounded-[var(--cb-radius)] border border-[rgba(26,54,70,0.12)] bg-white shadow-xl shadow-slate-900/12 ring-1 ring-black/[0.04] py-1 {{ $listScrollable ? 'max-h-60 overflow-y-auto overscroll-contain' : 'overflow-hidden' }}"
        aria-labelledby="event-type-label"
    >
        @foreach($typesList as $eventType)
            <li
                role="option"
                tabindex="-1"
                data-value="{{ $eventType->id }}"
                aria-selected="{{ $selectedIdStr === (string) $eventType->id ? 'true' : 'false' }}"
                class="events-status-opt cb-event-type-opt"
            >
                <i class="fas fa-tag cb-event-type-opt__icon w-4 shrink-0 text-center text-sm" aria-hidden="true"></i>
                <span class="cb-event-type-opt__label">{{ $eventType->name }}</span>
            </li>
        @endforeach
        <li
            role="option"
            tabindex="-1"
            data-value=""
            aria-selected="false"
            id="event-type-clear-option"
            class="events-status-opt cb-event-type-opt cb-event-type-opt--clear {{ $selectedIdStr === '' ? 'hidden' : '' }}"
        >
            <i class="fas fa-arrow-rotate-left cb-event-type-opt__icon w-4 shrink-0 text-center text-xs opacity-80" aria-hidden="true"></i>
            <span class="cb-event-type-opt__label">Clear selection</span>
        </li>
    </ul>
</div>

@include('client.events.partials.event-type-select-scripts')

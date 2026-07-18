{{-- Expects $d, optional $wrapClass, $titleClass, $heading, $rowClass, $titleCellClass, $dotsClass, $whenClass --}}
@php
    $wrapClass = $wrapClass ?? '';
    $titleClass = $titleClass ?? '';
    $heading = $heading ?? 'Schedule of events';
    $rowClass = $rowClass ?? '';
    $titleCellClass = $titleCellClass ?? '';
    $dotsClass = $dotsClass ?? '';
    $whenClass = $whenClass ?? '';
@endphp
@if(!empty($d['schedule_events']) && is_array($d['schedule_events']))
<div class="{{ $wrapClass }}">
    <h3 class="{{ $titleClass }}">{{ $heading }}</h3>
    @foreach($d['schedule_events'] as $ev)
        @if(empty($ev['title'])) @continue @endif
        @php
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
        @endphp
        <div class="{{ $rowClass }}">
            <span class="{{ $titleCellClass }}">{{ $ev['title'] }}</span>
            <span class="{{ $dotsClass }}" aria-hidden="true"></span>
            <span class="{{ $whenClass }}">{{ $schedRight !== '' ? $schedRight : '—' }}</span>
        </div>
    @endforeach
</div>
@endif

{{-- Small preview image: couple photo if available, else initials. --}}
@php
    $inv = $invitation;
    $raw = $inv->data ?? [];
    $d = \App\Support\MarriageInvitationCard::mergeUserDataWithDemoDefaults($raw);
    $groom = trim((string) ($d['groom_name'] ?? ''));
    $bride = trim((string) ($d['bride_name'] ?? ''));
    $thumbUrl = null;
    if (!empty($raw['couple_image']) && is_string($raw['couple_image'])) {
        $p = ltrim(str_replace('\\', '/', $raw['couple_image']), '/');
        if (str_starts_with($p, 'storage/')) {
            $p = substr($p, strlen('storage/'));
        }
        if ($p && \Illuminate\Support\Facades\Storage::disk('public')->exists($p)) {
            $thumbUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($p);
        }
    }
    $initials = '';
    if ($groom !== '') {
        $initials .= strtoupper(mb_substr($groom, 0, 1));
    }
    if ($bride !== '') {
        $initials .= strtoupper(mb_substr($bride, 0, 1));
    }
    if ($initials === '') {
        $initials = '♥';
    }
    $size = $size ?? 'sm';
    $box = $size === 'lg'
        ? 'h-20 w-20 sm:h-24 sm:w-24 text-xl sm:text-2xl rounded-2xl'
        : 'h-12 w-12 text-xs rounded-xl';
@endphp
<div class="shrink-0 flex {{ $box }} overflow-hidden bg-gradient-to-br from-indigo-600 to-violet-700 shadow-sm ring-1 ring-white/10" role="img" aria-label="Invitation preview">
    @if($thumbUrl)
        <img src="{{ $thumbUrl }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async" width="96" height="96">
    @else
        <span class="m-auto font-bold leading-none text-white/95 tracking-tight select-none">{{ $initials }}</span>
    @endif
</div>

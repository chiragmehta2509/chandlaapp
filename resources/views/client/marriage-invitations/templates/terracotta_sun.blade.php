<!DOCTYPE html>
<html lang="en"@if(!empty($demoThumbIframe)) class="cb-demo-thumb-scope"@endif>
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding — {{ $d['groom_name'] ?? '' }} & {{ $d['bride_name'] ?? '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Nunito:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'Nunito', sans-serif; background: linear-gradient(160deg, #ffedd5 0%, #fed7aa 50%, #fdba74 100%); padding: 28px 16px; color: #7c2d12; }
        @if(!empty($pngExportScript)) body.png-terra { background: #ffedd5 !important; } @endif
        .capture-root { max-width: 515px; margin: 0 auto; background: #fffaf5; border-radius: 8px; border: 2px solid #ea580c; box-shadow: 0 20px 44px rgba(194,65,12,.15); padding: 38px 30px 34px; }
        .ts-arch { text-align: center; font-size: .65rem; letter-spacing: .28em; text-transform: uppercase; color: #c2410c; font-weight: 700; margin-bottom: 16px; }
        .ts-photo { max-width: 225px; margin: 0 auto 18px; border-radius: 8px; overflow: hidden; border: 3px solid #fb923c; box-shadow: 8px 8px 0 rgba(234,88,12,.2); }
        .ts-photo img { display: block; width: 100%; aspect-ratio: 3/4; object-fit: cover; }
        .ts-name { text-align: center; font-family: 'Libre Baskerville', Georgia, serif; font-size: clamp(1.55rem, 4vw, 1.95rem); font-weight: 700; margin: 0; color: #431407; line-height: 1.2; }
        .ts-amp { text-align: center; font-family: 'Libre Baskerville', serif; font-style: italic; color: #ea580c; font-size: 1.4rem; margin: 8px 0; }
        .ts-parents { text-align: center; font-size: .86rem; color: #9a3412; line-height: 1.5; margin: 14px 0; }
        .ts-box { margin-top: 14px; padding: 16px; background: #fff7ed; border-left: 4px solid #ea580c; border-radius: 0 12px 12px 0; }
        .ts-box strong { display: block; font-size: .58rem; letter-spacing: .2em; text-transform: uppercase; color: #c2410c; margin-bottom: 6px; }
        .ts-box span, .ts-box div { font-size: .9rem; color: #431407; }
        .ts-addr { white-space: pre-line; margin-top: 6px; line-height: 1.45; }
        .ts-sched { margin-top: 16px; padding: 18px; background: #7c2d12; color: #ffedd5; border-radius: 12px; }
        .ts-sched h3 { text-align: center; font-size: .58rem; letter-spacing: .2em; text-transform: uppercase; margin: 0 0 12px; color: #fdba74; }
        .ts-srow { display: flex; align-items: baseline; gap: 8px; margin: 10px 0; font-size: .84rem; }
        .ts-st { flex-shrink: 0; font-weight: 700; color: #fff; }
        .ts-dots { flex: 1; border-bottom: 1px dotted rgba(253,186,116,.45); min-width: 12px; margin: 0 2px 4px; height: 0; }
        .ts-sw { flex-shrink: 0; color: #fdba74; font-weight: 600; }
        .ts-foot { text-align: center; margin-top: 16px; font-size: .86rem; color: #9a3412; }
        .ts-brand { text-align: center; margin-top: 14px; font-size: .58rem; letter-spacing: .14em; color: #fdba74; text-transform: uppercase; }
    </style>
    @includeWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head')
</head>
<body @class([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'png-terra' => !empty($pngExportScript),
])>
@php extract(\App\Support\MarriageInvitationCard::viewData($d ?? [], $coupleImagePdfSrc ?? null, $coupleImageDataUri ?? null, !empty($pngExportScript))); @endphp
@if(!empty($demoThumbIframe))<div id="cb-demo-fit-root">@endif
<div class="capture-root">
    <p class="ts-arch">{{ $d['tagline'] ?? 'Together with family' }}</p>
    @if($coupleImageOk && $imgSrc)
    <div class="ts-photo">
        <img src="{{ $imgSrc }}" alt="" width="225" height="300" @if(!empty($pngExportScript)) loading="eager" decoding="sync" @else loading="lazy" @endif>
    </div>
    @endif
    <h1 class="ts-name">{{ $d['groom_name'] ?? '' }}</h1>
    <p class="ts-amp">&amp;</p>
    <h1 class="ts-name">{{ $d['bride_name'] ?? '' }}</h1>
    @if(!empty($d['parent_groom']) || !empty($d['parent_bride']))
    <div class="ts-parents">
        @if(!empty($d['parent_groom']))<div>{{ $d['parent_groom'] }}</div>@endif
        @if(!empty($d['parent_bride']))<div>{{ $d['parent_bride'] }}</div>@endif
    </div>
    @endif
    <div class="ts-box">
        <strong>When</strong>
        <span>{{ $dateLine ?: '—' }}@if(!empty($d['wedding_time'])) · {{ \App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null) }}@endif</span>
    </div>
    <div class="ts-box">
        <strong>Where</strong>
        <span>{{ $d['venue_name'] ?? '' }}</span>
        <div class="ts-addr">{{ $d['venue_address'] ?? '' }}</div>
    </div>
    @include('client.marriage-invitations.partials.schedule-block', ['d' => $d, 'wrapClass' => 'ts-sched', 'titleClass' => '', 'heading' => 'Schedule', 'rowClass' => 'ts-srow', 'titleCellClass' => 'ts-st', 'dotsClass' => 'ts-dots', 'whenClass' => 'ts-sw'])
    @if(!empty($d['rsvp_contact']))<p class="ts-foot">RSVP: {{ $d['rsvp_contact'] }}</p>@endif
    <p class="ts-brand">Chandla Book</p>
</div>
@if(!empty($demoThumbIframe))
</div>
@include('client.marriage-invitations.partials.demo-thumb-fit-script')
@endif
@include('client.marriage-invitations.partials.export-png-script')
</body>
</html>

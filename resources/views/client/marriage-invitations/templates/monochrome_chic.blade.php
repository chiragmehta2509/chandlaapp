<!DOCTYPE html>
<html lang="en"@if(!empty($demoThumbIframe)) class="cb-demo-thumb-scope"@endif>
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding — {{ $d['groom_name'] ?? '' }} & {{ $d['bride_name'] ?? '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600&family=Crimson+Pro:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'Crimson Pro', Georgia, serif; background: #fafafa; padding: 32px 16px; color: #171717; }
        @if(!empty($pngExportScript)) body.png-mono { background: #fafafa !important; } @endif
        .capture-root { max-width: 480px; margin: 0 auto; background: #fff; border: 1px solid #171717; padding: 0; }
        .mc-bar { height: 6px; background: #171717; }
        .mc-inner { padding: 40px 32px 36px; }
        .mc-label { font-family: 'Oswald', sans-serif; font-size: .58rem; letter-spacing: .35em; text-transform: uppercase; color: #525252; margin-bottom: 20px; text-align: center; font-weight: 500; }
        .mc-photo { max-width: 200px; margin: 0 auto 24px; border: 1px solid #171717; overflow: hidden; }
        .mc-photo img { display: block; width: 100%; aspect-ratio: 3/4; object-fit: cover; filter: grayscale(100%); }
        .mc-name { font-family: 'Oswald', sans-serif; text-align: center; font-size: clamp(1.85rem, 5vw, 2.35rem); font-weight: 500; letter-spacing: .04em; text-transform: uppercase; margin: 0; line-height: 1.1; }
        .mc-amp { text-align: center; font-family: 'Crimson Pro', serif; font-style: italic; font-size: 1.75rem; margin: 10px 0; color: #404040; }
        .mc-parents { text-align: center; font-size: .9rem; color: #404040; line-height: 1.55; margin: 20px 0; border-top: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5; padding: 16px 0; }
        .mc-row { margin-top: 20px; border: 1px solid #171717; }
        .mc-cell { background: #fff; padding: 18px 14px; text-align: center; border-bottom: 1px solid #171717; }
        .mc-cell:last-child { border-bottom: none; }
        .mc-cell h2 { font-family: 'Oswald', sans-serif; font-size: .55rem; letter-spacing: .25em; text-transform: uppercase; margin: 0 0 8px; color: #737373; font-weight: 500; }
        .mc-cell p { margin: 0; font-size: .88rem; line-height: 1.45; }
        .mc-sched { margin-top: 20px; padding: 20px; background: #171717; color: #fafafa; }
        .mc-sched h3 { font-family: 'Oswald', sans-serif; text-align: center; font-size: .55rem; letter-spacing: .28em; text-transform: uppercase; margin: 0 0 14px; color: #a3a3a3; }
        .mc-srow { display: flex; align-items: baseline; gap: 8px; margin: 12px 0; font-size: .84rem; border-bottom: 1px solid #404040; padding-bottom: 10px; }
        .mc-srow:last-child { border-bottom: none; }
        .mc-st { flex-shrink: 0; font-weight: 600; color: #fff; font-family: 'Oswald', sans-serif; font-size: .75rem; letter-spacing: .06em; text-transform: uppercase; }
        .mc-dots { display: none; }
        .mc-sw { flex-shrink: 0; color: #d4d4d4; margin-left: auto; }
        .mc-foot { text-align: center; margin-top: 20px; font-size: .82rem; color: #525252; }
        .mc-brand { text-align: center; margin-top: 16px; font-family: 'Oswald', sans-serif; font-size: .55rem; letter-spacing: .2em; color: #a3a3a3; text-transform: uppercase; }
    </style>
    @includeWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head')
</head>
<body @class([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'png-mono' => !empty($pngExportScript),
])>
@php extract(\App\Support\MarriageInvitationCard::viewData($d ?? [], $coupleImagePdfSrc ?? null, $coupleImageDataUri ?? null, !empty($pngExportScript))); @endphp
@if(!empty($demoThumbIframe))<div id="cb-demo-fit-root">@endif
<div class="capture-root">
    <div class="mc-bar"></div>
    <div class="mc-inner">
        <p class="mc-label">{{ $d['tagline'] ?? 'Wedding invitation' }}</p>
        @if($coupleImageOk && $imgSrc)
        <div class="mc-photo">
            <img src="{{ $imgSrc }}" alt="" width="200" height="267" @if(!empty($pngExportScript)) loading="eager" decoding="sync" @else loading="lazy" @endif>
        </div>
        @endif
        <h1 class="mc-name">{{ $d['groom_name'] ?? '' }}</h1>
        <p class="mc-amp">&amp;</p>
        <h1 class="mc-name">{{ $d['bride_name'] ?? '' }}</h1>
        @if(!empty($d['parent_groom']) || !empty($d['parent_bride']))
        <div class="mc-parents">
            @if(!empty($d['parent_groom']))<div>{{ $d['parent_groom'] }}</div>@endif
            @if(!empty($d['parent_bride']))<div>{{ $d['parent_bride'] }}</div>@endif
        </div>
        @endif
        <div class="mc-row">
            <div class="mc-cell">
                <h2>Date</h2>
                <p>{{ $dateLine ?: '—' }}@if(!empty($d['wedding_time']))<br>{{ \App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null) }}@endif</p>
            </div>
            <div class="mc-cell">
                <h2>Venue</h2>
                <p><strong>{{ $d['venue_name'] ?? '' }}</strong></p>
                <p style="white-space:pre-line;margin-top:6px">{{ $d['venue_address'] ?? '' }}</p>
            </div>
        </div>
        @include('client.marriage-invitations.partials.schedule-block', ['d' => $d, 'wrapClass' => 'mc-sched', 'titleClass' => '', 'heading' => 'Schedule', 'rowClass' => 'mc-srow', 'titleCellClass' => 'mc-st', 'dotsClass' => 'mc-dots', 'whenClass' => 'mc-sw'])
        @if(!empty($d['rsvp_contact']))<p class="mc-foot">RSVP — {{ $d['rsvp_contact'] }}</p>@endif
        <p class="mc-brand">Chandla Book</p>
    </div>
</div>
@if(!empty($demoThumbIframe))
</div>
@include('client.marriage-invitations.partials.demo-thumb-fit-script')
@endif
@include('client.marriage-invitations.partials.export-png-script')
</body>
</html>

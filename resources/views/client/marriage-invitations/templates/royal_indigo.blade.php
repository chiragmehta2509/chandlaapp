<!DOCTYPE html>
<html lang="en"@if(!empty($demoThumbIframe)) class="cb-demo-thumb-scope"@endif>
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding — {{ $d['groom_name'] ?? '' }} & {{ $d['bride_name'] ?? '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'Source Sans 3', system-ui, sans-serif; background: linear-gradient(145deg, #1e1b4b 0%, #312e81 45%, #0f172a 100%); padding: 28px 16px 40px; color: #1e1b4b; }
        @if(!empty($pngExportScript)) body.png-royal { background: #1e1b4b !important; } @endif
        .capture-root { max-width: 520px; margin: 0 auto; background: linear-gradient(180deg, #fefce8 0%, #fff 55%, #faf5ff 100%); border-radius: 10px; border: 3px solid #ca8a04; box-shadow: 0 24px 48px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.85); padding: 38px 30px 34px; position: relative; }
        .capture-root::before { content: ''; position: absolute; inset: 11px; border: 1px solid rgba(202,138,4,.4); pointer-events: none; border-radius: 6px; }
        .rh-sub { text-align: center; font-size: .92rem; color: #64748b; font-style: italic; margin: 0 0 18px; }
        .rh-photo { max-width: 220px; margin: 0 auto 18px; border-radius: 18px; overflow: hidden; border: 3px solid #e9d5ff; box-shadow: 0 14px 32px rgba(30,27,75,.12); }
        .rh-photo img { display: block; width: 100%; aspect-ratio: 3/4; object-fit: cover; }
        .rh-title { text-align: center; font-family: 'Playfair Display', serif; font-size: clamp(1.75rem, 5vw, 2.25rem); font-weight: 600; margin: 0; color: #1e1b4b; line-height: 1.15; }
        .rh-amp { text-align: center; font-family: 'Playfair Display', serif; font-style: italic; color: #a16207; font-size: 1.45rem; margin: 6px 0; }
        .rh-parents { text-align: center; font-size: .88rem; color: #475569; line-height: 1.55; margin: 14px 0 18px; }
        .rh-box { background: rgba(30,27,75,.05); border-radius: 14px; padding: 16px 18px; text-align: center; margin-top: 10px; border: 1px solid rgba(30,27,75,.08); }
        .rh-box strong { display: block; font-size: .6rem; letter-spacing: .22em; text-transform: uppercase; color: #a16207; margin-bottom: 8px; font-weight: 700; }
        .rh-sched { margin-top: 16px; padding: 18px; background: linear-gradient(165deg, #1e1b4b 0%, #312e81 100%); color: #fef9c3; border-radius: 14px; border: 1px solid rgba(250,204,21,.25); }
        .rh-sched h3 { text-align: center; font-size: .62rem; letter-spacing: .22em; text-transform: uppercase; margin: 0 0 12px; color: #fde047; font-weight: 700; }
        .rh-srow { display: flex; align-items: baseline; gap: 8px; margin: 10px 0; font-size: .86rem; }
        .rh-st { flex-shrink: 0; font-weight: 600; color: #fff; }
        .rh-dots { flex: 1; border-bottom: 1px dotted rgba(253,224,71,.45); min-width: 12px; margin: 0 2px 4px; height: 0; }
        .rh-sw { flex-shrink: 0; color: #fde047; font-weight: 500; }
        .rh-foot { text-align: center; margin-top: 16px; font-size: .86rem; color: #64748b; }
        .rh-brand { text-align: center; margin-top: 14px; font-size: .62rem; letter-spacing: .14em; color: #94a3b8; text-transform: uppercase; }
    </style>
    @includeWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head')
</head>
<body @class([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'png-royal' => !empty($pngExportScript),
])>
@php extract(\App\Support\MarriageInvitationCard::viewData($d ?? [], $coupleImagePdfSrc ?? null, $coupleImageDataUri ?? null, !empty($pngExportScript))); @endphp
@if(!empty($demoThumbIframe))<div id="cb-demo-fit-root">@endif
<div class="capture-root">
    <p class="rh-sub">{{ $d['tagline'] ?? 'Together with our families' }}</p>
    @if($coupleImageOk && $imgSrc)
    <div class="rh-photo">
        <img src="{{ $imgSrc }}" alt="" width="220" height="294" @if(!empty($pngExportScript)) loading="eager" decoding="sync" @else loading="lazy" @endif>
    </div>
    @endif
    <h1 class="rh-title">{{ $d['groom_name'] ?? '' }}</h1>
    <p class="rh-amp">&amp;</p>
    <h1 class="rh-title">{{ $d['bride_name'] ?? '' }}</h1>
    @if(!empty($d['parent_groom']) || !empty($d['parent_bride']))
    <div class="rh-parents">
        @if(!empty($d['parent_groom']))<div>{{ $d['parent_groom'] }}</div>@endif
        @if(!empty($d['parent_bride']))<div>{{ $d['parent_bride'] }}</div>@endif
    </div>
    @endif
    <div class="rh-box">
        <strong>When</strong>
        <span>{{ $dateLine ?: '—' }}@if(!empty($d['wedding_time'])) · {{ \App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null) }}@endif</span>
    </div>
    <div class="rh-box">
        <strong>Where</strong>
        <span style="font-family:'Playfair Display',serif;font-size:1.05rem;display:block">{{ $d['venue_name'] ?? '' }}</span>
        <div style="white-space:pre-line;margin-top:8px;font-size:.88rem;color:#475569">{{ $d['venue_address'] ?? '' }}</div>
    </div>
    @include('client.marriage-invitations.partials.schedule-block', ['d' => $d, 'wrapClass' => 'rh-sched', 'titleClass' => '', 'heading' => 'Schedule of events', 'rowClass' => 'rh-srow', 'titleCellClass' => 'rh-st', 'dotsClass' => 'rh-dots', 'whenClass' => 'rh-sw'])
    @if(!empty($d['rsvp_contact']))<p class="rh-foot">RSVP / Contact: {{ $d['rsvp_contact'] }}</p>@endif
    <p class="rh-brand">Chandla Book</p>
</div>
@if(!empty($demoThumbIframe))
</div>
@include('client.marriage-invitations.partials.demo-thumb-fit-script')
@endif
@include('client.marriage-invitations.partials.export-png-script')
</body>
</html>

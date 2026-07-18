<!DOCTYPE html>
<html lang="en"@if(!empty($demoThumbIframe)) class="cb-demo-thumb-scope"@endif>
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding — {{ $d['groom_name'] ?? '' }} & {{ $d['bride_name'] ?? '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'Montserrat', sans-serif; background: radial-gradient(ellipse at top, #27272a 0%, #09090b 55%); padding: 32px 16px; color: #fafafa; }
        @if(!empty($pngExportScript)) body.png-mid { background: #18181b !important; } @endif
        .capture-root { max-width: 510px; margin: 0 auto; background: linear-gradient(180deg, #18181b 0%, #0c0c0e 100%); border-radius: 4px; border: 1px solid rgba(212,175,55,.45); box-shadow: 0 0 0 1px rgba(255,255,255,.06), 0 32px 64px rgba(0,0,0,.6); padding: 44px 32px 40px; }
        .mg-line { width: 48px; height: 2px; background: linear-gradient(90deg, transparent, #d4af37, transparent); margin: 0 auto 20px; }
        .mg-sub { text-align: center; font-size: .72rem; letter-spacing: .25em; text-transform: uppercase; color: #a1a1aa; margin-bottom: 24px; font-weight: 500; }
        .mg-photo { max-width: 200px; margin: 0 auto 22px; border-radius: 4px; overflow: hidden; border: 2px solid #d4af37; box-shadow: 0 16px 40px rgba(0,0,0,.5); }
        .mg-photo img { display: block; width: 100%; aspect-ratio: 3/4; object-fit: cover; }
        .mg-name { text-align: center; font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 6vw, 2.65rem); font-weight: 400; margin: 0; background: linear-gradient(135deg, #fef3c7, #d4af37, #fde68a); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .mg-amp { text-align: center; font-family: 'Cormorant Garamond', serif; font-style: italic; color: #a8a29e; font-size: 1.6rem; margin: 8px 0; }
        .mg-parents { text-align: center; font-size: .82rem; color: #a3a3a3; line-height: 1.55; margin: 18px 0; }
        .mg-detail { text-align: center; margin-top: 18px; padding: 16px; border-top: 1px solid rgba(212,175,55,.25); border-bottom: 1px solid rgba(212,175,55,.25); }
        .mg-detail strong { display: block; font-size: .58rem; letter-spacing: .25em; color: #d4af37; margin-bottom: 8px; }
        .mg-detail span { font-size: .95rem; color: #e5e5e5; }
        .mg-addr { white-space: pre-line; font-size: .86rem; color: #a3a3a3; margin-top: 8px; line-height: 1.45; }
        .mg-sched { margin-top: 20px; padding: 20px; background: rgba(212,175,55,.08); border: 1px solid rgba(212,175,55,.2); border-radius: 4px; }
        .mg-sched h3 { text-align: center; font-size: .58rem; letter-spacing: .22em; text-transform: uppercase; margin: 0 0 14px; color: #d4af37; }
        .mg-srow { display: flex; align-items: baseline; gap: 8px; margin: 12px 0; font-size: .84rem; }
        .mg-st { flex-shrink: 0; color: #fafafa; font-weight: 600; }
        .mg-dots { flex: 1; border-bottom: 1px dotted rgba(212,175,55,.35); min-width: 12px; margin: 0 2px 4px; height: 0; }
        .mg-sw { flex-shrink: 0; color: #fcd34d; }
        .mg-foot { text-align: center; margin-top: 20px; font-size: .82rem; color: #737373; }
        .mg-brand { text-align: center; margin-top: 16px; font-size: .58rem; letter-spacing: .2em; color: #525252; text-transform: uppercase; }
    </style>
    @includeWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head')
</head>
<body @class([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'png-mid' => !empty($pngExportScript),
])>
@php extract(\App\Support\MarriageInvitationCard::viewData($d ?? [], $coupleImagePdfSrc ?? null, $coupleImageDataUri ?? null, !empty($pngExportScript))); @endphp
@if(!empty($demoThumbIframe))<div id="cb-demo-fit-root">@endif
<div class="capture-root">
    <div class="mg-line"></div>
    <p class="mg-sub">{{ $d['tagline'] ?? 'Evening celebration' }}</p>
    @if($coupleImageOk && $imgSrc)
    <div class="mg-photo">
        <img src="{{ $imgSrc }}" alt="" width="200" height="267" @if(!empty($pngExportScript)) loading="eager" decoding="sync" @else loading="lazy" @endif>
    </div>
    @endif
    <h1 class="mg-name">{{ $d['groom_name'] ?? '' }}</h1>
    <p class="mg-amp">&amp;</p>
    <h1 class="mg-name">{{ $d['bride_name'] ?? '' }}</h1>
    @if(!empty($d['parent_groom']) || !empty($d['parent_bride']))
    <div class="mg-parents">
        @if(!empty($d['parent_groom']))<div>{{ $d['parent_groom'] }}</div>@endif
        @if(!empty($d['parent_bride']))<div>{{ $d['parent_bride'] }}</div>@endif
    </div>
    @endif
    <div class="mg-detail">
        <strong>Date</strong>
        <span>{{ $dateLine ?: '—' }}@if(!empty($d['wedding_time'])) · {{ \App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null) }}@endif</span>
    </div>
    <div class="mg-detail">
        <strong>Venue</strong>
        <span>{{ $d['venue_name'] ?? '' }}</span>
        <div class="mg-addr">{{ $d['venue_address'] ?? '' }}</div>
    </div>
    @include('client.marriage-invitations.partials.schedule-block', ['d' => $d, 'wrapClass' => 'mg-sched', 'titleClass' => '', 'heading' => 'Schedule', 'rowClass' => 'mg-srow', 'titleCellClass' => 'mg-st', 'dotsClass' => 'mg-dots', 'whenClass' => 'mg-sw'])
    @if(!empty($d['rsvp_contact']))<p class="mg-foot">RSVP: {{ $d['rsvp_contact'] }}</p>@endif
    <p class="mg-brand">Chandla Book</p>
</div>
@if(!empty($demoThumbIframe))
</div>
@include('client.marriage-invitations.partials.demo-thumb-fit-script')
@endif
@include('client.marriage-invitations.partials.export-png-script')
</body>
</html>

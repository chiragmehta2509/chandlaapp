<!DOCTYPE html>
<html lang="en"@if(!empty($demoThumbIframe)) class="cb-demo-thumb-scope"@endif>
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding — {{ $d['groom_name'] ?? '' }} & {{ $d['bride_name'] ?? '' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Pinyon+Script&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        :root {
            --emerald: #02231c;
            --emerald-light: #064436;
            --gold: #d4af37;
            --gold-dark: #aa820a;
            --gold-light: #f3e5ab;
            --cream: #fbf9f3;
        }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Cormorant Garamond', Georgia, serif;
            color: var(--cream);
            background-color: #011410;
            background-image:
                linear-gradient(165deg, rgba(2, 35, 28, 0.9) 0%, rgba(6, 68, 54, 0.8) 50%, rgba(1, 20, 16, 0.95) 100%),
                url('https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=1920&q=75');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 32px 16px 48px;
        }
        .page-wrap {
            max-width: 560px;
            margin: 0 auto;
            position: relative;
        }
        .sheet {
            position: relative;
            padding: 56px 36px 48px;
            background: linear-gradient(135deg, #022820 0%, #031f18 100%);
            border: 3px double var(--gold);
            border-radius: 4px;
            box-shadow: 
                0 0 0 4px #022820,
                0 0 0 6px var(--gold-dark),
                0 24px 50px rgba(0,0,0,0.6);
            text-align: center;
        }
        .mughal-arch {
            position: absolute;
            inset: 12px;
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 2px;
            pointer-events: none;
        }
        .mughal-arch::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 140px;
            height: 40px;
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-bottom: none;
            border-radius: 120px 120px 0 0;
        }
        .tagline {
            font-size: 0.85rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--gold-light);
            margin: 0 0 15px;
            font-weight: 500;
        }
        .mandala {
            width: 50px;
            height: 50px;
            margin: 0 auto 16px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23d4af37"><path d="M12 2a1 1 0 0 1 1 1v1.077A8.002 8.002 0 0 1 19.923 11H21a1 1 0 0 1 0 2h-1.077A8.002 8.002 0 0 1 13 19.923V21a1 1 0 0 1-2 0v-1.077A8.002 8.002 0 0 1 4.077 13H3a1 1 0 0 1 0-2h1.077A8.002 8.002 0 0 1 11 4.077V3a1 1 0 0 1 1-1zm0 4a6 6 0 1 0 0 12 6 6 0 0 0 0-12z"/></svg>');
            background-size: contain;
            background-repeat: no-repeat;
            opacity: 0.85;
        }
        h1 {
            font-size: clamp(1.4rem, 4vw, 1.8rem);
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--gold);
            margin: 0 0 24px;
        }
        .couple-photo-wrap {
            max-width: 240px;
            margin: 0 auto 24px;
            padding: 6px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            border-radius: 120px 120px 10px 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }
        .couple-photo-wrap img {
            display: block;
            width: 100%;
            aspect-ratio: 3/4;
            object-fit: cover;
            border-radius: 114px 114px 6px 6px;
        }
        .couple-photo-missing {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 200px;
            padding: 1.25rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 114px 114px 6px 6px;
            color: var(--gold-light);
            font-size: 0.85rem;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }
        .names {
            margin: 20px 0;
        }
        .names .script {
            font-family: 'Pinyon Script', cursive;
            font-size: clamp(3rem, 10vw, 4rem);
            font-weight: 400;
            color: var(--gold-light);
            display: block;
            line-height: 1;
        }
        .names .amp {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            color: var(--gold);
            display: block;
            margin: 10px 0;
            font-style: italic;
        }
        .parents {
            font-size: 1rem;
            color: #d1e2dd;
            max-width: 90%;
            margin: 0 auto 28px;
            line-height: 1.6;
        }
        .parents div {
            border-top: 1px solid rgba(212, 175, 55, 0.15);
            padding-top: 8px;
            margin-top: 8px;
        }
        .parents div:first-child {
            border: none;
            padding: 0;
            margin: 0;
        }
        .detail {
            margin: 18px 0;
            padding: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px dashed rgba(212, 175, 55, 0.3);
            border-radius: 8px;
        }
        .detail strong {
            display: block;
            font-size: 0.72rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 6px;
            font-weight: 700;
        }
        .detail span {
            font-size: 1.15rem;
            font-weight: 500;
            color: var(--cream);
        }
        .address {
            white-space: pre-line;
            font-size: 0.95rem;
            margin-top: 6px;
            color: #d1e2dd;
            line-height: 1.45;
        }
        .schedule-card {
            margin-top: 24px;
            padding: 20px;
            background: rgba(0,0,0,0.25);
            border-radius: 10px;
            border: 1px solid rgba(212, 175, 55, 0.25);
        }
        .schedule-card h3 {
            font-size: 0.75rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            margin: 0 0 16px;
            color: var(--gold);
            font-weight: 600;
        }
        .schedule-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
            margin: 12px 0;
            font-size: 0.95rem;
        }
        .sched-title {
            font-weight: 600;
            color: var(--gold-light);
        }
        .sched-dots {
            flex: 1;
            border-bottom: 1px dotted rgba(212, 175, 55, 0.3);
            height: 0;
        }
        .sched-when {
            color: var(--cream);
        }
        .rsvp {
            margin-top: 28px;
            font-size: 0.95rem;
            color: var(--gold-light);
            font-style: italic;
        }
        .brand {
            margin-top: 24px;
            font-size: 0.7rem;
            letter-spacing: 0.1em;
            color: rgba(212, 175, 55, 0.4);
            text-transform: uppercase;
        }
        @if(!empty($pngExportScript))
        body.png-export-mode {
            background: #02231c !important;
            background-image: none !important;
        }
        @endif
    </style>
    @includeWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head')
</head>
<body @class([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'png-export-mode' => !empty($pngExportScript),
])>
@php
    extract(\App\Support\MarriageInvitationCard::viewData(
        $d ?? [],
        $coupleImagePdfSrc ?? null,
        $coupleImageDataUri ?? null,
        !empty($pngExportScript)
    ));
@endphp
@if(!empty($demoThumbIframe))<div id="cb-demo-fit-root">@endif
<div class="page-wrap capture-root">
    <div class="sheet">
        <div class="mughal-arch"></div>
        <div class="mandala"></div>
        <p class="tagline">{{ $d['tagline'] ?? 'Together with our families' }}</p>
        <h1>Wedding Invitation</h1>

        @if($coupleImageOk && $imgSrc)
        <div class="couple-photo-wrap">
            <img src="{{ $imgSrc }}" alt="Couple photo" width="240" height="320"@if(!empty($pngExportScript)) loading="eager" decoding="sync"@else loading="lazy" decoding="async"@endif>
        </div>
        @elseif($couplePath)
        <div class="couple-photo-wrap">
            <div class="couple-photo-missing" role="img" aria-label="Photo loading failed">
                <span>We couldn’t load this image. Re-upload the photo on Chandla Book.</span>
            </div>
        </div>
        @endif

        <div class="names">
            <span class="script">{{ $d['groom_name'] ?? '' }}</span>
            <span class="amp">and</span>
            <span class="script">{{ $d['bride_name'] ?? '' }}</span>
        </div>

        @if(!empty($d['parent_groom']) || !empty($d['parent_bride']))
            <div class="parents">
                @if(!empty($d['parent_groom']))<div>{{ $d['parent_groom'] }}</div>@endif
                @if(!empty($d['parent_bride']))<div>{{ $d['parent_bride'] }}</div>@endif
            </div>
        @endif

        <div class="detail">
            <strong>Date & Time</strong>
            <span>{{ $dateLine ?: '—' }}@if(!empty($d['wedding_time'])) · {{ \App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null) }}@endif</span>
        </div>

        <div class="detail">
            <strong>Venue</strong>
            <span>{{ $d['venue_name'] ?? '' }}</span>
            <div class="address">{{ $d['venue_address'] ?? '' }}</div>
        </div>

        @if(!empty($d['schedule_events']) && is_array($d['schedule_events']))
        <div class="schedule-card">
            <h3>Celebration Schedule</h3>
            @foreach($d['schedule_events'] as $ev)
                @if(empty($ev['title'])) @continue @endif
                @php
                    $schedDate = '';
                    if (!empty($ev['date'])) {
                        try { $schedDate = \Carbon\Carbon::parse($ev['date'])->format('d/m/Y'); } catch (\Throwable $e) { $schedDate = (string) $ev['date']; }
                    }
                    $schedTime = trim((string) ($ev['time'] ?? ''));
                    $schedRight = $schedDate;
                    if ($schedTime !== '') {
                        $schedRight .= ($schedRight !== '' ? ' · ' : '') . $schedTime;
                    }
                @endphp
                <div class="schedule-row">
                    <span class="sched-title">{{ $ev['title'] }}</span>
                    <span class="sched-dots" aria-hidden="true"></span>
                    <span class="sched-when">{{ $schedRight !== '' ? $schedRight : '—' }}</span>
                </div>
            @endforeach
        </div>
        @endif

        @if(!empty($d['rsvp_contact']))
            <div class="rsvp">RSVP / Contact: {{ $d['rsvp_contact'] }}</div>
        @endif
        
        <div class="brand">Made with Chandla Book</div>
    </div>
</div>
@if(!empty($demoThumbIframe))
</div>
@include('client.marriage-invitations.partials.demo-thumb-fit-script')
@endif
@include('client.marriage-invitations.partials.export-png-script')
</body>
</html>

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
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Great+Vibes&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        :root {
            --space-bg: #070919;
            --space-purple: #131433;
            --gold: #e9c46a;
            --gold-dark: #b89025;
            --cream: #ffffff;
            --muted: #a6adcc;
        }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Montserrat', system-ui, sans-serif;
            color: var(--cream);
            background-color: var(--space-bg);
            background-image:
                linear-gradient(165deg, rgba(7, 9, 25, 0.95) 0%, rgba(19, 20, 51, 0.9) 55%, rgba(3, 4, 12, 0.98) 100%),
                url('https://images.unsplash.com/photo-1506318137071-a8e063b4bec0?auto=format&fit=crop&w=1920&q=75');
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
            padding: 56px 36px 44px;
            background: rgba(7, 9, 25, 0.75);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(233, 196, 106, 0.3);
            border-radius: 12px;
            box-shadow: 
                0 25px 50px rgba(0,0,0,0.5),
                inset 0 0 0 1px rgba(255,255,255,0.05);
            text-align: center;
            overflow: hidden;
        }
        .stars-accent {
            position: absolute;
            inset: 8px;
            border: 1px solid rgba(233, 196, 106, 0.15);
            border-radius: 8px;
            pointer-events: none;
        }
        .stars-accent::before {
            content: '★';
            position: absolute;
            top: 10px;
            left: 10px;
            color: var(--gold);
            font-size: 0.7rem;
            opacity: 0.6;
        }
        .stars-accent::after {
            content: '★';
            position: absolute;
            bottom: 10px;
            right: 10px;
            color: var(--gold);
            font-size: 0.7rem;
            opacity: 0.6;
        }
        .tagline {
            font-size: 0.75rem;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--gold);
            margin: 0 0 16px;
            font-weight: 500;
        }
        h1 {
            font-family: 'Cinzel', serif;
            font-size: clamp(1.3rem, 4vw, 1.6rem);
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--cream);
            margin: 0 0 24px;
        }
        .couple-photo-wrap {
            max-width: 220px;
            margin: 0 auto 24px;
            padding: 5px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            border-radius: 50%;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
        }
        .couple-photo-wrap img {
            display: block;
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            border-radius: 50%;
        }
        .couple-photo-missing {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            aspect-ratio: 1/1;
            padding: 1.25rem;
            text-align: center;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
            color: var(--gold);
            font-size: 0.8rem;
            border: 1px solid rgba(233, 196, 106, 0.2);
        }
        .names {
            margin: 22px 0;
        }
        .names .script {
            font-family: 'Great Vibes', cursive;
            font-size: clamp(3.2rem, 10vw, 4.2rem);
            font-weight: 400;
            color: var(--gold);
            display: block;
            line-height: 0.95;
        }
        .names .amp {
            font-family: 'Cinzel', serif;
            font-size: 1.2rem;
            color: var(--cream);
            display: block;
            margin: 8px 0;
            opacity: 0.8;
        }
        .parents {
            font-size: 0.92rem;
            color: var(--muted);
            max-width: 90%;
            margin: 0 auto 26px;
            line-height: 1.55;
        }
        .parents div { margin-top: 4px; }
        .detail {
            margin: 18px 0;
            padding: 16px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
            border: 1px solid rgba(233, 196, 106, 0.15);
        }
        .detail strong {
            display: block;
            font-family: 'Cinzel', serif;
            font-size: 0.65rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 6px;
            font-weight: 600;
        }
        .detail span {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--cream);
        }
        .address {
            white-space: pre-line;
            font-size: 0.9rem;
            margin-top: 6px;
            color: var(--muted);
            line-height: 1.45;
        }
        .schedule-card {
            margin-top: 24px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.01);
            border: 1px solid rgba(233, 196, 106, 0.15);
            border-radius: 8px;
        }
        .schedule-card h3 {
            font-family: 'Cinzel', serif;
            font-size: 0.72rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin: 0 0 12px;
            color: var(--gold);
            font-weight: 600;
        }
        .schedule-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
            margin: 8px 0;
            font-size: 0.9rem;
        }
        .sched-title {
            font-weight: 500;
            color: var(--cream);
        }
        .sched-dots {
            flex: 1;
            border-bottom: 1px dotted rgba(233, 196, 106, 0.2);
            height: 0;
        }
        .sched-when {
            color: var(--gold);
        }
        .rsvp {
            margin-top: 28px;
            font-size: 0.92rem;
            color: var(--gold);
            font-style: italic;
        }
        .brand {
            margin-top: 24px;
            font-size: 0.65rem;
            letter-spacing: 0.08em;
            color: rgba(233, 196, 106, 0.35);
            text-transform: uppercase;
        }
        @if(!empty($pngExportScript))
        body.png-export-mode {
            background: var(--space-bg) !important;
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
        <div class="stars-accent"></div>
        <p class="tagline">{{ $d['tagline'] ?? 'Together with our families' }}</p>
        <h1>Written in the Stars</h1>

        @if($coupleImageOk && $imgSrc)
        <div class="couple-photo-wrap">
            <img src="{{ $imgSrc }}" alt="Couple photo" width="220" height="220"@if(!empty($pngExportScript)) loading="eager" decoding="sync"@else loading="lazy" decoding="async"@endif>
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
            <span class="amp">&amp;</span>
            <span class="script">{{ $d['bride_name'] ?? '' }}</span>
        </div>

        @if(!empty($d['parent_groom']) || !empty($d['parent_bride']))
            <div class="parents">
                @if(!empty($d['parent_groom']))<div>{{ $d['parent_groom'] }}</div>@endif
                @if(!empty($d['parent_bride']))<div>{{ $d['parent_bride'] }}</div>@endif
            </div>
        @endif

        <div class="detail">
            <strong>The Celebration</strong>
            <span>{{ $dateLine ?: '—' }}@if(!empty($d['wedding_time'])) · {{ \App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null) }}@endif</span>
        </div>

        <div class="detail">
            <strong>The Venue</strong>
            <span>{{ $d['venue_name'] ?? '' }}</span>
            <div class="address">{{ $d['venue_address'] ?? '' }}</div>
        </div>

        @if(!empty($d['schedule_events']) && is_array($d['schedule_events']))
        <div class="schedule-card">
            <h3>Celebration Events</h3>
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
            <div class="rsvp">RSVP: {{ $d['rsvp_contact'] }}</div>
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

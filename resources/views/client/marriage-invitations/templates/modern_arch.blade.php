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
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400;0,600;0,700;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        :root {
            --terracotta: #c96f53;
            --clay: #e6a18a;
            --cream: #faf6f0;
            --cream-light: #fffcf7;
            --ink: #2c211e;
            --muted: #7d6e6a;
        }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Montserrat', system-ui, sans-serif;
            color: var(--ink);
            background-color: #f1e9e3;
            background-image:
                linear-gradient(165deg, rgba(250, 246, 240, 0.96) 0%, rgba(241, 233, 227, 0.9) 50%, rgba(235, 221, 212, 0.95) 100%),
                url('https://images.unsplash.com/photo-1607190074257-dd4b7af0309f?auto=format&fit=crop&w=1920&q=75');
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
            padding: 54px 32px 44px;
            background: var(--cream-light);
            border-radius: 40px 40px 8px 8px;
            border: 1px solid rgba(201, 111, 83, 0.2);
            box-shadow: 
                0 20px 40px rgba(44, 33, 30, 0.08),
                0 1px 3px rgba(0,0,0,0.02);
            text-align: center;
            overflow: hidden;
        }
        .arch-accent {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 180px;
            height: 16px;
            background: var(--terracotta);
            border-radius: 0 0 100px 100px;
            opacity: 0.85;
        }
        .tagline {
            font-size: 0.75rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--muted);
            margin: 0 0 16px;
            font-weight: 500;
        }
        h1 {
            font-family: 'Bodoni Moda', serif;
            font-size: clamp(1.4rem, 5vw, 1.95rem);
            font-weight: 600;
            letter-spacing: 0.05em;
            color: var(--terracotta);
            margin: 0 0 24px;
        }
        .couple-photo-wrap {
            max-width: 220px;
            margin: 0 auto 24px;
            padding: 6px;
            background: var(--cream);
            border: 1px solid rgba(201, 111, 83, 0.15);
            border-radius: 110px 110px 0 0;
        }
        .couple-photo-wrap img {
            display: block;
            width: 100%;
            aspect-ratio: 3/4;
            object-fit: cover;
            border-radius: 104px 104px 0 0;
        }
        .couple-photo-missing {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 180px;
            padding: 1rem;
            text-align: center;
            background: #faf6f0;
            color: var(--terracotta);
            border-radius: 104px 104px 0 0;
            font-size: 0.82rem;
        }
        .names {
            margin: 20px 0;
        }
        .names .script {
            font-family: 'Bodoni Moda', serif;
            font-size: clamp(2rem, 8vw, 2.75rem);
            font-weight: 600;
            color: var(--ink);
            display: block;
            line-height: 1.1;
        }
        .names .amp {
            font-family: 'Bodoni Moda', serif;
            font-size: 1.5rem;
            color: var(--terracotta);
            display: block;
            margin: 8px 0;
            font-style: italic;
        }
        .parents {
            font-size: 0.9rem;
            color: var(--muted);
            max-width: 85%;
            margin: 0 auto 28px;
            line-height: 1.5;
            font-weight: 400;
        }
        .parents div { margin-top: 4px; }
        .detail {
            margin: 16px 0;
            padding: 14px;
            background: rgba(201, 111, 83, 0.03);
            border-radius: 12px;
            border: 1px solid rgba(201, 111, 83, 0.08);
        }
        .detail strong {
            display: block;
            font-size: 0.68rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--terracotta);
            margin-bottom: 6px;
            font-weight: 600;
        }
        .detail span {
            font-size: 1.05rem;
            font-weight: 500;
            color: var(--ink);
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
            background: rgba(201, 111, 83, 0.02);
            border-radius: 16px;
            border: 1px solid rgba(201, 111, 83, 0.1);
        }
        .schedule-card h3 {
            font-size: 0.72rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin: 0 0 12px;
            color: var(--terracotta);
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
            color: var(--ink);
        }
        .sched-dots {
            flex: 1;
            border-bottom: 1px solid rgba(201, 111, 83, 0.15);
            height: 0;
        }
        .sched-when {
            color: var(--muted);
        }
        .rsvp {
            margin-top: 24px;
            font-size: 0.9rem;
            color: var(--terracotta);
            font-weight: 500;
        }
        .brand {
            margin-top: 24px;
            font-size: 0.65rem;
            letter-spacing: 0.1em;
            color: rgba(201, 111, 83, 0.4);
            text-transform: uppercase;
        }
        @if(!empty($pngExportScript))
        body.png-export-mode {
            background: var(--cream) !important;
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
        <div class="arch-accent"></div>
        <p class="tagline">{{ $d['tagline'] ?? 'Together with our families' }}</p>
        <h1>The Wedding</h1>

        @if($coupleImageOk && $imgSrc)
        <div class="couple-photo-wrap">
            <img src="{{ $imgSrc }}" alt="Couple photo" width="208" height="277"@if(!empty($pngExportScript)) loading="eager" decoding="sync"@else loading="lazy" decoding="async"@endif>
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
            <strong>When</strong>
            <span>{{ $dateLine ?: '—' }}@if(!empty($d['wedding_time'])) · {{ \App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null) }}@endif</span>
        </div>

        <div class="detail">
            <strong>Where</strong>
            <span>{{ $d['venue_name'] ?? '' }}</span>
            <div class="address">{{ $d['venue_address'] ?? '' }}</div>
        </div>

        @if(!empty($d['schedule_events']) && is_array($d['schedule_events']))
        <div class="schedule-card">
            <h3>Schedule</h3>
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

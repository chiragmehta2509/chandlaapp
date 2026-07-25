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
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        :root {
            --champagne: #f4ebd9;
            --champagne-light: #faf7f0;
            --gold: #bca374;
            --gold-dark: #8c734b;
            --ink: #1a1a1a;
            --muted: #5e5a52;
        }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--ink);
            background-color: #ebdcb9;
            background-image:
                linear-gradient(165deg, rgba(250, 247, 240, 0.95) 0%, rgba(235, 220, 185, 0.85) 60%, rgba(224, 204, 160, 0.95) 100%),
                url('https://images.unsplash.com/photo-1469371670807-013ccf25f16a?auto=format&fit=crop&w=1920&q=75');
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
            background: var(--champagne-light);
            border: 2px solid var(--gold);
            border-radius: 2px;
            box-shadow: 
                0 25px 45px rgba(26, 26, 26, 0.12),
                inset 0 0 0 4px var(--champagne-light),
                inset 0 0 0 5px var(--gold);
            text-align: center;
        }
        .tagline {
            font-family: 'Cinzel', serif;
            font-size: 0.72rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--gold-dark);
            margin: 0 0 20px;
            font-weight: 500;
        }
        .crest {
            font-family: 'Cinzel', serif;
            font-size: 1.15rem;
            color: var(--gold-dark);
            border: 1px solid var(--gold);
            width: 44px;
            height: 44px;
            line-height: 42px;
            border-radius: 50%;
            margin: 0 auto 20px;
            font-weight: 600;
        }
        h1 {
            font-family: 'Cinzel', serif;
            font-size: clamp(1.25rem, 4vw, 1.55rem);
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--ink);
            margin: 0 0 24px;
        }
        .couple-photo-wrap {
            max-width: 220px;
            margin: 0 auto 24px;
            padding: 5px;
            background: var(--champagne-light);
            border: 1px solid var(--gold);
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
        }
        .couple-photo-wrap img {
            display: block;
            width: 100%;
            aspect-ratio: 3/4;
            object-fit: cover;
        }
        .couple-photo-missing {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 180px;
            padding: 1rem;
            text-align: center;
            background: var(--champagne);
            color: var(--gold-dark);
            font-size: 0.85rem;
        }
        .names {
            margin: 22px 0;
        }
        .names .script {
            font-family: 'Great Vibes', cursive;
            font-size: clamp(3.2rem, 10vw, 4.2rem);
            font-weight: 400;
            color: var(--gold-dark);
            display: block;
            line-height: 0.9;
        }
        .names .amp {
            font-family: 'Cinzel', serif;
            font-size: 1.1rem;
            color: var(--muted);
            display: block;
            margin: 8px 0;
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
            padding: 12px;
            border-top: 1px solid rgba(188, 163, 116, 0.3);
        }
        .detail:last-of-type {
            border-bottom: 1px solid rgba(188, 163, 116, 0.3);
        }
        .detail strong {
            display: block;
            font-family: 'Cinzel', serif;
            font-size: 0.65rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--gold-dark);
            margin-bottom: 4px;
        }
        .detail span {
            font-size: 1.12rem;
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
            background: rgba(188, 163, 116, 0.05);
            border: 1px solid rgba(188, 163, 116, 0.2);
            border-radius: 4px;
        }
        .schedule-card h3 {
            font-family: 'Cinzel', serif;
            font-size: 0.72rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin: 0 0 12px;
            color: var(--gold-dark);
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
            font-weight: 600;
            color: var(--ink);
        }
        .sched-dots {
            flex: 1;
            border-bottom: 1px solid rgba(188, 163, 116, 0.15);
            height: 0;
        }
        .sched-when {
            color: var(--muted);
        }
        .rsvp {
            margin-top: 28px;
            font-family: 'Cinzel', serif;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            color: var(--gold-dark);
            font-weight: 600;
        }
        .brand {
            margin-top: 24px;
            font-size: 0.65rem;
            letter-spacing: 0.08em;
            color: rgba(140, 115, 75, 0.45);
            text-transform: uppercase;
        }
        @if(!empty($pngExportScript))
        body.png-export-mode {
            background: var(--champagne) !important;
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
        @php
            $gInitial = substr($d['groom_name'] ?? 'W', 0, 1);
            $bInitial = substr($d['bride_name'] ?? 'I', 0, 1);
        @endphp
        <div class="crest">{{ $gInitial }}{{ $bInitial }}</div>
        <p class="tagline">{{ $d['tagline'] ?? 'Together with our families' }}</p>
        <h1>Request the Honor of your Presence</h1>

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
            <strong>Date & Time</strong>
            <span>{{ $dateLine ?: '—' }}@if(!empty($d['wedding_time'])) · {{ \App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null) }}@endif</span>
        </div>

        <div class="detail">
            <strong>Location</strong>
            <span>{{ $d['venue_name'] ?? '' }}</span>
            <div class="address">{{ $d['venue_address'] ?? '' }}</div>
        </div>

        @if(!empty($d['schedule_events']) && is_array($d['schedule_events']))
        <div class="schedule-card">
            <h3>Event Schedule</h3>
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

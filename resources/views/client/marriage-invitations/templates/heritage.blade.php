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
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Great+Vibes&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        :root {
            --gold: #c9a227;
            --gold-dark: #8b6914;
            --ink: #2c1810;
            --ink-soft: #4a3728;
            --cream: #fffef9;
            --cream-mid: #faf5eb;
        }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Cormorant Garamond', Georgia, serif;
            color: var(--ink);
            /* Soft floral / celebration background (Unsplash — free to use) */
            background-color: #1a0f0a;
            background-image:
                linear-gradient(165deg, rgba(35, 22, 16, 0.82) 0%, rgba(60, 40, 30, 0.65) 40%, rgba(35, 22, 16, 0.88) 100%),
                url('https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=1920&q=75');
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
        /* Outer decorative frame */
        .sheet {
            position: relative;
            padding: 52px 36px 44px;
            background:
                linear-gradient(180deg, rgba(255, 254, 249, 0.97) 0%, rgba(250, 245, 235, 0.96) 45%, rgba(255, 252, 245, 0.97) 100%);
            border: 2px solid rgba(201, 162, 39, 0.55);
            outline: 1px solid rgba(139, 105, 20, 0.35);
            outline-offset: 6px;
            border-radius: 2px;
            box-shadow:
                0 4px 0 rgba(201, 162, 39, 0.15),
                0 32px 64px rgba(0, 0, 0, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }
        .sheet::before {
            content: '';
            position: absolute;
            inset: 14px;
            border: 1px solid rgba(184, 134, 11, 0.2);
            border-radius: 1px;
            pointer-events: none;
        }
        .ornament {
            text-align: center;
            color: var(--gold);
            font-size: 11px;
            letter-spacing: 14px;
            margin-bottom: 12px;
            opacity: 0.95;
        }
        .ornament span {
            display: inline-block;
            transform: rotate(45deg);
            width: 6px;
            height: 6px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            margin: 0 2px;
            vertical-align: middle;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.4);
        }
        h1 {
            text-align: center;
            font-size: clamp(1.35rem, 4vw, 1.65rem);
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin: 0 0 10px;
            color: var(--ink-soft);
        }
        .sub {
            text-align: center;
            font-size: 1.05rem;
            font-style: italic;
            color: #6b5344;
            margin: 0 0 8px;
            font-weight: 500;
        }
        .divider {
            width: 72px;
            height: 1px;
            margin: 20px auto 24px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }
        .couple-photo-wrap {
            max-width: 240px;
            margin: 0 auto 22px;
            padding: 6px;
            background: linear-gradient(145deg, rgba(201, 162, 39, 0.4), rgba(139, 105, 20, 0.22));
            border-radius: 20px;
            box-shadow: 0 10px 28px rgba(44, 24, 16, 0.2);
        }
        .couple-photo-wrap img {
            display: block;
            width: 100%;
            aspect-ratio: 3/4;
            object-fit: cover;
            border-radius: 14px;
        }
        .couple-photo-missing {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 200px;
            padding: 1.25rem;
            text-align: center;
            background: var(--cream-mid);
            border-radius: 14px;
            color: #6b5344;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        .couple-photo-missing .missing-ico {
            color: #b89860;
            margin-bottom: 0.5rem;
        }
        .names {
            text-align: center;
            margin: 8px 0 20px;
            line-height: 1.35;
        }
        .names .script {
            font-family: 'Great Vibes', cursive;
            font-size: clamp(2.5rem, 8vw, 3.35rem);
            font-weight: 400;
            color: var(--ink);
            display: block;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.6);
        }
        .names .amp {
            font-family: 'Great Vibes', cursive;
            font-size: 2rem;
            color: var(--gold-dark);
            display: block;
            margin: 4px 0;
            line-height: 1;
        }
        .parents {
            text-align: center;
            font-size: 0.98rem;
            color: #5c4a3d;
            max-width: 92%;
            margin: 0 auto 28px;
            line-height: 1.55;
            font-weight: 500;
        }
        .parents div + div { margin-top: 6px; }
        .detail {
            margin: 14px 0;
            padding: 18px 22px;
            background: linear-gradient(135deg, rgba(201, 162, 39, 0.09) 0%, rgba(201, 162, 39, 0.04) 100%);
            border: 1px solid rgba(184, 134, 11, 0.22);
            border-radius: 12px;
            text-align: center;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
        }
        .detail strong {
            display: block;
            font-size: 0.68rem;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: #8b7355;
            margin-bottom: 8px;
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
        }
        .detail span { font-size: 1.12rem; font-weight: 500; }
        .address {
            white-space: pre-line;
            font-size: 0.98rem;
            margin-top: 8px;
            color: var(--ink-soft);
            line-height: 1.45;
        }
        .footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.95rem;
            color: #6b5344;
            font-style: italic;
        }
        .brand {
            text-align: center;
            margin-top: 20px;
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            color: #a89888;
            text-transform: uppercase;
        }
        .schedule-card {
            margin-top: 20px;
            padding: 22px 18px 18px;
            background: linear-gradient(165deg, #2a1810 0%, #1a120c 100%);
            color: #f0d78c;
            border-radius: 14px;
            border: 1px solid rgba(201, 162, 39, 0.45);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
        }
        .schedule-card h3 {
            text-align: center;
            font-size: 0.68rem;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            margin: 0 0 18px;
            color: #e8c547;
            font-weight: 600;
        }
        .schedule-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
            margin: 14px 0;
            font-size: 0.98rem;
            line-height: 1.35;
        }
        .sched-title {
            flex-shrink: 0;
            font-weight: 600;
            color: #f5e6b8;
        }
        .sched-dots {
            flex: 1;
            min-width: 16px;
            border-bottom: 1px dotted rgba(232, 197, 71, 0.45);
            margin: 0 2px 5px;
            height: 0;
        }
        .sched-when {
            flex-shrink: 0;
            text-align: right;
            color: #e8c547;
            font-weight: 500;
        }
        @if(!empty($pngExportScript))
        body.png-export-mode {
            background: #1a0f0a !important;
            background-image: none !important;
            background-attachment: scroll !important;
        }
        @endif
        @media print {
            body {
                background: var(--cream-mid) !important;
                background-image: none !important;
                padding: 0;
            }
            .sheet {
                box-shadow: none;
                outline: none;
                border: 3px double var(--gold);
            }
            .sheet::before { inset: 12px; }
        }
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
        <div class="ornament" aria-hidden="true"><span></span><span></span><span></span></div>
        <h1>Wedding Invitation</h1>
        <p class="sub">{{ $d['tagline'] ?? 'Together with our families' }}</p>
        <div class="divider"></div>
        @if($coupleImageOk && $imgSrc)
        <div class="couple-photo-wrap">
            <img src="{{ $imgSrc }}" alt="Couple photo" width="240" height="320"@if(!empty($pngExportScript)) loading="eager" decoding="sync"@else loading="lazy" decoding="async"@endif>
        </div>
        @elseif($couplePath)
        <div class="couple-photo-wrap">
            <div class="couple-photo-missing" role="img" aria-label="Photo not available on server">
                <span class="missing-ico" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.2"/><path d="M3 19l6.5-6.5L14.5 17l2.2-2.2L21 19"/></svg>
                </span>
                <span>We couldn’t load this image. Re-upload the photo in <strong>Edit</strong> on Chandla Book.</span>
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
            <strong>Date</strong>
            <span>{{ $dateLine ?: '—' }}@if(!empty($d['wedding_time'])) · {{ \App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null) }}@endif</span>
        </div>
        <div class="detail">
            <strong>Venue</strong>
            <span>{{ $d['venue_name'] ?? '' }}</span>
            <div class="address">{{ $d['venue_address'] ?? '' }}</div>
        </div>
        @if(!empty($d['schedule_events']) && is_array($d['schedule_events']))
        <div class="schedule-card">
            <h3>Schedule of events</h3>
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
            <div class="footer">RSVP / Contact: {{ $d['rsvp_contact'] }}</div>
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

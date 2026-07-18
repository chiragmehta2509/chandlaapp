<!DOCTYPE html>
<html lang="en"@if(!empty($demoThumbIframe)) class="cb-demo-thumb-scope"@endif>
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-wedding — {{ $milestoneKey }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;0,9..40,800&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Great+Vibes&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&display=swap" rel="stylesheet">
    @include('client.pre-wedding.theme-variants')
    @if(!empty($demoThumbIframe))
    <style>
        .capture-root {
            width: 100% !important;
            height: 100vh !important;
            max-width: none !important;
            aspect-ratio: none !important;
            border-radius: 0 !important;
        }
    </style>
    @endif
</head>
<body @class([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
]) style="margin:0; @if(!empty($demoThumbIframe)) height:100vh; padding:0; background:transparent; overflow:hidden; @else min-height:100vh; background:#0f172a; display:flex; align-items:center; justify-content:center; padding:16px; @endif">
<div class="capture-root pw-theme-{{ $theme }}">
    <div class="pw-bg" style="background-image: url('{{ $bgUrl }}');"></div>
    <div class="pw-scrim" aria-hidden="true"></div>
    <div class="pw-content">
        <div class="pw-headblock">
            <div class="pw-headline-row">
                <span class="pw-h-main">{{ $headline }}</span>
                @if(!empty($headlineSmall))
                    <span class="pw-h-side">{{ $headlineSmall }}</span>
                @endif
            </div>
            @if(!empty($subline))
                <div class="pw-subline">{{ $subline }}</div>
            @endif
            @if(!empty($quote))
                <p class="pw-quote">{{ $quote }}</p>
            @endif
        </div>
        @if(!empty($customText))
            <div class="pw-custom-text">{{ $customText }}</div>
        @endif
    </div>
</div>
@include('client.marriage-invitations.partials.export-png-script')
</body>
</html>

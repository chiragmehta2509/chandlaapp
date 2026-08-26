<!DOCTYPE html>
<html lang="en"<?php if(!empty($demoThumbIframe)): ?> class="cb-demo-thumb-scope"<?php endif; ?>>
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video — <?php echo e($d['groom_name'] ?? ''); ?> & <?php echo e($d['bride_name'] ?? ''); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'DM Sans', system-ui, sans-serif;
            background: #0c1222;
            padding: 24px 16px 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        <?php if(!empty($videoExportScript)): ?>
        body.cr-export {
            background: #0c1222 !important;
        }
        <?php endif; ?>
        /* 9:16 story frame — Canva / Reels style */
        .capture-root {
            width: 100%;
            max-width: 380px;
            aspect-ratio: 9 / 16;
            max-height: min(88vh, 760px);
            margin: 0 auto;
            position: relative;
            border-radius: 28px;
            overflow: hidden;
            box-shadow:
                0 0 0 3px rgba(251, 191, 36, 0.35),
                0 24px 60px rgba(0, 0, 0, 0.45);
            background:
                radial-gradient(ellipse 120% 80% at 50% 0%, rgba(251, 191, 36, 0.18) 0%, transparent 55%),
                radial-gradient(ellipse 90% 60% at 80% 100%, rgba(139, 92, 246, 0.2) 0%, transparent 50%),
                linear-gradient(165deg, #1a2640 0%, #121a2e 45%, #1e1033 100%);
            color: #fefce8;
            padding: 28px 22px 22px;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        .cr-frame {
            position: absolute;
            inset: 12px;
            border: 1px solid rgba(251, 191, 36, 0.22);
            border-radius: 20px;
            pointer-events: none;
        }
        .cr-eyebrow {
            text-align: center;
            font-size: 0.62rem;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: rgba(253, 224, 71, 0.9);
            font-weight: 600;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }
        .cr-photo-shell {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 220px;
            margin: 0 auto 16px;
            padding: 3px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fbbf24, #d97706, #a855f7);
        }
        .cr-photo {
            border-radius: 50%;
            overflow: hidden;
            background: rgba(15, 23, 42, 0.6);
            aspect-ratio: 1;
        }
        .cr-photo img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cr-photo-ph {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 0.72rem;
            line-height: 1.45;
            color: rgba(254, 252, 232, 0.75);
            padding: 1rem;
        }
        .cr-names {
            position: relative;
            z-index: 1;
            text-align: center;
            font-family: 'Playfair Display', Georgia, serif;
            font-weight: 600;
            font-size: clamp(1.65rem, 5.5vw, 2rem);
            line-height: 1.15;
            color: #fff;
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.35);
        }
        .cr-amp {
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic;
            font-weight: 500;
            color: #fde047;
            font-size: 1.35rem;
            margin: 6px 0;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .cr-tag {
            text-align: center;
            font-size: 0.82rem;
            color: rgba(254, 252, 232, 0.82);
            margin-top: 10px;
            line-height: 1.4;
            position: relative;
            z-index: 1;
        }
        .cr-pills {
            margin-top: auto;
            padding-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative;
            z-index: 1;
        }
        .cr-pill {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 14px;
            padding: 12px 14px;
            backdrop-filter: blur(8px);
        }
        .cr-pill strong {
            display: block;
            font-size: 0.55rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #fde047;
            margin-bottom: 4px;
        }
        .cr-pill span {
            font-size: 0.88rem;
            line-height: 1.45;
            color: #fefce8;
        }
        .cr-brand {
            text-align: center;
            margin-top: 12px;
            font-size: 0.55rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(253, 224, 71, 0.45);
            position: relative;
            z-index: 1;
        }
    </style>
    <?php echo $__env->renderWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
</head>
<body class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'cr-export' => !empty($videoExportScript),
]); ?>">
<?php
    extract(\App\Support\MarriageInvitationCard::viewData(
        $d ?? [],
        $coupleImagePdfSrc ?? null,
        $coupleImageDataUri ?? null,
        !empty($videoExportScript)
    ));
?>
<?php if(!empty($demoThumbIframe)): ?><div id="cb-demo-fit-root"><?php endif; ?>
<div class="capture-root">
    <span class="cr-frame" aria-hidden="true"></span>
    <p class="cr-eyebrow"><?php echo e($d['tagline'] ?? "We're getting married"); ?></p>
    <div class="cr-photo-shell">
        <div class="cr-photo">
            <?php if($coupleImageOk && $imgSrc): ?>
                <img src="<?php echo e($imgSrc); ?>" alt="" width="400" height="400" <?php if(!empty($videoExportScript)): ?> loading="eager" decoding="sync" fetchpriority="high" <?php else: ?> loading="lazy" <?php endif; ?>>
            <?php else: ?>
                <div class="cr-photo-ph">Add your photo in Edit to complete this look.</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="cr-names"><?php echo e($d['groom_name'] ?? ''); ?></div>
    <p class="cr-amp">&amp;</p>
    <div class="cr-names"><?php echo e($d['bride_name'] ?? ''); ?></div>
    <div class="cr-pills">
        <div class="cr-pill">
            <strong>When</strong>
            <span><?php echo e($dateLine ?: '—'); ?><?php if(!empty($d['wedding_time'])): ?><br><?php echo e(\App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null)); ?><?php endif; ?></span>
        </div>
        <div class="cr-pill">
            <strong>Where</strong>
            <span><strong style="font-weight:700"><?php echo e($d['venue_name'] ?? ''); ?></strong><?php if(!empty($d['venue_address'])): ?><br><span style="opacity:.9"><?php echo e($d['venue_address']); ?></span><?php endif; ?></span>
        </div>
    </div>
    <p class="cr-brand">Chandla Book</p>
</div>
<?php if(!empty($demoThumbIframe)): ?>
</div>
<?php echo $__env->make('client.marriage-invitations.partials.demo-thumb-fit-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php echo $__env->make('client.marriage-invitations.partials.export-video-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH /home/chandlabook/public_html/resources/views/client/marriage-invitations/templates/canva_reel.blade.php ENDPATH**/ ?>
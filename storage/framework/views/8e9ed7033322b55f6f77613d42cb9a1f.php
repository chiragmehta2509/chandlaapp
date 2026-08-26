<!DOCTYPE html>
<html lang="en"<?php if(!empty($demoThumbIframe)): ?> class="cb-demo-thumb-scope"<?php endif; ?>>
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding — <?php echo e($d['groom_name'] ?? ''); ?> & <?php echo e($d['bride_name'] ?? ''); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: linear-gradient(165deg, #e8e4f0 0%, #f0ecf8 35%, #ebe7f2 100%);
            padding: 28px 16px 40px;
            color: #4c1d95;
        }
        <?php if(!empty($pngExportScript)): ?> body.png-lav { background: #ebe7f2 !important; } <?php endif; ?>
        .capture-root {
            max-width: 420px;
            margin: 0 auto;
            background: #f4f4f7;
            border-radius: 28px;
            padding: 32px 24px 28px;
            box-shadow:
                0 4px 24px rgba(91, 33, 182, 0.08),
                0 0 0 1px rgba(196, 181, 253, 0.35);
        }
        .ld-blob {
            width: 64px;
            height: 64px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: linear-gradient(140deg, #a855f7 0%, #c026d3 45%, #8b5cf6 100%);
            box-shadow: 0 8px 20px rgba(124, 58, 237, 0.25);
        }
        .ld-sub {
            text-align: center;
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 1rem;
            font-weight: 600;
            color: #6d28d9;
            margin: 0 0 20px;
            line-height: 1.35;
        }
        /* Arch / dome frame — half-round top, soft purple rim */
        .ld-photo-shell {
            width: 228px;
            margin: 0 auto 22px;
            padding: 3px;
            border-radius: 114px 114px 22px 22px;
            background: linear-gradient(180deg, #ddd6fe 0%, #c4b5fd 100%);
            box-shadow: 0 10px 28px rgba(109, 40, 217, 0.12);
        }
        .ld-photo {
            border-radius: 111px 111px 19px 19px;
            overflow: hidden;
            background: #ede9fe;
        }
        .ld-photo img {
            display: block;
            width: 100%;
            aspect-ratio: 3 / 4;
            object-fit: cover;
            vertical-align: middle;
        }
        .ld-photo-ph {
            aspect-ratio: 3 / 4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            color: #7c3aed;
            text-align: center;
            padding: 1rem;
            line-height: 1.4;
        }
        .ld-name {
            text-align: center;
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(1.9rem, 5.5vw, 2.45rem);
            font-weight: 700;
            margin: 0;
            color: #4c1d95;
            line-height: 1.12;
            letter-spacing: 0.01em;
        }
        .ld-amp {
            text-align: center;
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-style: italic;
            color: #9333ea;
            font-size: 1.55rem;
            margin: 4px 0 2px;
            line-height: 1;
        }
        .ld-parents {
            text-align: center;
            font-size: 0.82rem;
            color: #6d28d9;
            line-height: 1.55;
            margin: 14px 0 4px;
        }
        .ld-pill {
            margin-top: 14px;
            padding: 16px 18px;
            border-radius: 20px;
            background: #ebe4ff;
            border: 1px solid #ddd6fe;
            text-align: center;
        }
        .ld-pill h2 {
            margin: 0 0 8px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.62rem;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #7c3aed;
            font-weight: 700;
        }
        .ld-pill p {
            margin: 0;
            font-size: 0.92rem;
            line-height: 1.55;
            color: #4c1d95;
            font-weight: 500;
        }
        .ld-pill .ld-venue-line2 {
            margin-top: 6px;
            font-weight: 400;
            white-space: pre-line;
        }
        .ld-sched {
            margin-top: 16px;
            padding: 20px 18px 18px;
            border-radius: 20px;
            background: linear-gradient(165deg, #5b21b6 0%, #4c1d95 55%, #4c1d95 100%);
            color: #f5f3ff;
        }
        .ld-sched h3 {
            text-align: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.62rem;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            margin: 0 0 14px;
            color: #e9d5ff;
            font-weight: 700;
        }
        .ld-srow {
            display: flex;
            align-items: center;
            gap: 0;
            margin: 12px 0;
            font-size: 0.84rem;
            line-height: 1.3;
        }
        .ld-st {
            flex-shrink: 0;
            font-weight: 600;
            color: #fff;
            max-width: 46%;
        }
        .ld-dots {
            flex: 1 1 auto;
            min-width: 12px;
            margin: 0 8px;
            align-self: center;
            height: 0;
            border-bottom: 1px dotted rgba(233, 213, 255, 0.55);
        }
        .ld-sw {
            flex-shrink: 0;
            text-align: right;
            color: #fae8ff;
            font-weight: 500;
            white-space: nowrap;
        }
        .ld-foot {
            text-align: center;
            margin-top: 16px;
            font-size: 0.84rem;
            color: #6d28d9;
            font-weight: 500;
        }
        .ld-brand {
            text-align: center;
            margin-top: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.58rem;
            letter-spacing: 0.18em;
            color: #a78bfa;
            text-transform: uppercase;
            font-weight: 600;
        }
    </style>
    <?php echo $__env->renderWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
</head>
<body class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'png-lav' => !empty($pngExportScript),
]); ?>">
<?php extract(\App\Support\MarriageInvitationCard::viewData($d ?? [], $coupleImagePdfSrc ?? null, $coupleImageDataUri ?? null, !empty($pngExportScript))); ?>
<?php if(!empty($demoThumbIframe)): ?><div id="cb-demo-fit-root"><?php endif; ?>
<div class="capture-root">
    <div class="ld-blob" aria-hidden="true"></div>
    <p class="ld-sub"><?php echo e($d['tagline'] ?? 'A dreamy celebration'); ?></p>
    <div class="ld-photo-shell">
        <div class="ld-photo">
            <?php if($coupleImageOk && $imgSrc): ?>
                <img src="<?php echo e($imgSrc); ?>" alt="" width="222" height="296" <?php if(!empty($pngExportScript)): ?> loading="eager" decoding="sync" fetchpriority="high" <?php else: ?> loading="lazy" <?php endif; ?>>
            <?php else: ?>
                <div class="ld-photo-ph">Your photo appears here when you add one in Edit.</div>
            <?php endif; ?>
        </div>
    </div>
    <h1 class="ld-name"><?php echo e($d['groom_name'] ?? ''); ?></h1>
    <p class="ld-amp">&amp;</p>
    <h1 class="ld-name"><?php echo e($d['bride_name'] ?? ''); ?></h1>
    <?php if(!empty($d['parent_groom']) || !empty($d['parent_bride'])): ?>
    <div class="ld-parents">
        <?php if(!empty($d['parent_groom'])): ?><div><?php echo e($d['parent_groom']); ?></div><?php endif; ?>
        <?php if(!empty($d['parent_bride'])): ?><div><?php echo e($d['parent_bride']); ?></div><?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="ld-pill">
        <h2>When</h2>
        <p><?php echo e($dateLine ?: '—'); ?><?php if(!empty($d['wedding_time'])): ?><br><span style="font-weight:600"><?php echo e(\App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null)); ?></span><?php endif; ?></p>
    </div>
    <div class="ld-pill">
        <h2>Where</h2>
        <p><strong><?php echo e($d['venue_name'] ?? ''); ?></strong></p>
        <?php if(!empty($d['venue_address'])): ?>
            <p class="ld-venue-line2"><?php echo e($d['venue_address']); ?></p>
        <?php endif; ?>
    </div>
    <?php echo $__env->make('client.marriage-invitations.partials.schedule-block', ['d' => $d, 'wrapClass' => 'ld-sched', 'titleClass' => '', 'heading' => 'Schedule', 'rowClass' => 'ld-srow', 'titleCellClass' => 'ld-st', 'dotsClass' => 'ld-dots', 'whenClass' => 'ld-sw'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if(!empty($d['rsvp_contact'])): ?><p class="ld-foot">Contact: <?php echo e($d['rsvp_contact']); ?></p><?php endif; ?>
    <p class="ld-brand">Chandla Book</p>
</div>
<?php if(!empty($demoThumbIframe)): ?>
</div>
<?php echo $__env->make('client.marriage-invitations.partials.demo-thumb-fit-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php echo $__env->make('client.marriage-invitations.partials.export-png-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH /home/chandlabook/public_html/resources/views/client/marriage-invitations/templates/lavender_dream.blade.php ENDPATH**/ ?>
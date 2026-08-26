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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Alex+Brush&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        :root {
            --rose-bg: #fdf5f5;
            --rose-accent: #b87d82;
            --rose-dark: #865256;
            --ink: #3d2b2c;
            --gold: #cfa780;
            --cream: #fffbfb;
        }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--ink);
            background-color: #f7eaea;
            background-image:
                linear-gradient(165deg, rgba(253, 245, 245, 0.95) 0%, rgba(247, 234, 234, 0.85) 60%, rgba(243, 222, 222, 0.95) 100%),
                url('https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=1920&q=75');
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
            padding: 50px 32px 44px;
            background: var(--cream);
            border: 1px solid rgba(184, 125, 130, 0.25);
            box-shadow: 
                0 15px 35px rgba(134, 82, 86, 0.15),
                inset 0 0 0 10px var(--cream),
                inset 0 0 0 11px rgba(207, 167, 128, 0.4);
            border-radius: 8px;
            text-align: center;
        }
        .tagline {
            font-size: 0.8rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--rose-accent);
            margin: 0 0 12px;
            font-weight: 600;
        }
        .flower-ornament {
            font-size: 1.5rem;
            color: var(--rose-accent);
            margin-bottom: 16px;
            line-height: 1;
        }
        h1 {
            font-size: clamp(1.3rem, 4vw, 1.65rem);
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--rose-dark);
            margin: 0 0 20px;
        }
        .couple-photo-wrap {
            max-width: 230px;
            margin: 0 auto 22px;
            padding: 8px;
            background: #fff;
            border: 1px solid rgba(184, 125, 130, 0.15);
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            border-radius: 4px;
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
            background: #fdfafb;
            color: var(--rose-accent);
            font-size: 0.85rem;
        }
        .names {
            margin: 18px 0;
        }
        .names .script {
            font-family: 'Alex Brush', cursive;
            font-size: clamp(3.2rem, 10vw, 4.2rem);
            font-weight: 400;
            color: var(--rose-dark);
            display: block;
            line-height: 0.95;
        }
        .names .amp {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            color: var(--gold);
            display: block;
            margin: 6px 0;
            font-style: italic;
        }
        .parents {
            font-size: 0.95rem;
            color: #614c4e;
            max-width: 90%;
            margin: 0 auto 24px;
            line-height: 1.55;
        }
        .parents div {
            margin-top: 4px;
        }
        .detail {
            margin: 14px 0;
            padding: 14px;
            border-bottom: 1px solid rgba(184, 125, 130, 0.15);
        }
        .detail:last-of-type {
            border-bottom: none;
        }
        .detail strong {
            display: block;
            font-size: 0.72rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--rose-accent);
            margin-bottom: 4px;
        }
        .detail span {
            font-size: 1.08rem;
            font-weight: 600;
            color: var(--ink);
        }
        .address {
            white-space: pre-line;
            font-size: 0.92rem;
            margin-top: 6px;
            color: #614c4e;
            line-height: 1.4;
        }
        .schedule-card {
            margin-top: 22px;
            padding: 18px;
            background: #fffafa;
            border-radius: 6px;
            border: 1px solid rgba(184, 125, 130, 0.15);
        }
        .schedule-card h3 {
            font-size: 0.75rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin: 0 0 14px;
            color: var(--rose-dark);
            font-weight: 700;
        }
        .schedule-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
            margin: 10px 0;
            font-size: 0.92rem;
        }
        .sched-title {
            font-weight: 600;
            color: var(--rose-dark);
        }
        .sched-dots {
            flex: 1;
            border-bottom: 1px dashed rgba(184, 125, 130, 0.2);
            height: 0;
        }
        .sched-when {
            color: #614c4e;
        }
        .rsvp {
            margin-top: 26px;
            font-size: 0.92rem;
            color: var(--rose-accent);
            font-style: italic;
            font-weight: 600;
        }
        .brand {
            margin-top: 24px;
            font-size: 0.68rem;
            letter-spacing: 0.08em;
            color: rgba(134, 82, 86, 0.45);
            text-transform: uppercase;
        }
        <?php if(!empty($pngExportScript)): ?>
        body.png-export-mode {
            background: var(--rose-bg) !important;
            background-image: none !important;
        }
        <?php endif; ?>
    </style>
    <?php echo $__env->renderWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
</head>
<body class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'png-export-mode' => !empty($pngExportScript),
]); ?>">
<?php
    extract(\App\Support\MarriageInvitationCard::viewData(
        $d ?? [],
        $coupleImagePdfSrc ?? null,
        $coupleImageDataUri ?? null,
        !empty($pngExportScript)
    ));
?>
<?php if(!empty($demoThumbIframe)): ?><div id="cb-demo-fit-root"><?php endif; ?>
<div class="page-wrap capture-root">
    <div class="sheet">
        <p class="tagline"><?php echo e($d['tagline'] ?? 'Together with our families'); ?></p>
        <div class="flower-ornament">❦</div>
        <h1>Wedding Invitation</h1>

        <?php if($coupleImageOk && $imgSrc): ?>
        <div class="couple-photo-wrap">
            <img src="<?php echo e($imgSrc); ?>" alt="Couple photo" width="214" height="285"<?php if(!empty($pngExportScript)): ?> loading="eager" decoding="sync"<?php else: ?> loading="lazy" decoding="async"<?php endif; ?>>
        </div>
        <?php elseif($couplePath): ?>
        <div class="couple-photo-wrap">
            <div class="couple-photo-missing" role="img" aria-label="Photo loading failed">
                <span>We couldn’t load this image. Re-upload the photo on Chandla Book.</span>
            </div>
        </div>
        <?php endif; ?>

        <div class="names">
            <span class="script"><?php echo e($d['groom_name'] ?? ''); ?></span>
            <span class="amp">and</span>
            <span class="script"><?php echo e($d['bride_name'] ?? ''); ?></span>
        </div>

        <?php if(!empty($d['parent_groom']) || !empty($d['parent_bride'])): ?>
            <div class="parents">
                <?php if(!empty($d['parent_groom'])): ?><div><?php echo e($d['parent_groom']); ?></div><?php endif; ?>
                <?php if(!empty($d['parent_bride'])): ?><div><?php echo e($d['parent_bride']); ?></div><?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="detail">
            <strong>Date & Time</strong>
            <span><?php echo e($dateLine ?: '—'); ?><?php if(!empty($d['wedding_time'])): ?> · <?php echo e(\App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null)); ?><?php endif; ?></span>
        </div>

        <div class="detail">
            <strong>Location</strong>
            <span><?php echo e($d['venue_name'] ?? ''); ?></span>
            <div class="address"><?php echo e($d['venue_address'] ?? ''); ?></div>
        </div>

        <?php if(!empty($d['schedule_events']) && is_array($d['schedule_events'])): ?>
        <div class="schedule-card">
            <h3>Wedding Schedule</h3>
            <?php $__currentLoopData = $d['schedule_events']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(empty($ev['title'])): ?> <?php continue; ?> <?php endif; ?>
                <?php
                    $schedDate = '';
                    if (!empty($ev['date'])) {
                        try { $schedDate = \Carbon\Carbon::parse($ev['date'])->format('d/m/Y'); } catch (\Throwable $e) { $schedDate = (string) $ev['date']; }
                    }
                    $schedTime = trim((string) ($ev['time'] ?? ''));
                    $schedRight = $schedDate;
                    if ($schedTime !== '') {
                        $schedRight .= ($schedRight !== '' ? ' · ' : '') . $schedTime;
                    }
                ?>
                <div class="schedule-row">
                    <span class="sched-title"><?php echo e($ev['title']); ?></span>
                    <span class="sched-dots" aria-hidden="true"></span>
                    <span class="sched-when"><?php echo e($schedRight !== '' ? $schedRight : '—'); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <?php if(!empty($d['rsvp_contact'])): ?>
            <div class="rsvp">RSVP: <?php echo e($d['rsvp_contact']); ?></div>
        <?php endif; ?>
        
        <div class="brand">Made with Chandla Book</div>
    </div>
</div>
<?php if(!empty($demoThumbIframe)): ?>
</div>
<?php echo $__env->make('client.marriage-invitations.partials.demo-thumb-fit-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php echo $__env->make('client.marriage-invitations.partials.export-png-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH /home/chandlabook/public_html/resources/views/client/marriage-invitations/templates/vintage_rose.blade.php ENDPATH**/ ?>
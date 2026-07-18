<!DOCTYPE html>
<html lang="en"<?php if(!empty($demoThumbIframe)): ?> class="cb-demo-thumb-scope"<?php endif; ?>>
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding — <?php echo e($d['groom_name'] ?? ''); ?> & <?php echo e($d['bride_name'] ?? ''); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Tangerine:wght@700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #fef3c7 0%, #fce7f3 35%, #fed7aa 70%, #fde68a 100%); padding: 26px 14px; color: #78350f; }
        <?php if(!empty($pngExportScript)): ?> body.png-saf { background: #fffbeb !important; } <?php endif; ?>
        .capture-root { max-width: 520px; margin: 0 auto; background: linear-gradient(180deg, #fffbeb 0%, #fff 40%, #fff7ed 100%); border-radius: 16px; border: 3px solid #f59e0b; box-shadow: 0 8px 0 #d97706, 0 24px 48px rgba(217,119,6,.2); padding: 32px 26px 28px; position: relative; overflow: hidden; }
        .capture-root::before { content: '✦'; position: absolute; top: 12px; left: 16px; color: #ec4899; font-size: 1.2rem; opacity: .7; }
        .capture-root::after { content: '✦'; position: absolute; top: 12px; right: 16px; color: #ec4899; font-size: 1.2rem; opacity: .7; }
        .sf-banner { text-align: center; font-size: .62rem; font-weight: 700; letter-spacing: .25em; text-transform: uppercase; color: #b45309; margin-bottom: 8px; }
        .sf-script { text-align: center; font-family: 'Tangerine', cursive; font-size: 2.75rem; font-weight: 700; color: #be185d; line-height: 1; margin: 8px 0 14px; text-shadow: 0 1px 0 rgba(255,255,255,.8); }
        .sf-photo { max-width: 230px; margin: 0 auto 14px; border-radius: 12px; overflow: hidden; border: 3px solid #fbbf24; box-shadow: 0 0 0 2px #be185d, 0 12px 28px rgba(190,24,93,.2); }
        .sf-photo img { display: block; width: 100%; aspect-ratio: 3/4; object-fit: cover; }
        .sf-name { text-align: center; font-size: clamp(1.35rem, 4vw, 1.65rem); font-weight: 600; color: #92400e; margin: 0; line-height: 1.2; }
        .sf-amp { text-align: center; font-size: 1.75rem; color: #db2777; font-weight: 600; margin: 4px 0; }
        .sf-parents { text-align: center; font-size: .84rem; color: #a16207; line-height: 1.5; margin: 12px 0; padding: 12px; background: rgba(251,191,36,.15); border-radius: 12px; }
        .sf-card { margin-top: 12px; padding: 14px 16px; border-radius: 12px; background: linear-gradient(90deg, rgba(236,72,153,.08), rgba(245,158,11,.12), rgba(236,72,153,.08)); border: 1px dashed #f59e0b; text-align: center; }
        .sf-card h2 { margin: 0 0 6px; font-size: .55rem; letter-spacing: .2em; text-transform: uppercase; color: #be185d; font-weight: 700; }
        .sf-card p { margin: 0; font-size: .88rem; }
        .sf-sched { margin-top: 14px; padding: 16px; border-radius: 14px; background: linear-gradient(145deg, #9d174d 0%, #be185d 100%); color: #fffbeb; border: 2px solid #fbbf24; }
        .sf-sched h3 { text-align: center; font-size: .55rem; letter-spacing: .22em; text-transform: uppercase; margin: 0 0 12px; color: #fde047; font-weight: 700; }
        .sf-srow { display: flex; align-items: baseline; gap: 6px; margin: 10px 0; font-size: .82rem; }
        .sf-st { flex-shrink: 0; font-weight: 600; color: #fff; }
        .sf-dots { flex: 1; border-bottom: 1px dotted rgba(253,224,71,.5); min-width: 10px; margin: 0 2px 4px; height: 0; }
        .sf-sw { flex-shrink: 0; color: #fde047; font-weight: 500; }
        .sf-foot { text-align: center; margin-top: 14px; font-size: .84rem; color: #b45309; font-weight: 500; }
        .sf-brand { text-align: center; margin-top: 12px; font-size: .55rem; letter-spacing: .15em; color: #d97706; text-transform: uppercase; font-weight: 600; }
    </style>
    <?php echo $__env->renderWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
</head>
<body class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'png-saf' => !empty($pngExportScript),
]); ?>">
<?php extract(\App\Support\MarriageInvitationCard::viewData($d ?? [], $coupleImagePdfSrc ?? null, $coupleImageDataUri ?? null, !empty($pngExportScript))); ?>
<?php if(!empty($demoThumbIframe)): ?><div id="cb-demo-fit-root"><?php endif; ?>
<div class="capture-root">
    <p class="sf-banner"><?php echo e($d['tagline'] ?? 'Shubh vivah'); ?></p>
    <p class="sf-script">Celebration</p>
    <?php if($coupleImageOk && $imgSrc): ?>
    <div class="sf-photo">
        <img src="<?php echo e($imgSrc); ?>" alt="" width="230" height="307" <?php if(!empty($pngExportScript)): ?> loading="eager" decoding="sync" <?php else: ?> loading="lazy" <?php endif; ?>>
    </div>
    <?php endif; ?>
    <h1 class="sf-name"><?php echo e($d['groom_name'] ?? ''); ?></h1>
    <p class="sf-amp">♥</p>
    <h1 class="sf-name"><?php echo e($d['bride_name'] ?? ''); ?></h1>
    <?php if(!empty($d['parent_groom']) || !empty($d['parent_bride'])): ?>
    <div class="sf-parents">
        <?php if(!empty($d['parent_groom'])): ?><div><?php echo e($d['parent_groom']); ?></div><?php endif; ?>
        <?php if(!empty($d['parent_bride'])): ?><div><?php echo e($d['parent_bride']); ?></div><?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="sf-card">
        <h2>When</h2>
        <p><?php echo e($dateLine ?: '—'); ?><?php if(!empty($d['wedding_time'])): ?> · <?php echo e(\App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null)); ?><?php endif; ?></p>
    </div>
    <div class="sf-card">
        <h2>Where</h2>
        <p><strong style="color:#9d174d"><?php echo e($d['venue_name'] ?? ''); ?></strong></p>
        <p style="white-space:pre-line;margin-top:6px"><?php echo e($d['venue_address'] ?? ''); ?></p>
    </div>
    <?php echo $__env->make('client.marriage-invitations.partials.schedule-block', ['d' => $d, 'wrapClass' => 'sf-sched', 'titleClass' => '', 'heading' => 'Events', 'rowClass' => 'sf-srow', 'titleCellClass' => 'sf-st', 'dotsClass' => 'sf-dots', 'whenClass' => 'sf-sw'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if(!empty($d['rsvp_contact'])): ?><p class="sf-foot">Contact: <?php echo e($d['rsvp_contact']); ?></p><?php endif; ?>
    <p class="sf-brand">Chandla Book</p>
</div>
<?php if(!empty($demoThumbIframe)): ?>
</div>
<?php echo $__env->make('client.marriage-invitations.partials.demo-thumb-fit-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php echo $__env->make('client.marriage-invitations.partials.export-png-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/marriage-invitations/templates/saffron_festival.blade.php ENDPATH**/ ?>
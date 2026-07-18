<!DOCTYPE html>
<html lang="en"<?php if(!empty($demoThumbIframe)): ?> class="cb-demo-thumb-scope"<?php endif; ?>>
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding — <?php echo e($d['groom_name'] ?? ''); ?> & <?php echo e($d['bride_name'] ?? ''); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&family=Fraunces:opsz,wght@9..144,500;9..144,600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'Outfit', sans-serif; background: linear-gradient(165deg, #e0f2fe 0%, #cffafe 40%, #f0f9ff 100%); padding: 28px 16px; color: #0c4a6e; }
        <?php if(!empty($pngExportScript)): ?> body.png-coast { background: #e0f2fe !important; } <?php endif; ?>
        .capture-root { max-width: 505px; margin: 0 auto; background: rgba(255,255,255,.92); border-radius: 24px; padding: 36px 28px 32px; box-shadow: 0 20px 48px rgba(14,116,144,.12), 0 0 0 1px rgba(125,211,252,.5); }
        .cb-wave { height: 5px; border-radius: 999px; margin-bottom: 20px; background: linear-gradient(90deg, #22d3ee, #38bdf8, #0ea5e9, #22d3ee); opacity: .85; }
        .cb-eyebrow { text-align: center; font-size: .68rem; letter-spacing: .2em; text-transform: uppercase; color: #0891b2; font-weight: 600; margin-bottom: 8px; }
        .cb-names { text-align: center; font-family: 'Fraunces', serif; font-size: clamp(1.7rem, 4.5vw, 2.1rem); font-weight: 600; color: #0e7490; line-height: 1.15; }
        .cb-names span { display: block; font-family: 'Outfit', sans-serif; font-weight: 400; font-size: 1.35rem; color: #06b6d4; margin: 10px 0; }
        .cb-photo { max-width: 230px; margin: 16px auto; border-radius: 20px; overflow: hidden; border: 3px solid #bae6fd; box-shadow: 0 12px 28px rgba(14,165,233,.15); }
        .cb-photo img { display: block; width: 100%; aspect-ratio: 3/4; object-fit: cover; }
        .cb-parents { text-align: center; font-size: .86rem; color: #0369a1; line-height: 1.5; margin: 12px 0; }
        .cb-card { margin-top: 14px; padding: 16px 18px; border-radius: 16px; background: linear-gradient(135deg, #f0f9ff 0%, #ecfeff 100%); border: 1px solid #7dd3fc; text-align: center; }
        .cb-card h2 { margin: 0 0 6px; font-size: .6rem; letter-spacing: .18em; text-transform: uppercase; color: #0284c7; font-weight: 600; }
        .cb-card p { margin: 0; font-size: .92rem; line-height: 1.5; }
        .cb-sched { margin-top: 14px; padding: 18px; border-radius: 16px; background: linear-gradient(145deg, #0e7490 0%, #155e75 100%); color: #ecfeff; }
        .cb-sched h3 { text-align: center; font-size: .6rem; letter-spacing: .2em; text-transform: uppercase; margin: 0 0 12px; color: #a5f3fc; font-weight: 600; }
        .cb-srow { display: flex; align-items: baseline; gap: 6px; margin: 10px 0; font-size: .84rem; }
        .cb-st { flex-shrink: 0; font-weight: 600; color: #fff; }
        .cb-dots { flex: 1; border-bottom: 1px dotted rgba(165,243,252,.5); min-width: 10px; margin: 0 2px 4px; height: 0; }
        .cb-sw { flex-shrink: 0; color: #fde047; }
        .cb-foot { text-align: center; margin-top: 14px; font-size: .84rem; color: #0369a1; }
        .cb-brand { text-align: center; margin-top: 12px; font-size: .6rem; letter-spacing: .12em; color: #7dd3fc; text-transform: uppercase; }
    </style>
    <?php echo $__env->renderWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
</head>
<body class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'png-coast' => !empty($pngExportScript),
]); ?>">
<?php extract(\App\Support\MarriageInvitationCard::viewData($d ?? [], $coupleImagePdfSrc ?? null, $coupleImageDataUri ?? null, !empty($pngExportScript))); ?>
<?php if(!empty($demoThumbIframe)): ?><div id="cb-demo-fit-root"><?php endif; ?>
<div class="capture-root">
    <div class="cb-wave"></div>
    <p class="cb-eyebrow"><?php echo e($d['tagline'] ?? 'By the water'); ?></p>
    <div class="cb-names"><?php echo e($d['groom_name'] ?? ''); ?><span>&amp;</span><?php echo e($d['bride_name'] ?? ''); ?></div>
    <?php if($coupleImageOk && $imgSrc): ?>
    <div class="cb-photo">
        <img src="<?php echo e($imgSrc); ?>" alt="" width="230" height="307" <?php if(!empty($pngExportScript)): ?> loading="eager" decoding="sync" <?php else: ?> loading="lazy" <?php endif; ?>>
    </div>
    <?php endif; ?>
    <?php if(!empty($d['parent_groom']) || !empty($d['parent_bride'])): ?>
    <div class="cb-parents">
        <?php if(!empty($d['parent_groom'])): ?><div><?php echo e($d['parent_groom']); ?></div><?php endif; ?>
        <?php if(!empty($d['parent_bride'])): ?><div><?php echo e($d['parent_bride']); ?></div><?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="cb-card">
        <h2>When</h2>
        <p><?php echo e($dateLine ?: '—'); ?><?php if(!empty($d['wedding_time'])): ?><br><?php echo e(\App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null)); ?><?php endif; ?></p>
    </div>
    <div class="cb-card">
        <h2>Where</h2>
        <p><strong style="color:#0e7490"><?php echo e($d['venue_name'] ?? ''); ?></strong></p>
        <p style="white-space:pre-line;margin-top:6px"><?php echo e($d['venue_address'] ?? ''); ?></p>
    </div>
    <?php echo $__env->make('client.marriage-invitations.partials.schedule-block', ['d' => $d, 'wrapClass' => 'cb-sched', 'titleClass' => '', 'heading' => 'Schedule', 'rowClass' => 'cb-srow', 'titleCellClass' => 'cb-st', 'dotsClass' => 'cb-dots', 'whenClass' => 'cb-sw'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if(!empty($d['rsvp_contact'])): ?><p class="cb-foot">Contact: <?php echo e($d['rsvp_contact']); ?></p><?php endif; ?>
    <p class="cb-brand">Chandla Book</p>
</div>
<?php if(!empty($demoThumbIframe)): ?>
</div>
<?php echo $__env->make('client.marriage-invitations.partials.demo-thumb-fit-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php echo $__env->make('client.marriage-invitations.partials.export-png-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/marriage-invitations/templates/coastal_breeze.blade.php ENDPATH**/ ?>
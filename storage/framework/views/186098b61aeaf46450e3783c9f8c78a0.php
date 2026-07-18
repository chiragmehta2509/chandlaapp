<!DOCTYPE html>
<html lang="en"<?php if(!empty($demoThumbIframe)): ?> class="cb-demo-thumb-scope"<?php endif; ?>>
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding — <?php echo e($d['groom_name'] ?? ''); ?> & <?php echo e($d['bride_name'] ?? ''); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Jost:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'Jost', sans-serif; background: linear-gradient(160deg, #fce7f3 0%, #ecfccb 45%, #d9f99d 100%); padding: 28px 16px; color: #3f3f46; }
        <?php if(!empty($pngExportScript)): ?> body.png-garden { background: #fdf2f8 !important; } <?php endif; ?>
        .capture-root { max-width: 500px; margin: 0 auto; background: #fffefb; border-radius: 28px; padding: 36px 28px 32px; box-shadow: 0 20px 50px rgba(190,24,93,.12), 0 0 0 1px rgba(21,128,61,.12); position: relative; overflow: hidden; }
        .capture-root::after { content: ''; position: absolute; top: -40px; right: -40px; width: 140px; height: 140px; background: radial-gradient(circle, rgba(34,197,94,.2) 0%, transparent 70%); pointer-events: none; }
        .gb-floral { height: 6px; border-radius: 999px; margin-bottom: 22px; background: linear-gradient(90deg, #f9a8d4, #86efac, #fde047, #f9a8d4); opacity: .9; }
        .gb-eyebrow { text-align: center; font-size: .68rem; letter-spacing: .2em; text-transform: uppercase; color: #a855f7; font-weight: 600; margin-bottom: 10px; }
        .gb-names { text-align: center; font-family: 'Fraunces', Georgia, serif; font-size: clamp(1.65rem, 4.5vw, 2rem); font-weight: 600; color: #831843; line-height: 1.2; }
        .gb-names span { display: block; font-family: 'Jost', sans-serif; font-size: 1.5rem; font-weight: 500; color: #16a34a; margin: 10px 0; }
        .gb-photo { max-width: 240px; margin: 18px auto; border-radius: 22px; overflow: hidden; border: 3px solid #fce7f3; box-shadow: 0 12px 28px rgba(190,24,93,.1); }
        .gb-photo img { display: block; width: 100%; aspect-ratio: 3/4; object-fit: cover; }
        .gb-parents { text-align: center; font-size: .88rem; color: #52525b; line-height: 1.5; margin: 14px 0; }
        .gb-block { margin-top: 16px; padding: 18px; border-radius: 18px; background: linear-gradient(135deg, #fdf2f8 0%, #f0fdf4 100%); border: 1px solid #f9a8d4; text-align: center; }
        .gb-block h2 { margin: 0 0 8px; font-size: .62rem; letter-spacing: .18em; text-transform: uppercase; color: #be185d; font-weight: 600; }
        .gb-block p { margin: 0; font-size: .95rem; line-height: 1.5; color: #3f3f46; }
        .gb-sched { margin-top: 16px; padding: 18px; border-radius: 18px; background: #14532d; color: #ecfccb; }
        .gb-sched h3 { text-align: center; font-size: .62rem; letter-spacing: .2em; text-transform: uppercase; margin: 0 0 12px; color: #f9a8d4; font-weight: 600; }
        .gb-srow { display: flex; align-items: baseline; gap: 6px; margin: 10px 0; font-size: .85rem; }
        .gb-st { flex-shrink: 0; font-weight: 600; color: #fff; }
        .gb-dots { flex: 1; border-bottom: 1px dotted rgba(249,168,212,.5); min-width: 10px; margin: 0 2px 4px; height: 0; }
        .gb-sw { flex-shrink: 0; color: #fde047; }
        .gb-contact { text-align: center; margin-top: 16px; font-size: .88rem; color: #71717a; }
        .gb-brand { text-align: center; margin-top: 14px; font-size: .62rem; letter-spacing: .12em; color: #a1a1aa; text-transform: uppercase; }
    </style>
    <?php echo $__env->renderWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
</head>
<body class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'png-garden' => !empty($pngExportScript),
]); ?>">
<?php extract(\App\Support\MarriageInvitationCard::viewData($d ?? [], $coupleImagePdfSrc ?? null, $coupleImageDataUri ?? null, !empty($pngExportScript))); ?>
<?php if(!empty($demoThumbIframe)): ?><div id="cb-demo-fit-root"><?php endif; ?>
<div class="capture-root">
    <div class="gb-floral"></div>
    <p class="gb-eyebrow"><?php echo e($d['tagline'] ?? 'With love'); ?></p>
    <div class="gb-names"><?php echo e($d['groom_name'] ?? ''); ?><span>&amp;</span><?php echo e($d['bride_name'] ?? ''); ?></div>
    <?php if($coupleImageOk && $imgSrc): ?>
    <div class="gb-photo">
        <img src="<?php echo e($imgSrc); ?>" alt="" width="240" height="320" <?php if(!empty($pngExportScript)): ?> loading="eager" decoding="sync" <?php else: ?> loading="lazy" <?php endif; ?>>
    </div>
    <?php endif; ?>
    <?php if(!empty($d['parent_groom']) || !empty($d['parent_bride'])): ?>
    <div class="gb-parents">
        <?php if(!empty($d['parent_groom'])): ?><div><?php echo e($d['parent_groom']); ?></div><?php endif; ?>
        <?php if(!empty($d['parent_bride'])): ?><div><?php echo e($d['parent_bride']); ?></div><?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="gb-block">
        <h2>When</h2>
        <p><?php echo e($dateLine ?: '—'); ?><?php if(!empty($d['wedding_time'])): ?><br><?php echo e(\App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null)); ?><?php endif; ?></p>
    </div>
    <div class="gb-block">
        <h2>Where</h2>
        <p><strong style="color:#831843"><?php echo e($d['venue_name'] ?? ''); ?></strong></p>
        <p style="white-space:pre-line;margin-top:6px"><?php echo e($d['venue_address'] ?? ''); ?></p>
    </div>
    <?php echo $__env->make('client.marriage-invitations.partials.schedule-block', ['d' => $d, 'wrapClass' => 'gb-sched', 'titleClass' => '', 'heading' => 'Schedule', 'rowClass' => 'gb-srow', 'titleCellClass' => 'gb-st', 'dotsClass' => 'gb-dots', 'whenClass' => 'gb-sw'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if(!empty($d['rsvp_contact'])): ?><p class="gb-contact">Contact: <?php echo e($d['rsvp_contact']); ?></p><?php endif; ?>
    <p class="gb-brand">Chandla Book</p>
</div>
<?php if(!empty($demoThumbIframe)): ?>
</div>
<?php echo $__env->make('client.marriage-invitations.partials.demo-thumb-fit-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php echo $__env->make('client.marriage-invitations.partials.export-png-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/marriage-invitations/templates/garden_blush.blade.php ENDPATH**/ ?>
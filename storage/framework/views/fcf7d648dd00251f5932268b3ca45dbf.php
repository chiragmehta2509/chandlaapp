<!DOCTYPE html>
<html lang="en"<?php if(!empty($demoThumbIframe)): ?> class="cb-demo-thumb-scope"<?php endif; ?>>
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-wedding — <?php echo e($milestoneKey); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;0,9..40,800&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Great+Vibes&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&display=swap" rel="stylesheet">
    <?php echo $__env->make('client.pre-wedding.theme-variants', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if(!empty($demoThumbIframe)): ?>
    <style>
        .capture-root {
            width: 100% !important;
            height: 100vh !important;
            max-width: none !important;
            aspect-ratio: none !important;
            border-radius: 0 !important;
        }
    </style>
    <?php endif; ?>
</head>
<body class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
]); ?>" style="margin:0; <?php if(!empty($demoThumbIframe)): ?> height:100vh; padding:0; background:transparent; overflow:hidden; <?php else: ?> min-height:100vh; background:#0f172a; display:flex; align-items:center; justify-content:center; padding:16px; <?php endif; ?>">
<div class="capture-root pw-theme-<?php echo e($theme); ?>">
    <div class="pw-bg" style="background-image: url('<?php echo e($bgUrl); ?>');"></div>
    <div class="pw-scrim" aria-hidden="true"></div>
    <div class="pw-content">
        <div class="pw-headblock">
            <div class="pw-headline-row">
                <span class="pw-h-main"><?php echo e($headline); ?></span>
                <?php if(!empty($headlineSmall)): ?>
                    <span class="pw-h-side"><?php echo e($headlineSmall); ?></span>
                <?php endif; ?>
            </div>
            <?php if(!empty($subline)): ?>
                <div class="pw-subline"><?php echo e($subline); ?></div>
            <?php endif; ?>
            <?php if(!empty($quote)): ?>
                <p class="pw-quote"><?php echo e($quote); ?></p>
            <?php endif; ?>
        </div>
        <?php if(!empty($customText)): ?>
            <div class="pw-custom-text"><?php echo e($customText); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php echo $__env->make('client.marriage-invitations.partials.export-png-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH /home/chandlabook/public_html/resources/views/client/pre-wedding/card-export.blade.php ENDPATH**/ ?>
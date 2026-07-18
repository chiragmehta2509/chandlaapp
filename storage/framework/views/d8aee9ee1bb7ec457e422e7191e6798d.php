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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,400&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'DM Sans', system-ui, sans-serif;
            color: #1e293b;
            padding: 28px 16px 40px;
            background-color: #0f172a;
            background-image:
                linear-gradient(145deg, rgba(15, 23, 42, 0.88) 0%, rgba(30, 27, 75, 0.75) 50%, rgba(15, 23, 42, 0.9) 100%),
                url('https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=1920&q=75');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .sheet {
            max-width: 520px;
            margin: 0 auto;
            padding: 48px 36px 40px;
            background: rgba(255, 255, 255, 0.94);
            border-radius: 20px;
            box-shadow:
                0 4px 24px rgba(0, 0, 0, 0.12),
                0 24px 48px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
        }
        .floral {
            height: 5px;
            border-radius: 999px;
            margin-bottom: 28px;
            background: linear-gradient(90deg, transparent, #e879f9, #a78bfa, #38bdf8, #e879f9, transparent);
            opacity: 0.9;
        }
        .eyebrow {
            text-align: center;
            font-size: 0.72rem;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 14px;
            font-weight: 600;
        }
        .names {
            text-align: center;
            font-family: 'Fraunces', Georgia, serif;
            font-size: clamp(1.75rem, 5vw, 2.15rem);
            font-weight: 600;
            line-height: 1.2;
            color: #0f172a;
        }
        .names span {
            display: block;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            font-size: 1.35rem;
            background: linear-gradient(90deg, #7c3aed, #db2777, #0891b2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin: 14px 0;
        }
        .couple-photo-minimal {
            max-width: 260px;
            margin: 20px auto 8px;
            border-radius: 18px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
        }
        .couple-photo-minimal img {
            display: block;
            width: 100%;
            aspect-ratio: 4/5;
            object-fit: cover;
        }
        .couple-photo-missing-min {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 180px;
            padding: 1.25rem;
            text-align: center;
            background: #f1f5f9;
            color: #64748b;
            font-size: 0.86rem;
            line-height: 1.45;
        }
        .couple-photo-missing-min svg { margin-bottom: 0.5rem; color: #94a3b8; }
        .block {
            margin-top: 26px;
            padding: 22px 20px;
            border-radius: 14px;
            text-align: center;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
        }
        .block h2 {
            font-size: 0.65rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
            margin: 0 0 10px;
            font-weight: 600;
        }
        .block p { margin: 0; font-size: 1.02rem; color: #334155; line-height: 1.55; }
        .address { white-space: pre-line; }
        .contact { margin-top: 22px; font-size: 0.92rem; color: #64748b; text-align: center; }
        .brand {
            text-align: center;
            margin-top: 36px;
            font-size: 0.68rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #94a3b8;
        }
        .schedule-minimal {
            margin-top: 22px;
            padding: 22px 18px 18px;
            border-radius: 16px;
            background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
            color: #fef3c7;
            border: 1px solid #334155;
        }
        .schedule-minimal h2 {
            text-align: center;
            font-size: 0.65rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #fcd34d;
            margin: 0 0 16px;
            font-weight: 600;
        }
        .schedule-minimal .schedule-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
            margin: 12px 0;
            font-size: 0.95rem;
        }
        .schedule-minimal .sched-title {
            flex-shrink: 0;
            font-weight: 600;
            color: #fffbeb;
        }
        .schedule-minimal .sched-dots {
            flex: 1;
            min-width: 12px;
            border-bottom: 1px dotted rgba(252, 211, 77, 0.4);
            margin: 0 2px 5px;
            height: 0;
        }
        .schedule-minimal .sched-when {
            flex-shrink: 0;
            color: #fcd34d;
            font-weight: 500;
        }
        <?php if(!empty($pngExportScript)): ?>
        body.png-export-mode-minimal {
            background: #0f172a !important;
            background-image: none !important;
            background-attachment: scroll !important;
        }
        .png-export-mode-minimal .sheet {
            backdrop-filter: none;
        }
        <?php endif; ?>
        @media print {
            body {
                background: #f8fafc !important;
                background-image: none !important;
                padding: 0;
            }
            .sheet {
                box-shadow: none;
                border: 1px solid #e2e8f0;
            }
        }
    </style>
    <?php echo $__env->renderWhen(!empty($demoThumbIframe), 'client.marriage-invitations.partials.demo-thumb-fit-head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
</head>
<body class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'cb-demo-thumb-fit' => !empty($demoThumbIframe),
    'png-export-mode-minimal' => !empty($pngExportScript),
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
<div class="sheet capture-root">
    <div class="floral"></div>
    <p class="eyebrow">We invite you to celebrate</p>
    <div class="names">
        <?php echo e($d['groom_name'] ?? ''); ?>

        <span>&amp;</span>
        <?php echo e($d['bride_name'] ?? ''); ?>

    </div>
    <?php if($coupleImageOk && $imgSrc): ?>
    <div class="couple-photo-minimal">
        <img src="<?php echo e($imgSrc); ?>" alt="Couple photo" width="260" height="325"<?php if(!empty($pngExportScript)): ?> loading="eager" decoding="sync"<?php else: ?> loading="lazy" decoding="async"<?php endif; ?>>
    </div>
    <?php elseif($couplePath): ?>
    <div class="couple-photo-minimal" style="overflow: visible; border-color: #cbd5e1;">
        <div class="couple-photo-missing-min" role="img" aria-label="Photo not available on server">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.2"/><path d="M3 19l6.5-6.5L14.5 17l2.2-2.2L21 19"/></svg>
            <span>Image not found. Re-upload in <strong>Edit</strong> in Chandla Book.</span>
        </div>
    </div>
    <?php endif; ?>
    <div class="block">
        <h2>When</h2>
        <p><?php echo e($dateLine ?: '—'); ?><?php if(!empty($d['wedding_time'])): ?><br><?php echo e(\App\Support\MarriageInvitationCard::formatWeddingTimeForDisplay($d['wedding_time'] ?? null)); ?><?php endif; ?></p>
    </div>
    <div class="block">
        <h2>Where</h2>
        <p><strong style="font-weight:600;color:#0f172a;"><?php echo e($d['venue_name'] ?? ''); ?></strong></p>
        <p class="address"><?php echo e($d['venue_address'] ?? ''); ?></p>
    </div>
    <?php if(!empty($d['schedule_events']) && is_array($d['schedule_events'])): ?>
    <div class="schedule-minimal">
        <h2>Schedule of events</h2>
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
        <p class="contact">Contact: <?php echo e($d['rsvp_contact']); ?></p>
    <?php endif; ?>
    <p class="brand">Chandla Book</p>
</div>
<?php if(!empty($demoThumbIframe)): ?>
</div>
<?php echo $__env->make('client.marriage-invitations.partials.demo-thumb-fit-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php echo $__env->make('client.marriage-invitations.partials.export-png-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/marriage-invitations/templates/minimal.blade.php ENDPATH**/ ?>
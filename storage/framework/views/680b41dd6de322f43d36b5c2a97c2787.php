<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta charset="utf-8">
    <title>Entire Ledger — Chandla Book</title>
    <?php
        $pdfFontUrl = null;
        if (!empty($gujaratiFontPath) && file_exists($gujaratiFontPath)) {
            $pdfFontUrl = 'file://' . str_replace('\\', '/', $gujaratiFontPath);
        }
        $logoPath = null;
        foreach (['images/logo.jpeg', 'images/logo.png', 'images/chandla-logo.png', 'images/chandla-logo.jpg'] as $img) {
            $potentialPath = public_path($img);
            if (file_exists($potentialPath)) {
                $logoPath = 'file://' . str_replace('\\', '/', $potentialPath);
                break;
            }
        }
        $hasLogo = !empty($logoPath);
    ?>
    <style>
        <?php if($pdfFontUrl): ?>
        @font-face {
            font-family: 'PdfGujarati';
            font-style: normal;
            font-weight: 400;
            src: url('<?php echo e($pdfFontUrl); ?>') format('truetype');
        }
        <?php endif; ?>

        @page {
            margin: 24mm 11mm 20mm 11mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: <?php echo e($pdfFontUrl ? "'PdfGujarati', 'DejaVu Sans'" : "'DejaVu Sans'"); ?>, sans-serif;
            font-size: 10.5px;
            line-height: 1.45;
            color: #1f2937;
            background: #fdfbf7;
        }

        .inr {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .serif-title {
            font-family: 'DejaVu Serif', serif;
            letter-spacing: 0.02em;
        }

        .pdf-fixed-logo {
            position: fixed;
            top: -18mm;
            right: 0;
            z-index: 1000;
            text-align: right;
        }

        .pdf-fixed-logo img {
            height: 46px;
            width: auto;
            display: block;
        }

        /* ==================== PAGE 2: OUR PLANS ==================== */
        .plans-wrap {
            page-break-after: always;
            padding: 2mm 0;
        }

        .plans-header {
            text-align: center;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #b8860b;
        }

        .plans-header h2 {
            font-family: 'DejaVu Serif', serif;
            font-size: 18px;
            color: #1a3646;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .plans-header p {
            margin: 0;
            font-size: 10px;
            color: #7c6b52;
            font-style: italic;
        }

        table.plans-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 12px;
        }

        table.plans-grid td.plan-card {
            width: 50%;
            vertical-align: top;
            background: #fffefb;
            border: 1px solid #c9b896;
            border-radius: 4px;
            padding: 10px 12px;
        }

        table.plans-grid td.plan-card.featured {
            border: 2px solid #b8860b;
            background: #fdfbf5;
        }

        .plan-badge {
            display: inline-block;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            background: #b8860b;
            color: #ffffff;
            padding: 2px 8px;
            border-radius: 2px;
            float: right;
        }

        .plan-name {
            font-family: 'DejaVu Serif', serif;
            font-size: 13px;
            font-weight: bold;
            color: #1a3646;
            margin: 0 0 4px 0;
        }

        .plan-price {
            font-size: 16px;
            font-weight: bold;
            color: #b8860b;
            margin-bottom: 6px;
        }

        .plan-price span.period {
            font-size: 9px;
            color: #6b7280;
            font-weight: normal;
        }

        .plan-desc {
            font-size: 9px;
            color: #4b5563;
            margin-bottom: 8px;
            line-height: 1.35;
        }

        ul.plan-features {
            margin: 0;
            padding-left: 14px;
            font-size: 9px;
            color: #374151;
        }

        ul.plan-features li {
            margin-bottom: 3px;
        }

        .plans-footer-box {
            background: #1a3646;
            color: #faf7ef;
            padding: 10px 14px;
            border-radius: 4px;
            margin-top: 10px;
            text-align: center;
        }

        .plans-footer-box h4 {
            margin: 0 0 4px 0;
            font-size: 11px;
            color: #b8860b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .plans-footer-box p {
            margin: 0;
            font-size: 9.5px;
            color: #e5e7eb;
            line-height: 1.4;
        }

        /* ==================== LAST PAGE: CONTACT US ==================== */
        .contact-wrap {
            page-break-before: always;
            padding: 6mm 4mm;
        }

        .contact-card {
            border: 2px solid #1a3646;
            background: #fefdfb;
            border-radius: 6px;
            padding: 16px;
            margin-top: 10px;
        }

        .contact-head {
            text-align: center;
            border-bottom: 2px solid #b8860b;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .contact-head h2 {
            font-family: 'DejaVu Serif', serif;
            font-size: 20px;
            color: #1a3646;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .contact-head p {
            margin: 0;
            font-size: 11px;
            color: #7c6b52;
        }

        table.contact-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table.contact-info-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e6dcc8;
            font-size: 11px;
            vertical-align: middle;
        }

        table.contact-info-table td.c-label {
            width: 140px;
            font-weight: bold;
            color: #1a3646;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.06em;
        }

        table.contact-info-table td.c-val {
            color: #374151;
            font-weight: 500;
        }

        .contact-footer-box {
            text-align: center;
            background: #f5efe3;
            border: 1px solid #c9b896;
            padding: 14px 12px;
            border-radius: 4px;
            margin-top: 14px;
        }

        .contact-footer-box h4 {
            margin: 0 0 4px 0;
            color: #1a3646;
            font-size: 12px;
            font-family: 'DejaVu Serif', serif;
        }

        .contact-footer-box p {
            margin: 0;
            font-size: 9.5px;
            color: #6b7280;
        }

        /* ----- Ledger paper texture feel ----- */
        .page-shell {
            border: 1px solid #d4c4a8;
            background: #faf8f3;
            padding: 18px 16px;
            margin-bottom: 8px;
        }

        /* ----- Cover ----- */
        .cover-wrap {
            padding: 8mm 6mm 14mm;
            page-break-after: always;
        }

        .cover-frame {
            border: 3px double #8b7355;
            padding: 4mm;
            background: #fefdfb;
        }

        .cover-frame-inner {
            border: 1px solid #c9b896;
            padding: 14mm 12mm;
            text-align: center;
        }

        .cover-band {
            display: inline-block;
            padding: 6px 28px;
            border-top: 2px solid #b8860b;
            border-bottom: 2px solid #b8860b;
            margin: 14px 0 18px;
        }

        .cover-title-main {
            margin: 0;
            font-size: 26px;
            font-weight: bold;
            color: #1a3646;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .cover-title-sub {
            margin: 10px 0 0;
            font-size: 13px;
            color: #5c4d3d;
            font-style: italic;
        }

        .cover-meta {
            margin-top: 22px;
            width: 100%;
            border-top: 1px dashed #c9b896;
            padding-top: 14px;
        }

        table.cover-meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        table.cover-meta-table td {
            padding: 7px 4px;
            border-bottom: 1px dotted #e5dcc8;
            vertical-align: top;
        }

        table.cover-meta-table td.lbl {
            width: 118px;
            color: #7c6b52;
            font-weight: bold;
        }

        table.cover-meta-table td.val {
            color: #1f2937;
        }

        table.cover-meta-table tr:last-child td {
            border-bottom: none;
        }

        .cover-footer-note {
            margin-top: 28px;
            font-size: 9px;
            color: #9a8b73;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        /* ----- Section headings ----- */
        .section-head {
            margin: 0 0 10px 0;
            padding-bottom: 6px;
            border-bottom: 2px solid #1a3646;
            font-family: 'DejaVu Serif', serif;
            font-size: 15px;
            color: #1a3646;
            letter-spacing: 0.04em;
        }

        .section-head span {
            display: inline-block;
            padding-right: 12px;
            border-bottom: 3px solid #b8860b;
            margin-bottom: -2px;
            padding-bottom: 5px;
        }

        /* ----- Summary cards ----- */
        .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 14px;
        }

        .summary-grid td {
            width: 33%;
            vertical-align: top;
            padding: 8px;
            border: 1px solid #ddd4c4;
            background: #fffefb;
        }

        .summary-kicker {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: #8a7960;
            margin-bottom: 4px;
        }

        .summary-num {
            font-size: 15px;
            font-weight: bold;
            color: #1a3646;
            font-family: 'DejaVu Serif', serif;
        }

        .summary-note {
            font-size: 8.5px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* ----- Tables (ledger style) ----- */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 14px;
            border: 2px solid #1a3646;
            background: #fffefb;
        }

        table.data-table thead tr {
            background: #1a3646;
            color: #faf7ef;
        }

        table.data-table thead th .inr {
            color: #faf7ef;
        }

        table.data-table th {
            padding: 8px 7px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            text-align: left;
            border: 1px solid #152a38;
            font-weight: bold;
        }

        table.data-table td {
            padding: 7px;
            border: 1px solid #e6dcc8;
            vertical-align: top;
            font-size: 10px;
        }

        table.data-table tbody tr:nth-child(even) {
            background: #faf8f3;
        }

        table.data-table tbody tr:nth-child(odd) {
            background: #fffefb;
        }

        table.data-table .total-row td {
            background: #f5efe3 !important;
            font-weight: bold;
            border-top: 2px solid #b8860b;
            color: #1a3646;
        }

        table.compact-notes th,
        table.compact-notes td {
            text-align: center;
        }

        table.compact-notes td:first-child {
            text-align: left;
            font-weight: bold;
            color: #4b5563;
        }

        .muted {
            color: #9ca3af;
            font-style: italic;
        }

        .page-break {
            page-break-before: always;
        }

        .mini-heading {
            font-size: 11px;
            font-weight: bold;
            color: #5c4d3d;
            margin: 14px 0 6px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .hr-decor {
            height: 0;
            border: none;
            border-top: 1px solid #e8dcc8;
            margin: 12px 0;
        }
    </style>
</head>
<body>
    <?php
        $totalCollected = $cash->sum('amount') + $cover->sum('amount');
        $cashTotal  = $cash->sum('amount');
        $coverTotal = $cover->sum('amount');
        $giftCount  = $gift->count();
        $gpayTotal  = $gpay->sum('amount');
        $gpayCount  = $gpay->count();

        $celebrationInr  = (float) config('packs.celebration.amount_inr', 300);
        $guestPayInr     = (float) config('packs.guest_pay_single.amount_inr', 400);
        $hostDuoInr      = (float) config('packs.ledger_duo.amount_inr', 500);
        $premiumInr      = (float) config('packs.premium_bundle.amount_inr', 700);
        $dgUnlockInr     = (float) config('services.direct_gpay_unlock.amount', 400);
        $pdfContactEmail = trim((string) config('chandlabook.support_email', ''));
        if ($pdfContactEmail === '') {
            $pdfContactEmail = (string) config('mail.from.address', '');
        }
        $pdfContactPhone = trim((string) config('chandlabook.support_phone', ''));
        $pdfSiteHost     = parse_url((string) config('app.url'), PHP_URL_HOST);
    ?>

    <?php if($hasLogo): ?>
        <div class="pdf-fixed-logo">
            <img src="<?php echo e($logoPath); ?>" alt="Chandla Book">
        </div>
    <?php endif; ?>

    <!-- Cover -->
    <div class="cover-wrap">
        <div class="cover-frame">
            <div class="cover-frame-inner serif-title">
                <div class="cover-band">
                    <p class="cover-title-main">Chandla register</p>
                </div>
                <p class="cover-title-sub">Consolidated multi-event collection ledger</p>

                <div class="cover-meta">
                <table class="cover-meta-table">
                    <tr>
                        <td class="lbl">Ledger for</td>
                        <td class="val"><?php echo e($user->name); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">Entries recorded</td>
                        <td class="val"><?php echo e($cash->count() + $gpay->count() + $cover->count() + $gift->count()); ?> total rows</td>
                    </tr>
                    <tr>
                        <td class="lbl">Prepared</td>
                        <td class="val"><?php echo e(now()->timezone(config('app.timezone'))->format('M j, Y · g:i A')); ?></td>
                    </tr>
                </table>
                </div>
                <p class="cover-footer-note">Chandla Book · Confidential ledger extract</p>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="page-shell">
        <h2 class="section-head serif-title"><span>Financial summary</span></h2>
        <table class="summary-grid">
            <tr>
                <td colspan="3" style="text-align: center;">
                    <div class="summary-kicker">Total collected</div>
                    <div class="summary-num"><span class="inr">&#8377; <?php echo e(number_format($totalCollected, 2)); ?></span></div>
                    <div class="summary-note">Cash + cover amounts</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-kicker">Cash (chandla)</div>
                    <div class="summary-num"><span class="inr">&#8377; <?php echo e(number_format($cashTotal, 2)); ?></span></div>
                    <div class="summary-note"><?php echo e($cash->count()); ?> entries</div>
                </td>
                <td>
                    <div class="summary-kicker">Cover</div>
                    <div class="summary-num"><span class="inr">&#8377; <?php echo e(number_format($coverTotal, 2)); ?></span></div>
                    <div class="summary-note"><?php echo e($cover->count()); ?> entries</div>
                </td>
                <td>
                    <div class="summary-kicker">Gifts listed</div>
                    <div class="summary-num"><?php echo e($giftCount); ?></div>
                    <div class="summary-note">Gift rows in register</div>
                </td>
            </tr>
            <tr>
                <td style="background:#f0fdf4;border:1px solid #6ee7b7;">
                    <div class="summary-kicker" style="color:#065f46;">GPay transactions</div>
                    <div class="summary-num" style="color:#065f46;"><?php echo e($gpayCount); ?></div>
                    <div class="summary-note">UPI / GPay entries</div>
                </td>
                <td colspan="2" style="background:#f0fdf4;border:1px solid #6ee7b7;">
                    <div class="summary-kicker" style="color:#065f46;">GPay total received</div>
                    <div class="summary-num" style="color:#065f46;"><span class="inr">&#8377; <?php echo e(number_format($gpayTotal, 2)); ?></span></div>
                    <div class="summary-note">Sum of all GPay / UPI amounts</div>
                </td>
            </tr>
        </table>


    </div>

    <div class="page-break"></div>

    <!-- GPay Transactions -->
    <div class="page-shell" style="border-color:#6ee7b7;">
        <h2 class="section-head serif-title" style="border-bottom-color:#065f46;color:#065f46;">
            <span style="border-bottom-color:#10b981;">GPay / UPI Transactions</span>
        </h2>
        <p style="margin:0 0 10px;font-size:9px;color:#065f46;">
            Alphabetical order &middot; Payments received via Google Pay / UPI
        </p>
        <table class="data-table" style="border-color:#065f46;">
            <thead>
                <tr style="background:#065f46;">
                    <th style="width:20%;border-color:#044f38;">Name</th>
                    <th style="width:22%;border-color:#044f38;">Address</th>
                    <th style="width:14%;border-color:#044f38;">Phone</th>
                    <th style="width:16%;border-color:#044f38;">Event</th>
                    <th style="width:16%;border-color:#044f38;">Txn ID</th>
                    <th style="width:12%;border-color:#044f38;">Amount (<span class="inr">&#8377;</span>)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $gpay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($row->giver_name); ?></td>
                        <td><?php echo e($row->giver_address ?: '—'); ?></td>
                        <td><?php echo e($row->giver_phone ?: '—'); ?></td>
                        <td><?php echo e($row->event->title ?? '—'); ?></td>
                        <td style="font-size:8.5px;color:#065f46;"><?php echo e($row->gpay_transaction_id ?: '—'); ?></td>
                        <td><span class="inr" style="color:#065f46;font-weight:bold;">&#8377; <?php echo e(number_format($row->amount, 2)); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="muted">No GPay / UPI entries in this ledger.</td>
                    </tr>
                <?php endif; ?>
                <?php if($gpay->count() > 0): ?>
                    <tr class="total-row" style="background:#d1fae5 !important;">
                        <td colspan="5" style="color:#065f46;">GPay section total &mdash; <?php echo e($gpayCount); ?> transactions</td>
                        <td><span class="inr" style="color:#065f46;">&#8377; <?php echo e(number_format($gpayTotal, 2)); ?></span></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Cash -->
    <div class="page-shell">
        <h2 class="section-head serif-title"><span>Cash contributions</span></h2>
        <p style="margin: 0 0 10px; font-size: 9px; color: #6b7280;">Alphabetical order · Names as entered in ledger</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:22%">Name</th>
                    <th style="width:28%">Address</th>
                    <th style="width:16%">Phone</th>
                    <th style="width:16%">Event</th>
                    <th style="width:18%">Amount (<span class="inr">&#8377;</span>)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $cash; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($row->giver_name); ?></td>
                        <td><?php echo e($row->giver_address ?: '—'); ?></td>
                        <td><?php echo e($row->giver_phone ?: '—'); ?></td>
                        <td><?php echo e($row->event->title ?? '—'); ?></td>
                        <td><span class="inr">&#8377; <?php echo e(number_format($row->amount, 2)); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="muted">No cash entries in this ledger.</td>
                    </tr>
                <?php endif; ?>
                <?php if($cash->count() > 0): ?>
                    <tr class="total-row">
                        <td colspan="4">Section total</td>
                        <td><span class="inr">&#8377; <?php echo e(number_format($cashTotal, 2)); ?></span></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Cover -->
    <div class="page-shell">
        <h2 class="section-head serif-title"><span>Cover amounts</span></h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:22%">Name</th>
                    <th style="width:28%">Address</th>
                    <th style="width:16%">Phone</th>
                    <th style="width:16%">Event</th>
                    <th style="width:18%">Amount (<span class="inr">&#8377;</span>)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $cover; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($row->giver_name); ?></td>
                        <td><?php echo e($row->giver_address ?: '—'); ?></td>
                        <td><?php echo e($row->giver_phone ?: '—'); ?></td>
                        <td><?php echo e($row->event->title ?? '—'); ?></td>
                        <td><span class="inr">&#8377; <?php echo e(number_format($row->amount, 2)); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="muted">No cover entries in this ledger.</td>
                    </tr>
                <?php endif; ?>
                <?php if($cover->count() > 0): ?>
                    <tr class="total-row">
                        <td colspan="4">Section total</td>
                        <td><span class="inr">&#8377; <?php echo e(number_format($coverTotal, 2)); ?></span></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Gifts -->
    <div class="page-shell">
        <h2 class="section-head serif-title"><span>Gift register</span></h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:22%">Name</th>
                    <th style="width:28%">Address</th>
                    <th style="width:16%">Phone</th>
                    <th style="width:16%">Event</th>
                    <th style="width:18%">Gift item</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $gift; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($row->giver_name); ?></td>
                        <td><?php echo e($row->giver_address ?: '—'); ?></td>
                        <td><?php echo e($row->giver_phone ?: '—'); ?></td>
                        <td><?php echo e($row->event->title ?? '—'); ?></td>
                        <td><?php echo e($row->gift_item_name ?: '—'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="muted">No gift entries in this ledger.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <hr class="hr-decor">
        <p style="font-size: 9px; color: #9ca3af; text-align: center; margin: 8px 0 0;">
            End of register · Chandla Book · <?php echo e(config('app.name', 'Chandla Book')); ?>

        </p>
    </div>

    <!-- ==================== PAGE 2: OUR PLANS (1 of 2) ==================== -->
    <div class="plans-wrap">
        <div class="plans-header">
            <h2>Chandla Book — Our Membership Plans</h2>
            <p>Digitalize your weddings, family functions, and community events with ease &amp; full security</p>
        </div>

        <!-- Row 1: Starter + Celebration -->
        <table class="plans-grid">
            <tr>
                <td class="plan-card">
                    <span class="plan-badge">Free</span>
                    <div class="plan-name">Starter Plan</div>
                    <div class="plan-price">Rs. 0 <span class="period">/ free forever</span></div>
                    <div class="plan-desc">Ideal for small family functions and basic ledger tracking.</div>
                    <ul class="plan-features">
                        <li>1 Event Limit</li>
                        <li>Up to 50 Gift / Chandla Entries</li>
                        <li>Cash &amp; Cover Tracking</li>
                        <li>Standard PDF Export</li>
                        <li>3 Family Viewers (Read Only)</li>
                    </ul>
                </td>

                <td class="plan-card">
                    <span class="plan-badge">Best Value</span>
                    <div class="plan-name">Celebration Pack</div>
                    <div class="plan-price">Rs. 300 <span class="period">/ one-time</span></div>
                    <div class="plan-desc">Enhance your celebration with printable invitations and graphic studio assets.</div>
                    <ul class="plan-features">
                        <li>Marriage Invitation Templates</li>
                        <li>Printable Invitation Designs</li>
                        <li>Pre-Wedding Video Creator</li>
                        <li>Event Countdown Studio</li>
                    </ul>
                </td>
            </tr>
        </table>

        <!-- Row 2: Guest Contribution + Host Plus -->
        <table class="plans-grid">
            <tr>
                <td class="plan-card featured">
                    <span class="plan-badge">Recommended</span>
                    <div class="plan-name">Guest Contribution</div>
                    <div class="plan-price">Rs. 400 <span class="period">/ one-time</span></div>
                    <div class="plan-desc">Direct payment collections and unlimited ledger entries for your single event.</div>
                    <ul class="plan-features">
                        <li>Personal UPI / QR Collection</li>
                        <li><strong>Unlimited Entries</strong> (Single Event)</li>
                        <li>Guest Payment Tracking &amp; Log</li>
                        <li>Detailed Full Event PDF Export</li>
                    </ul>
                </td>

                <td class="plan-card">
                    <span class="plan-badge">Great Value</span>
                    <div class="plan-name">Host Plus Plan</div>
                    <div class="plan-price">Rs. 500 <span class="period">/ one-time</span></div>
                    <div class="plan-desc">Manage multiple events with unlimited ledger entries &amp; hosting tools.</div>
                    <ul class="plan-features">
                        <li>Up to <strong>2 Events</strong></li>
                        <li><strong>Unlimited Entries</strong> (All Events)</li>
                        <li>Personal UPI / QR Payment Collection</li>
                        <li>Full PDF Ledger Downloads</li>
                    </ul>
                </td>
            </tr>
        </table>
    </div>

    <!-- ==================== PAGE 3: OUR PLANS (2 of 2) ==================== -->
    <div class="plans-wrap">
        <div class="plans-header">
            <h2>Chandla Book — Plans (Continued)</h2>
            <p>Higher tier plans with advanced features, family editors, and enterprise-level support</p>
        </div>

        <!-- Row 3: Family + Premium Host -->
        <table class="plans-grid">
            <tr>
                <td class="plan-card">
                    <span class="plan-badge">Family Pick</span>
                    <div class="plan-name">Family Plan</div>
                    <div class="plan-price">Rs. 600 <span class="period">/ family pack</span></div>
                    <div class="plan-desc">Coordinate family functions with multi-editor read/write access.</div>
                    <ul class="plan-features">
                        <li>3 Family Editors (Write Access)</li>
                        <li>Shared Event Management Space</li>
                        <li>Role-Based Team Permissions</li>
                        <li>Everything in Host Plus Plan</li>
                    </ul>
                </td>

                <td class="plan-card featured">
                    <span class="plan-badge">Most Popular</span>
                    <div class="plan-name">Premium Host</div>
                    <div class="plan-price">Rs. 700 <span class="period">/ complete bundle</span></div>
                    <div class="plan-desc">Our flagship plan with custom invitation templates, reel studio &amp; support.</div>
                    <ul class="plan-features">
                        <li>Up to <strong>3 Events</strong> &amp; Unlimited Entries</li>
                        <li>Premium Invitation &amp; Video Templates</li>
                        <li>Priority WhatsApp &amp; Phone Support</li>
                        <li>Full Data Export &amp; Custom Reports</li>
                    </ul>
                </td>
            </tr>
        </table>

        <!-- Row 4: Professional + Enterprise -->
        <table class="plans-grid">
            <tr>
                <td class="plan-card">
                    <span class="plan-badge">Professional</span>
                    <div class="plan-name">Professional</div>
                    <div class="plan-price">Rs. 999 <span class="period">/ one-time</span></div>
                    <div class="plan-desc">For power users and professional coordinators running multiple large events.</div>
                    <ul class="plan-features">
                        <li>Up to <strong>10 Events</strong></li>
                        <li>Unlimited Family Editors</li>
                        <li>Custom Branding &amp; Reports</li>
                        <li>Priority Support Channel</li>
                    </ul>
                </td>

                <td class="plan-card featured">
                    <span class="plan-badge">Enterprise</span>
                    <div class="plan-name">Enterprise</div>
                    <div class="plan-price">Custom <span class="period">/ contact sales</span></div>
                    <div class="plan-desc">Bespoke integration, white labeling, and dedicated hosting for large organizations.</div>
                    <ul class="plan-features">
                        <li>Unlimited Events &amp; Entries</li>
                        <li>White Label / Custom Branding</li>
                        <li>Dedicated Account Manager</li>
                        <li>SLA &amp; Priority Infrastructure</li>
                    </ul>
                </td>
            </tr>
        </table>

        <div class="plans-footer-box">
            <h4>Why Choose Chandla Book?</h4>
            <p>Direct UPI Collections &bull; Multi-Editor Family Access &bull; Automated WhatsApp Notifications &bull; Instant PDF Ledger Exports</p>
        </div>
    </div>

    <!-- ==================== LAST PAGE: CONTACT US ==================== -->
    <div class="contact-wrap">
        <div class="contact-card">
            <div class="contact-head">
                <h2>Contact Us — Chandla Book</h2>
                <p>Smart Event, Function & Digital Collection Management</p>
            </div>

            <table class="contact-info-table">
                <tr>
                    <td class="c-label">Platform Name</td>
                    <td class="c-val"><strong>Chandla Book</strong> (by Skylight Technologies)</td>
                </tr>
                <tr>
                    <td class="c-label">Official Website</td>
                    <td class="c-val"><a href="https://chandlabook.in" style="color:#b8860b; text-decoration:none; font-weight:bold;">https://chandlabook.in</a></td>
                </tr>
                <tr>
                    <td class="c-label">Customer Support Phone</td>
                    <td class="c-val">+91 78619 76671 (WhatsApp &amp; Voice Call)</td>
                </tr>
                <tr>
                    <td class="c-label">Alternative Phone</td>
                    <td class="c-val">+91 8200067737</td>
                </tr>
                <tr>
                    <td class="c-label">Support Email</td>
                    <td class="c-val">chandlabook@gmail.com / support@chandlabook.in</td>
                </tr>
                <tr>
                    <td class="c-label">Alternative Email</td>
                    <td class="c-val">info.ksky@gmail.com</td>
                </tr>
                <tr>
                    <td class="c-label">Mobile App Availability</td>
                    <td class="c-val">Available on <strong>Android Play Store</strong> &amp; <strong>Apple App Store</strong></td>
                </tr>
                <tr>
                    <td class="c-label">Company Address</td>
                    <td class="c-val">Skylight Technologies, Gujarat, India</td>
                </tr>
            </table>

            <div class="contact-footer-box">
                <h4>Thank You for Using Chandla Book!</h4>
                <p>For any queries, custom plan upgrades, or support, feel free to contact us via WhatsApp or Email.</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /home/chandlabook/public_html/resources/views/client/chandlas/ledger-pdf.blade.php ENDPATH**/ ?>
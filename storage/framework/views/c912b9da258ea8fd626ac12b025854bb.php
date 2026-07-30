<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta charset="utf-8">
    <title>Chandla register — <?php echo e($event->title); ?></title>
    <?php
        $pdfFontUrl = null;
        if (!empty($gujaratiFontPath) && file_exists($gujaratiFontPath)) {
            $pdfFontUrl = 'file://' . str_replace('\\', '/', $gujaratiFontPath);
        }
        $logoPath = public_path('images/chandla-logo.png');
        $hasLogo = file_exists($logoPath);
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
            margin: 26mm 11mm 22mm 11mm;
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

        /* ₹ U+20B9 — must use DejaVu Sans (bundled in DomPDF); Gujarati fonts omit this glyph */
        .inr {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .serif-title {
            font-family: 'DejaVu Serif', serif;
            letter-spacing: 0.02em;
        }

        /* Running header — DomPDF repeats fixed blocks on each page */
        .pdf-fixed-logo {
            position: fixed;
            top: 6mm;
            right: 10mm;
            z-index: 1000;
            text-align: right;
        }

        .pdf-fixed-logo img {
            height: 34px;
            width: auto;
            display: block;
        }

        /* ----- Promo / last page ----- */
        .promo-page {
            border: 2px double #8b7355;
            background: #fefdfb;
            padding: 12mm 10mm;
            page-break-before: always;
        }

        .promo-page h2 {
            margin: 0 0 8px;
            font-family: 'DejaVu Serif', serif;
            font-size: 17px;
            color: #1a3646;
            letter-spacing: 0.03em;
            border-bottom: 2px solid #b8860b;
            padding-bottom: 8px;
        }

        .promo-lead {
            font-size: 10.5px;
            color: #4b5563;
            line-height: 1.55;
            margin: 0 0 14px;
        }

        table.promo-contact {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 14px;
            border: 1px solid #ddd4c4;
            background: #fffefb;
        }

        table.promo-contact td {
            padding: 8px 10px;
            font-size: 10px;
            border-bottom: 1px dotted #e5dcc8;
            vertical-align: top;
        }

        table.promo-contact td.pt {
            width: 92px;
            font-weight: bold;
            color: #7c6b52;
        }

        table.promo-plans {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            border: 2px solid #1a3646;
            font-size: 9.5px;
        }

        table.promo-plans th {
            background: #1a3646;
            color: #faf7ef;
            padding: 7px;
            text-align: left;
            border: 1px solid #152a38;
            font-weight: bold;
        }

        table.promo-plans td {
            padding: 7px;
            border: 1px solid #e6dcc8;
            vertical-align: top;
            background: #fffefb;
        }

        table.promo-plans tr:nth-child(even) td {
            background: #faf8f3;
        }

        .promo-services {
            margin: 12px 0 0;
            padding: 10px 12px;
            border: 1px dashed #c9b896;
            background: #faf8f3;
            font-size: 9.5px;
            line-height: 1.55;
            color: #374151;
        }

        .promo-services strong {
            color: #1a3646;
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
        $notesOnHand = [
            500 => (int) ($inventory->note_500 ?? 0),
            200 => (int) ($inventory->note_200 ?? 0),
            100 => (int) ($inventory->note_100 ?? 0),
            50  => (int) ($inventory->note_50  ?? 0),
            20  => (int) ($inventory->note_20  ?? 0),
            10  => (int) ($inventory->note_10  ?? 0),
            5   => (int) ($inventory->note_5   ?? 0),
            2   => (int) ($inventory->note_2   ?? 0),
            1   => (int) ($inventory->note_1   ?? 0),
        ];
        $totalNotesCount = array_sum($notesOnHand);
        $totalCashOnHand = 0;
        foreach ($notesOnHand as $denomination => $count) {
            $totalCashOnHand += $denomination * $count;
        }
        $totalCollected = $event->chandlas->sum('amount');
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
                <p class="cover-title-sub">Traditional collection ledger — event summary</p>

                <div class="cover-meta">
                <table class="cover-meta-table">
                    <tr>
                        <td class="lbl">Event</td>
                        <td class="val"><?php echo e($event->title); ?></td>
                    </tr>
                    <tr>
                        <td class="lbl">Event date</td>
                        <td class="val"><?php echo e($event->event_date->format('l, F j, Y')); ?></td>
                    </tr>
                    <?php if($event->event_time): ?>
                        <tr>
                            <td class="lbl">Time</td>
                            <td class="val"><?php echo e($event->event_time->format('h:i A')); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if($event->venue): ?>
                        <tr>
                            <td class="lbl">Venue</td>
                            <td class="val"><?php echo e($event->venue); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="lbl">Entries recorded</td>
                        <td class="val"><?php echo e($event->chandlas->count()); ?> total rows</td>
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
                <td>
                    <div class="summary-kicker">Total collected</div>
                    <div class="summary-num"><span class="inr">&#8377; <?php echo e(number_format($totalCollected, 2)); ?></span></div>
                    <div class="summary-note">Cash + cover amounts</div>
                </td>
                <td>
                    <div class="summary-kicker">Notes on hand</div>
                    <div class="summary-num"><?php echo e(number_format($totalNotesCount)); ?></div>
                    <div class="summary-note">Pieces across denominations</div>
                </td>
                <td>
                    <div class="summary-kicker">Cash on hand</div>
                    <div class="summary-num"><span class="inr">&#8377; <?php echo e(number_format($totalCashOnHand, 2)); ?></span></div>
                    <div class="summary-note">Per inventory tally</div>
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

        <p class="mini-heading">Denomination-wise cash on hand</p>
        <table class="data-table compact-notes">
            <thead>
                <tr>
                    <th style="width:35%">Denomination</th>
                    <th style="width:22%">Qty</th>
                    <th style="width:43%">Value (<span class="inr">&#8377;</span>)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $notesOnHand; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $denomination => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><span class="inr">&#8377; <?php echo e(number_format($denomination)); ?></span></td>
                        <td><?php echo e(number_format($count)); ?></td>
                        <td><span class="inr">&#8377; <?php echo e(number_format($denomination * $count, 2)); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr class="total-row">
                    <td colspan="2">Total cash on hand</td>
                    <td><span class="inr">&#8377; <?php echo e(number_format($totalCashOnHand, 2)); ?></span></td>
                </tr>
            </tbody>
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
                    <th style="width:24%;border-color:#044f38;">Name</th>
                    <th style="width:28%;border-color:#044f38;">Address</th>
                    <th style="width:16%;border-color:#044f38;">Phone</th>
                    <th style="width:20%;border-color:#044f38;">Txn ID</th>
                    <th style="width:12%;border-color:#044f38;">Amount (<span class="inr">&#8377;</span>)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $gpay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($row->giver_name); ?></td>
                        <td><?php echo e($row->giver_address ?: '—'); ?></td>
                        <td><?php echo e($row->giver_phone ?: '—'); ?></td>
                        <td style="font-size:8.5px;color:#065f46;"><?php echo e($row->gpay_transaction_id ?: '—'); ?></td>
                        <td><span class="inr" style="color:#065f46;font-weight:bold;">&#8377; <?php echo e(number_format($row->amount, 2)); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="muted">No GPay / UPI entries for this event.</td>
                    </tr>
                <?php endif; ?>
                <?php if($gpay->count() > 0): ?>
                    <tr class="total-row" style="background:#d1fae5 !important;">
                        <td colspan="4" style="color:#065f46;">GPay section total &mdash; <?php echo e($gpayCount); ?> transactions</td>
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
                    <th style="width:26%">Name</th>
                    <th style="width:34%">Address</th>
                    <th style="width:18%">Phone</th>
                    <th style="width:22%">Amount (<span class="inr">&#8377;</span>)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $cash; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($row->giver_name); ?></td>
                        <td><?php echo e($row->giver_address ?: '—'); ?></td>
                        <td><?php echo e($row->giver_phone ?: '—'); ?></td>
                        <td><span class="inr">&#8377; <?php echo e(number_format($row->amount, 2)); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="muted">No cash entries for this event.</td>
                    </tr>
                <?php endif; ?>
                <?php if($cash->count() > 0): ?>
                    <tr class="total-row">
                        <td colspan="3">Section total</td>
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
                    <th style="width:26%">Name</th>
                    <th style="width:34%">Address</th>
                    <th style="width:18%">Phone</th>
                    <th style="width:22%">Amount (<span class="inr">&#8377;</span>)</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $cover; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($row->giver_name); ?></td>
                        <td><?php echo e($row->giver_address ?: '—'); ?></td>
                        <td><?php echo e($row->giver_phone ?: '—'); ?></td>
                        <td><span class="inr">&#8377; <?php echo e(number_format($row->amount, 2)); ?></span></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="muted">No cover entries for this event.</td>
                    </tr>
                <?php endif; ?>
                <?php if($cover->count() > 0): ?>
                    <tr class="total-row">
                        <td colspan="3">Section total</td>
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
                    <th style="width:26%">Name</th>
                    <th style="width:34%">Address</th>
                    <th style="width:22%">Phone</th>
                    <th style="width:18%">Gift item</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $gift; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($row->giver_name); ?></td>
                        <td><?php echo e($row->giver_address ?: '—'); ?></td>
                        <td><?php echo e($row->giver_phone ?: '—'); ?></td>
                        <td><?php echo e($row->gift_item_name ?: '—'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="muted">No gift entries for this event.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <hr class="hr-decor">
        <p style="font-size: 9px; color: #9ca3af; text-align: center; margin: 8px 0 0;">
            End of register · Chandla Book · <?php echo e(config('app.name', 'Chandla Book')); ?>

        </p>
    </div>

    <div class="promo-page">
        <h2 class="serif-title">Chandla Book — Plans, services &amp; contact</h2>
        <p class="promo-lead">
            Chandla Book is built for Indian weddings and community occasions: record cash by note,
            cover and gifts in one ledger, attach UPI proofs, manage inventory, share payment QR codes with guests,
            and export registers like this PDF for your records.
        </p>

        <?php if($pdfContactEmail !== '' || $pdfContactPhone !== '' || !empty($pdfSiteHost)): ?>
            <table class="promo-contact">
                <?php if($pdfContactEmail !== ''): ?>
                    <tr>
                        <td class="pt">Email</td>
                        <td><?php echo e($pdfContactEmail); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if($pdfContactPhone !== ''): ?>
                    <tr>
                        <td class="pt">Phone</td>
                        <td><?php echo e($pdfContactPhone); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if(!empty($pdfSiteHost)): ?>
                    <tr>
                        <td class="pt">Website</td>
                        <td><?php echo e(rtrim(config('app.url'), '/')); ?></td>
                    </tr>
                <?php endif; ?>
            </table>
        <?php else: ?>
            <p style="font-size:9.5px;color:#6b7280;margin:0 0 14px;">
                Configure <strong>CHANDLABOOK_SUPPORT_EMAIL</strong> or <strong>CHANDLABOOK_SUPPORT_PHONE</strong> in your environment to show contact lines here (website uses <strong>APP_URL</strong>).
            </p>
        <?php endif; ?>

        <p style="margin:0 0 6px;font-size:10px;font-weight:bold;color:#1a3646;text-transform:uppercase;letter-spacing:0.06em;">Popular packs (indicative pricing)</p>
        <table class="promo-plans">
            <thead>
                <tr>
                    <th style="width:24%">Pack / plan</th>
                    <th style="width:14%">From</th>
                    <th style="width:62%">What you get</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Celebration</strong></td>
                    <td><span class="inr">&#8377;</span> <?php echo e(number_format($celebrationInr, 0)); ?></td>
                    <td>Invitation layouts, celebration video workflow &amp; pre-wedding PNG studio — checkout after sign-in.</td>
                </tr>
                <tr>
                    <td><strong>Guest Contribution</strong></td>
                    <td><span class="inr">&#8377;</span> <?php echo e(number_format($guestPayInr, 0)); ?></td>
                    <td>Credit for one event: Direct GPay to your UPI, unlimited chandla on that event, full PDF exports.</td>
                </tr>
                <tr>
                    <td><strong>Host Plus Plan</strong></td>
                    <td><span class="inr">&#8377;</span> <?php echo e(number_format($hostDuoInr, 0)); ?></td>
                    <td>Ledger bundle covering two events — ideal when you host multiple functions.</td>
                </tr>
                <tr>
                    <td><strong>Premium Host Plan</strong></td>
                    <td><span class="inr">&#8377;</span> <?php echo e(number_format($premiumInr, 0)); ?></td>
                    <td>Bundled invitations plus multi-event perks — see in-app checkout for full detail.</td>
                </tr>
                <tr>
                    <td><strong>Direct GPay QR</strong></td>
                    <td><span class="inr">&#8377;</span> <?php echo e(number_format($dgUnlockInr, 0)); ?></td>
                    <td>Optional per-event unlock so guests pay your UPI from the event flow (subject to verification).</td>
                </tr>
            </tbody>
        </table>

        <div class="promo-services">
            <strong>Services inside the app:</strong>
            Events &amp; ledger · Contacts · Marriage invitations · Pre-wedding studio · Guest collections · Cash inventory · GPay proofs · PDF registers · Reports for admins.
        </div>
        <p style="margin:14px 0 0;font-size:8.5px;color:#9ca3af;text-align:center;line-height:1.45;">
            Figures shown are typical list prices — sign in for live Razorpay checkout and current offers.<br>
            Chandla Book · <?php echo e(config('app.name', 'Chandla Book')); ?>

        </p>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/chandlas/pdf.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/chandla-favicon.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/chandla-app-icon.png')); ?>">
    <meta charset="utf-8">
    <title>Ganpati Chanda Register — <?php echo e($event->title); ?></title>
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

        .inr {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .serif-title {
            font-family: 'DejaVu Serif', serif;
            letter-spacing: 0.02em;
        }

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

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <?php
        $totalCollected = $entries->sum('amount');
        $cashTotal  = $cash->sum('amount');
        $gpayTotal  = $gpay->sum('amount');
        $otherTotal = $other->sum('amount');
        $gpayCount  = $gpay->count();
        $otherCount = $other->count();
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
                    <p class="cover-title-main">Ganpati Chanda Register</p>
                </div>
                <p class="cover-title-sub">Traditional collection ledger — event summary</p>

                <div class="cover-meta">
                <table class="cover-meta-table">
                    <tr>
                        <td class="lbl">Event</td>
                        <td class="val"><?php echo e($event->title); ?></td>
                    </tr>
                    <?php if($event->event_date): ?>
                    <tr>
                        <td class="lbl">Event date</td>
                        <td class="val"><?php echo e($event->event_date->format('l, F j, Y')); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($event->venue): ?>
                    <tr>
                        <td class="lbl">Venue</td>
                        <td class="val"><?php echo e($event->venue); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="lbl">Total Collected</td>
                        <td class="val" style="font-size:14px; font-weight:bold; color:#1a3646;">
                            <span class="inr">₹</span> <?php echo e(number_format($totalCollected, 0)); ?>

                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Total Entries</td>
                        <td class="val"><?php echo e($entries->count()); ?> records</td>
                    </tr>
                    <tr>
                        <td class="lbl">Generated on</td>
                        <td class="val"><?php echo e(now()->format('d M Y, h:i A')); ?></td>
                    </tr>
                </table>
                </div>

                <div class="cover-footer-note">
                    Confidential &bull; Keep safe<br>
                    Generated securely via Chandla Book
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="page-shell">
        <h3 class="section-head"><span>Collection Summary</span></h3>
        <table class="summary-grid">
            <tr>
                <td>
                    <div class="summary-kicker">Cash Collected</div>
                    <div class="summary-num"><span class="inr">₹</span><?php echo e(number_format($cashTotal, 0)); ?></div>
                </td>
                <td>
                    <div class="summary-kicker">Digital (GPay)</div>
                    <div class="summary-num"><span class="inr">₹</span><?php echo e(number_format($gpayTotal, 0)); ?></div>
                    <div class="summary-note">(<?php echo e($gpayCount); ?> transactions)</div>
                </td>
                <td>
                    <div class="summary-kicker">Other Methods</div>
                    <div class="summary-num"><span class="inr">₹</span><?php echo e(number_format($otherTotal, 0)); ?></div>
                    <div class="summary-note">(<?php echo e($otherCount); ?> entries)</div>
                </td>
            </tr>
        </table>
    </div>

    <?php if($entries->isEmpty()): ?>
    <div class="page-shell">
        <p style="text-align:center; padding:30px; color:#6b7280; font-style:italic;">No entries recorded yet.</p>
    </div>
    <?php else: ?>
    
    <!-- All Entries Table -->
    <div class="page-shell">
        <h3 class="section-head"><span>All Entries</span></h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:25px; text-align:center;">#</th>
                    <th>Donor Name</th>
                    <th>Phone</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th style="width:75px; text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="text-align:center; color:#9ca3af;"><?php echo e($i + 1); ?></td>
                    <td><strong><?php echo e($row->giver_name); ?></strong><br><span style="font-size:8.5px; color:#6b7280;"><?php echo e($row->giver_address); ?></span></td>
                    <td><?php echo e($row->giver_phone); ?></td>
                    <td><?php echo e(strtoupper($row->payment_method ?? 'Other')); ?></td>
                    <td><?php echo e(optional($row->received_date)->format('d/m/Y')); ?></td>
                    <td style="text-align:right; font-weight:bold;"><?php echo e(number_format((float)$row->amount, 0)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr class="total-row">
                    <td colspan="5" style="text-align:right; font-size:10px;">GRAND TOTAL</td>
                    <td style="text-align:right;"><span class="inr">₹</span> <?php echo e(number_format($totalCollected, 0)); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

</body>
</html>
<?php /**PATH C:\Users\Chirag\Desktop\New folder\ChandlaBook\resources\views/client/ganpati/pdf.blade.php ENDPATH**/ ?>
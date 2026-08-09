<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">
    <meta charset="utf-8">
    <title>Ganpati Chanda Register — {{ $event->title }}</title>
    @php
        $pdfFontUrl = null;
        if (!empty($gujaratiFontPath) && file_exists($gujaratiFontPath)) {
            $pdfFontUrl = 'file://' . str_replace('\\', '/', $gujaratiFontPath);
        }
        $logoPath = null;
        foreach (['images/logo.jpeg', 'images/logo.png', 'images/chandla-logo.png', 'images/chandla-logo.jpg'] as $img) {
            if (file_exists(public_path($img))) {
                $logoPath = public_path($img);
                break;
            }
        }
        $hasLogo = !empty($logoPath);
    @endphp
    <style>
        @if($pdfFontUrl)
        @font-face {
            font-family: 'PdfGujarati';
            font-style: normal;
            font-weight: 400;
            src: url('{{ $pdfFontUrl }}') format('truetype');
        }
        @endif

        @page {
            margin: 24mm 11mm 20mm 11mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: {{ $pdfFontUrl ? "'PdfGujarati', 'DejaVu Sans'" : "'DejaVu Sans'" }}, sans-serif;
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
            top: 5mm;
            right: 10mm;
            z-index: 1000;
            text-align: right;
        }

        .pdf-fixed-logo img {
            height: 32px;
            width: auto;
            display: block;
        }

        .page-break {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }

        /* ----- Ledger paper texture feel ----- */
        .page-shell {
            border: 1px solid #d4c4a8;
            background: #faf8f3;
            padding: 16px 14px;
            margin-bottom: 8px;
        }

        /* ==================== PAGE 1: COVER ==================== */
        .cover-wrap {
            padding: 6mm 4mm 10mm;
            page-break-after: always;
        }

        .cover-frame {
            border: 3px double #8b7355;
            padding: 4mm;
            background: #fefdfb;
        }

        .cover-frame-inner {
            border: 1px solid #c9b896;
            padding: 12mm 10mm;
            text-align: center;
        }

        .cover-band {
            display: inline-block;
            padding: 6px 28px;
            border-top: 2px solid #b8860b;
            border-bottom: 2px solid #b8860b;
            margin: 12px 0 16px;
        }

        .cover-title-main {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #1a3646;
            text-transform: uppercase;
            letter-spacing: 0.10em;
        }

        .cover-title-sub {
            margin: 8px 0 0;
            font-size: 12.5px;
            color: #5c4d3d;
            font-style: italic;
        }

        .cover-meta {
            margin-top: 20px;
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
            width: 120px;
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
            margin-top: 24px;
            font-size: 9px;
            color: #9a8b73;
            letter-spacing: 0.06em;
            text-transform: uppercase;
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

        /* ==================== PAGE 3+: SUMMARY & ENTRIES ==================== */
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

        table.summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 14px;
        }

        table.summary-grid td {
            width: 33.33%;
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
    </style>
</head>
<body>
    @php
        $totalCollected = $entries->sum('amount');
        $cashTotal  = $cash->sum('amount');
        $gpayTotal  = $gpay->sum('amount');
        $otherTotal = $other->sum('amount');
        $gpayCount  = $gpay->count();
        $otherCount = $other->count();
    @endphp

    @if($hasLogo)
        <div class="pdf-fixed-logo">
            <img src="{{ $logoPath }}" alt="Chandla Book">
        </div>
    @endif

    <!-- ==================== PAGE 1: COVER ==================== -->
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
                        <td class="lbl">Event Title</td>
                        <td class="val"><strong>{{ $event->title }}</strong></td>
                    </tr>
                    @if($event->event_date)
                    <tr>
                        <td class="lbl">Event Date</td>
                        <td class="val">{{ $event->event_date->format('l, F j, Y') }}</td>
                    </tr>
                    @endif
                    @if($event->venue)
                    <tr>
                        <td class="lbl">Venue / Address</td>
                        <td class="val">{{ $event->venue }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="lbl">Total Collected</td>
                        <td class="val" style="font-size:14px; font-weight:bold; color:#1a3646;">
                            <span class="inr">₹</span> {{ number_format($totalCollected, 0) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Total Entries</td>
                        <td class="val">{{ $entries->count() }} records</td>
                    </tr>
                    <tr>
                        <td class="lbl">Report Generated</td>
                        <td class="val">{{ now()->format('d M Y, h:i A') }}</td>
                    </tr>
                </table>
                </div>

                <div class="cover-footer-note">
                    Confidential &bull; Keep safe<br>
                    Generated securely via Chandla Book Platform
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== PAGE 2: OUR PLANS ==================== -->
    <div class="plans-wrap">
        <div class="plans-header">
            <h2>Chandla Book — Our Membership Plans</h2>
            <p>Digitalize your weddings, family functions, and community events with ease & full security</p>
        </div>

        <!-- 2x2 Grid for Plans -->
        <table class="plans-grid">
            <tr>
                <td class="plan-card">
                    <span class="plan-badge">Free</span>
                    <div class="plan-name">Starter Plan</div>
                    <div class="plan-price">₹0 <span class="period">/ free forever</span></div>
                    <div class="plan-desc">Ideal for small family functions and basic ledger tracking.</div>
                    <ul class="plan-features">
                        <li>1 Event Limit</li>
                        <li>Up to 50 Gift / Chandla Entries</li>
                        <li>Cash & Cover Tracking</li>
                        <li>Standard PDF Export</li>
                    </ul>
                </td>

                <td class="plan-card featured">
                    <span class="plan-badge">Popular</span>
                    <div class="plan-name">Host Plus Plan</div>
                    <div class="plan-price">₹500 <span class="period">/ event pack</span></div>
                    <div class="plan-desc">Manage multiple events with unlimited ledger entries & hosting tools.</div>
                    <ul class="plan-features">
                        <li>Up to 2 Events</li>
                        <li><strong>Unlimited Entries</strong> (All Events)</li>
                        <li>Personal UPI / QR Payment Collection</li>
                        <li>Full PDF Ledger Downloads</li>
                    </ul>
                </td>
            </tr>

            <tr>
                <td class="plan-card">
                    <span class="plan-badge">Family Pick</span>
                    <div class="plan-name">Family Plan</div>
                    <div class="plan-price">₹600 <span class="period">/ family pack</span></div>
                    <div class="plan-desc">Coordinate family functions with multi-editor read/write access.</div>
                    <ul class="plan-features">
                        <li>3 Family Editors (Write Access)</li>
                        <li>Shared Event Management Space</li>
                        <li>Role-Based Team Permissions</li>
                        <li>Everything in Host Plus Plan</li>
                    </ul>
                </td>

                <td class="plan-card featured">
                    <span class="plan-badge">Flagship</span>
                    <div class="plan-name">Premium Host</div>
                    <div class="plan-price">₹700 <span class="period">/ complete bundle</span></div>
                    <div class="plan-desc">Our flagship plan with custom invitation templates, reel studio & support.</div>
                    <ul class="plan-features">
                        <li>Up to 3 Events & Unlimited Entries</li>
                        <li>Premium Invitation & Video Templates</li>
                        <li>Priority WhatsApp & Phone Support</li>
                        <li>Full Data Export & Custom Reports</li>
                    </ul>
                </td>
            </tr>
        </table>

        <div class="plans-footer-box">
            <h4>Why Choose Chandla Book?</h4>
            <p>Direct UPI Collections &bull; Multi-Editor Family Access &bull; Automated WhatsApp Notifications &bull; Instant PDF Ledger Exports</p>
        </div>
    </div>

    <!-- ==================== PAGE 3+: SUMMARY & ENTRIES ==================== -->
    <div class="page-shell">
        <h3 class="section-head"><span>Collection Summary</span></h3>
        <table class="summary-grid">
            <tr>
                <td>
                    <div class="summary-kicker">Cash Collected</div>
                    <div class="summary-num"><span class="inr">₹</span>{{ number_format($cashTotal, 0) }}</div>
                </td>
                <td>
                    <div class="summary-kicker">Digital (GPay / UPI)</div>
                    <div class="summary-num"><span class="inr">₹</span>{{ number_format($gpayTotal, 0) }}</div>
                    <div class="summary-note">({{ $gpayCount }} transactions)</div>
                </td>
                <td>
                    <div class="summary-kicker">Other Methods</div>
                    <div class="summary-num"><span class="inr">₹</span>{{ number_format($otherTotal, 0) }}</div>
                    <div class="summary-note">({{ $otherCount }} entries)</div>
                </td>
            </tr>
        </table>
    </div>

    @if($entries->isEmpty())
    <div class="page-shell">
        <p style="text-align:center; padding:30px; color:#6b7280; font-style:italic;">No entries recorded yet.</p>
    </div>
    @else
    
    <!-- All Entries Table -->
    <div class="page-shell">
        <h3 class="section-head"><span>All Collection Entries</span></h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:25px; text-align:center;">#</th>
                    <th>Donor Name & Address</th>
                    <th>Phone</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th style="width:75px; text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $i => $row)
                <tr>
                    <td style="text-align:center; color:#9ca3af;">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $row->giver_name }}</strong>
                        @if($row->giver_address)
                            <br><span style="font-size:8.5px; color:#6b7280;">{{ $row->giver_address }}</span>
                        @endif
                    </td>
                    <td>{{ $row->giver_phone ?? '-' }}</td>
                    <td>{{ strtoupper($row->payment_method ?? 'Other') }}</td>
                    <td>{{ optional($row->received_date)->format('d/m/Y') }}</td>
                    <td style="text-align:right; font-weight:bold;"><span class="inr">₹</span> {{ number_format((float)$row->amount, 0) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" style="text-align:right; font-size:10px;">GRAND TOTAL COLLECTED</td>
                    <td style="text-align:right;"><span class="inr">₹</span> {{ number_format($totalCollected, 0) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

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
                    <td class="c-val">+91 78619 76671 (WhatsApp & Voice Call)</td>
                </tr>
                <tr>
                    <td class="c-label">Support Email</td>
                    <td class="c-val">chandlabook@gmail.com / support@chandlabook.in</td>
                </tr>
                <tr>
                    <td class="c-label">Mobile App Availability</td>
                    <td class="c-val">Available on <strong>Android Play Store</strong> & <strong>Apple App Store</strong></td>
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

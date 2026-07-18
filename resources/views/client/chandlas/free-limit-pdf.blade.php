<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/chandla-app-icon.png') }}">
    <meta charset="utf-8">
    <title>Free Plan — First 50 Entries · Chandla Book</title>
    @php
        $logoPath = public_path('images/chandla-logo.png');
        $hasLogo = file_exists($logoPath);

        // Group entries by category for category-wise sections.
        $cash  = $entries->filter(fn($e) => $e->category === 'chandla')->values();
        $cover = $entries->filter(fn($e) => $e->category === 'cover')->values();
        $gift  = $entries->filter(fn($e) => $e->category === 'gift')->values();

        $cashTotal  = $cash->sum('amount');
        $coverTotal = $cover->sum('amount');
        $totalCollected = (float) $cash->sum('amount') + (float) $cover->sum('amount');

        $celebrationInr = (float) config('packs.celebration.amount_inr', 300);
        $hostDuoInr     = (float) config('packs.ledger_duo.amount_inr', 500);
        $familyInr      = (float) config('packs.family.amount_inr', 600);
        $premiumInr     = (float) config('packs.premium_bundle.amount_inr', 700);
    @endphp
    <style>
        @page {
            margin: 26mm 11mm 22mm 11mm;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            line-height: 1.45;
            color: #1f2937;
            background: #fdfbf7;
        }

        .inr { font-family: 'DejaVu Sans', sans-serif; }

        .serif-title {
            font-family: 'DejaVu Serif', serif;
            letter-spacing: 0.02em;
        }

        /* ----- Watermark (every page) ----- */
        .pdf-watermark {
            position: fixed;
            top: 38%;
            left: -10%;
            width: 120%;
            text-align: center;
            font-family: 'DejaVu Serif', serif;
            font-size: 64px;
            font-weight: bold;
            color: #1a3646;
            opacity: 0.06;
            transform: rotate(-28deg);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            z-index: 0;
            pointer-events: none;
        }

        /* ----- Fixed logo (every page, top right) ----- */
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

        /* ----- Cover ----- */
        .cover-wrap {
            padding: 8mm 6mm 14mm;
            page-break-after: always;
            position: relative;
            z-index: 1;
        }

        .cover-frame {
            border: 3px double #8b7355;
            padding: 4mm;
            background: rgba(254, 253, 251, 0.94);
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
            font-size: 24px;
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
            width: 130px;
            color: #7c6b52;
            font-weight: bold;
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

        .cover-callout {
            margin: 18px auto 0;
            max-width: 520px;
            padding: 10px 14px;
            border: 1px dashed #b8860b;
            background: rgba(255, 251, 235, 0.85);
            font-size: 9.5px;
            color: #7c4a03;
            line-height: 1.55;
        }

        /* ----- Section / page shell ----- */
        .page-shell {
            border: 1px solid #d4c4a8;
            background: rgba(250, 248, 243, 0.92);
            padding: 18px 16px;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

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
            width: 25%;
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

        /* ----- Data tables ----- */
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

        table.data-table tbody tr:nth-child(even) { background: #faf8f3; }
        table.data-table tbody tr:nth-child(odd)  { background: #fffefb; }

        table.data-table .total-row td {
            background: #f5efe3 !important;
            font-weight: bold;
            border-top: 2px solid #b8860b;
            color: #1a3646;
        }

        .muted {
            color: #9ca3af;
            font-style: italic;
        }

        .page-break { page-break-before: always; }

        /* ----- Upgrade callout ----- */
        .upgrade-page {
            border: 2px double #8b7355;
            background: rgba(254, 253, 251, 0.96);
            padding: 12mm 10mm;
            page-break-before: always;
            position: relative;
            z-index: 1;
        }

        .upgrade-page h2 {
            margin: 0 0 8px;
            font-family: 'DejaVu Serif', serif;
            font-size: 16px;
            color: #1a3646;
            border-bottom: 2px solid #b8860b;
            padding-bottom: 8px;
        }

        .upgrade-lead {
            font-size: 10.5px;
            color: #4b5563;
            line-height: 1.55;
            margin: 0 0 14px;
        }

        table.upgrade-plans {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            border: 2px solid #1a3646;
            font-size: 9.5px;
        }

        table.upgrade-plans th {
            background: #1a3646;
            color: #faf7ef;
            padding: 7px;
            text-align: left;
            border: 1px solid #152a38;
            font-weight: bold;
        }

        table.upgrade-plans td {
            padding: 7px;
            border: 1px solid #e6dcc8;
            background: #fffefb;
            vertical-align: top;
        }

        table.upgrade-plans tr:nth-child(even) td { background: #faf8f3; }
    </style>
</head>
<body>
    {{-- Watermark: repeats on every page via position: fixed --}}
    <div class="pdf-watermark">Created With Chandla Book</div>

    @if($hasLogo)
        <div class="pdf-fixed-logo">
            <img src="{{ $logoPath }}" alt="Chandla Book">
        </div>
    @endif

    {{-- ===== Cover page ===== --}}
    <div class="cover-wrap">
        <div class="cover-frame">
            <div class="cover-frame-inner serif-title">
                <div class="cover-band">
                    <p class="cover-title-main">Chandla register — Free</p>
                </div>
                <p class="cover-title-sub">First 50 entries across all events</p>

                <div class="cover-meta">
                    <table class="cover-meta-table">
                        <tr>
                            <td class="lbl">Account holder</td>
                            <td>{{ $user->name }}</td>
                        </tr>
                        @if(!empty($user->email))
                            <tr>
                                <td class="lbl">Email</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                        @endif
                        @if(!empty($user->phone))
                            <tr>
                                <td class="lbl">Mobile</td>
                                <td>{{ $user->phone }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="lbl">Entries in this file</td>
                            <td>{{ $entries->count() }} row{{ $entries->count() === 1 ? '' : 's' }} · across all events</td>
                        </tr>
                        <tr>
                            <td class="lbl">Generated on</td>
                            <td>{{ now()->timezone(config('app.timezone'))->format('M j, Y · g:i A') }}</td>
                        </tr>
                    </table>
                </div>

                <div class="cover-callout">
                    <strong>Free-plan export.</strong> Your account is on the 50-entry free tier. Upgrade to a paid pack
                    (Host Plus Plan / Family / Premium Host Plan) to unlock unlimited entries and full event-level PDF exports.
                </div>

                <p class="cover-footer-note">Chandla Book · Free-tier ledger extract</p>
            </div>
        </div>
    </div>

    {{-- ===== Summary ===== --}}
    <div class="page-shell">
        <h2 class="section-head serif-title"><span>Summary of these {{ $entries->count() }} entries</span></h2>
        <table class="summary-grid">
            <tr>
                <td>
                    <div class="summary-kicker">Total collected</div>
                    <div class="summary-num"><span class="inr">&#8377; {{ number_format($totalCollected, 2) }}</span></div>
                    <div class="summary-note">Cash + cover</div>
                </td>
                <td>
                    <div class="summary-kicker">Cash entries</div>
                    <div class="summary-num"><span class="inr">&#8377; {{ number_format($cashTotal, 2) }}</span></div>
                    <div class="summary-note">{{ $cash->count() }} {{ $cash->count() === 1 ? 'row' : 'rows' }}</div>
                </td>
                <td>
                    <div class="summary-kicker">Cover entries</div>
                    <div class="summary-num"><span class="inr">&#8377; {{ number_format($coverTotal, 2) }}</span></div>
                    <div class="summary-note">{{ $cover->count() }} {{ $cover->count() === 1 ? 'row' : 'rows' }}</div>
                </td>
                <td>
                    <div class="summary-kicker">Gifts listed</div>
                    <div class="summary-num">{{ $gift->count() }}</div>
                    <div class="summary-note">{{ $gift->count() === 1 ? 'gift row' : 'gift rows' }}</div>
                </td>
            </tr>
        </table>
        <p style="margin: 4px 0 0; font-size: 9px; color: #6b7280;">
            Entries shown earliest-first by received date. Categories sorted in the order they appear in the app: Cash → Cover → Gift.
        </p>
    </div>

    {{-- ===== Cash ===== --}}
    <div class="page-break"></div>
    <div class="page-shell">
        <h2 class="section-head serif-title"><span>Cash contributions</span></h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:6%">#</th>
                    <th style="width:24%">Giver</th>
                    <th style="width:26%">Event</th>
                    <th style="width:14%">Phone</th>
                    <th style="width:14%">Date</th>
                    <th style="width:16%">Amount (<span class="inr">&#8377;</span>)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cash as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->giver_name }}</td>
                        <td>{{ $row->event->title ?? '—' }}</td>
                        <td>{{ $row->giver_phone ?: '—' }}</td>
                        <td>{{ optional($row->received_date)->format('d/m/Y') }}</td>
                        <td><span class="inr">&#8377; {{ number_format($row->amount, 2) }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No cash entries.</td>
                    </tr>
                @endforelse
                @if($cash->count() > 0)
                    <tr class="total-row">
                        <td colspan="5">Section total ({{ $cash->count() }} rows)</td>
                        <td><span class="inr">&#8377; {{ number_format($cashTotal, 2) }}</span></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- ===== Cover ===== --}}
    <div class="page-break"></div>
    <div class="page-shell">
        <h2 class="section-head serif-title"><span>Cover amounts</span></h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:6%">#</th>
                    <th style="width:24%">Giver</th>
                    <th style="width:26%">Event</th>
                    <th style="width:14%">Phone</th>
                    <th style="width:14%">Date</th>
                    <th style="width:16%">Amount (<span class="inr">&#8377;</span>)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cover as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->giver_name }}</td>
                        <td>{{ $row->event->title ?? '—' }}</td>
                        <td>{{ $row->giver_phone ?: '—' }}</td>
                        <td>{{ optional($row->received_date)->format('d/m/Y') }}</td>
                        <td><span class="inr">&#8377; {{ number_format($row->amount, 2) }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No cover entries.</td>
                    </tr>
                @endforelse
                @if($cover->count() > 0)
                    <tr class="total-row">
                        <td colspan="5">Section total ({{ $cover->count() }} rows)</td>
                        <td><span class="inr">&#8377; {{ number_format($coverTotal, 2) }}</span></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- ===== Gifts ===== --}}
    <div class="page-break"></div>
    <div class="page-shell">
        <h2 class="section-head serif-title"><span>Gift register</span></h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:6%">#</th>
                    <th style="width:24%">Giver</th>
                    <th style="width:28%">Event</th>
                    <th style="width:14%">Phone</th>
                    <th style="width:14%">Date</th>
                    <th style="width:14%">Gift item</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gift as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->giver_name }}</td>
                        <td>{{ $row->event->title ?? '—' }}</td>
                        <td>{{ $row->giver_phone ?: '—' }}</td>
                        <td>{{ optional($row->received_date)->format('d/m/Y') }}</td>
                        <td>{{ $row->gift_item_name ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No gift entries.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <p style="font-size: 9px; color: #9ca3af; text-align: center; margin: 10px 0 0;">
            End of free-tier register · Created with Chandla Book
        </p>
    </div>

    {{-- ===== Upgrade page ===== --}}
    <div class="upgrade-page">
        <h2 class="serif-title">Unlock the full register — upgrade your plan</h2>
        <p class="upgrade-lead">
            This export is limited to your <strong>first 50 entries</strong> on the free tier. Upgrade once on Razorpay
            to remove the cap, enable per-event PDF exports, and (with Family / Premium Host Plan) add family members who can
            help manage your account.
        </p>

        <table class="upgrade-plans">
            <thead>
                <tr>
                    <th style="width:22%">Plan</th>
                    <th style="width:14%">Price</th>
                    <th style="width:64%">Highlights</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Celebration</strong></td>
                    <td><span class="inr">&#8377;</span> {{ number_format($celebrationInr, 0) }}</td>
                    <td>Invitation layouts · cinematic video · pre-wedding studio.</td>
                </tr>
                <tr>
                    <td><strong>Host Plus Plan</strong></td>
                    <td><span class="inr">&#8377;</span> {{ number_format($hostDuoInr, 0) }}</td>
                    <td>2 events · unlimited chandla rows · full event PDF exports.</td>
                </tr>
                <tr>
                    <td><strong>Family Plan</strong></td>
                    <td><span class="inr">&#8377;</span> {{ number_format($familyInr, 0) }}</td>
                    <td>Everything in Host Plus Plan + add <strong>3 family members</strong> with full add &amp; edit access.</td>
                </tr>
                <tr>
                    <td><strong>Premium Host Plan</strong></td>
                    <td><span class="inr">&#8377;</span> {{ number_format($premiumInr, 0) }}</td>
                    <td>All-in-one: Celebration + Host Plus Plan + Family Plan + 3 events on your account.</td>
                </tr>
            </tbody>
        </table>

        <p style="margin:14px 0 0;font-size:8.5px;color:#9ca3af;text-align:center;line-height:1.45;">
            Pay once on Razorpay using the same email or phone as your Chandla Book account so packs unlock automatically.<br>
            Created with Chandla Book · {{ config('app.name', 'Chandla Book') }}
        </p>
    </div>
</body>
</html>

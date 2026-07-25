<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/chandla-favicon.png') }}">
    <meta charset="utf-8">
    <title>Expense Register — {{ $filterLabel }}</title>
    @php
        $logoPath = public_path('images/chandla-logo.png');
        $hasLogo  = file_exists($logoPath);
        $pdfContactEmail = trim((string) config('chandlabook.support_email', ''));
        if ($pdfContactEmail === '') { $pdfContactEmail = (string) config('mail.from.address', ''); }
        $pdfContactPhone = trim((string) config('chandlabook.support_phone', ''));
        $pdfSiteHost     = parse_url((string) config('app.url'), PHP_URL_HOST);
    @endphp
    <style>
        @page { margin: 26mm 11mm 22mm 11mm; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            line-height: 1.45;
            color: #1f2937;
            background: #fdfbf7;
        }
        .inr { font-family: 'DejaVu Sans', sans-serif; }
        .serif-title { font-family: 'DejaVu Serif', serif; letter-spacing: 0.02em; }

        /* Running logo on every page */
        .pdf-fixed-logo { position: fixed; top: 6mm; right: 10mm; z-index: 1000; text-align: right; }
        .pdf-fixed-logo img { height: 34px; width: auto; display: block; }

        /* Cover */
        .cover-wrap { padding: 8mm 6mm 14mm; page-break-after: always; }
        .cover-frame { border: 3px double #8b7355; padding: 4mm; background: #fefdfb; }
        .cover-frame-inner { border: 1px solid #c9b896; padding: 14mm 12mm; text-align: center; }
        .cover-band {
            display: inline-block; padding: 6px 28px;
            border-top: 2px solid #b8860b; border-bottom: 2px solid #b8860b;
            margin: 14px 0 18px;
        }
        .cover-title-main {
            margin: 0; font-size: 26px; font-weight: bold;
            color: #1a3646; text-transform: uppercase; letter-spacing: 0.12em;
        }
        .cover-title-sub { margin: 10px 0 0; font-size: 13px; color: #5c4d3d; font-style: italic; }
        .cover-meta { margin-top: 22px; width: 100%; border-top: 1px dashed #c9b896; padding-top: 14px; }
        table.cover-meta-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.cover-meta-table td { padding: 7px 4px; border-bottom: 1px dotted #e5dcc8; vertical-align: top; }
        table.cover-meta-table td.lbl { width: 130px; color: #7c6b52; font-weight: bold; }
        table.cover-meta-table td.val { color: #1f2937; }
        table.cover-meta-table tr:last-child td { border-bottom: none; }
        .cover-footer-note {
            margin-top: 28px; font-size: 9px; color: #9a8b73;
            letter-spacing: 0.06em; text-transform: uppercase;
        }

        /* Page shell */
        .page-shell { border: 1px solid #d4c4a8; background: #faf8f3; padding: 18px 16px; margin-bottom: 8px; }

        /* Section headings */
        .section-head {
            margin: 0 0 10px 0; padding-bottom: 6px;
            border-bottom: 2px solid #1a3646;
            font-family: 'DejaVu Serif', serif;
            font-size: 15px; color: #1a3646; letter-spacing: 0.04em;
        }
        .section-head span {
            display: inline-block; padding-right: 12px;
            border-bottom: 3px solid #b8860b; margin-bottom: -2px; padding-bottom: 5px;
        }

        /* Summary grid */
        .summary-grid { width: 100%; border-collapse: collapse; margin: 10px 0 14px; }
        .summary-grid td { width: 33%; vertical-align: top; padding: 8px; border: 1px solid #ddd4c4; background: #fffefb; }
        .summary-kicker { font-size: 8px; text-transform: uppercase; letter-spacing: 0.14em; color: #8a7960; margin-bottom: 4px; }
        .summary-num { font-size: 15px; font-weight: bold; color: #1a3646; font-family: 'DejaVu Serif', serif; }
        .summary-note { font-size: 8.5px; color: #6b7280; margin-top: 4px; }

        /* Data table (ledger style) */
        table.data-table {
            width: 100%; border-collapse: collapse;
            margin: 8px 0 14px; border: 2px solid #1a3646; background: #fffefb;
        }
        table.data-table thead tr { background: #1a3646; color: #faf7ef; }
        table.data-table th {
            padding: 8px 7px; font-size: 9px; text-transform: uppercase;
            letter-spacing: 0.08em; text-align: left;
            border: 1px solid #152a38; font-weight: bold;
        }
        table.data-table td { padding: 7px; border: 1px solid #e6dcc8; vertical-align: top; font-size: 10px; }
        table.data-table tbody tr:nth-child(even) { background: #faf8f3; }
        table.data-table tbody tr:nth-child(odd) { background: #fffefb; }
        table.data-table .total-row td {
            background: #f5efe3 !important; font-weight: bold;
            border-top: 2px solid #b8860b; color: #1a3646;
        }
        .muted { color: #9ca3af; font-style: italic; }
        .page-break { page-break-before: always; }
        .mini-heading {
            font-size: 11px; font-weight: bold; color: #5c4d3d;
            margin: 14px 0 6px; letter-spacing: 0.06em; text-transform: uppercase;
        }
        .hr-decor { height: 0; border: none; border-top: 1px solid #e8dcc8; margin: 12px 0; }

        /* Category badge (simple text for PDF) */
        .badge {
            font-size: 8px; font-weight: bold; letter-spacing: 0.06em;
            text-transform: uppercase; padding: 1px 4px; border-radius: 3px;
        }
        .badge-cat { background: #dbeafe; color: #1e40af; }
        .badge-pay { background: #d1fae5; color: #065f46; }

        /* Promo page */
        .promo-page { border: 2px double #8b7355; background: #fefdfb; padding: 12mm 10mm; page-break-before: always; }
        .promo-page h2 { margin: 0 0 8px; font-family: 'DejaVu Serif', serif; font-size: 17px; color: #1a3646; letter-spacing: 0.03em; border-bottom: 2px solid #b8860b; padding-bottom: 8px; }
        .promo-lead { font-size: 10.5px; color: #4b5563; line-height: 1.55; margin: 0 0 14px; }
        table.promo-contact { width: 100%; border-collapse: collapse; margin: 0 0 14px; border: 1px solid #ddd4c4; background: #fffefb; }
        table.promo-contact td { padding: 8px 10px; font-size: 10px; border-bottom: 1px dotted #e5dcc8; vertical-align: top; }
        table.promo-contact td.pt { width: 92px; font-weight: bold; color: #7c6b52; }
    </style>
</head>
<body>

@if($hasLogo)
    <div class="pdf-fixed-logo">
        <img src="{{ $logoPath }}" alt="Chandla Book">
    </div>
@endif

@php
    $totalAmount   = $expenses->sum('amount');
    $cashAmt       = $expenses->where('payment_method','cash')->sum('amount');
    $gpayAmt       = $expenses->where('payment_method','gpay')->sum('amount');
    $bankAmt       = $expenses->where('payment_method','bank_transfer')->sum('amount');
    $chequeAmt     = $expenses->where('payment_method','cheque')->sum('amount');
    $totalEntries  = $expenses->count();
    $byCategory    = $expenses->groupBy('category');
    $byEvent       = $expenses->groupBy(fn($e) => optional($e->event)->title ?? 'Unknown');
@endphp

{{-- Cover page --}}
<div class="cover-wrap">
    <div class="cover-frame">
        <div class="cover-frame-inner serif-title">
            <div class="cover-band">
                <p class="cover-title-main">Expense Register</p>
            </div>
            <p class="cover-title-sub">Event-wise expense ledger — financial summary</p>

            <div class="cover-meta">
                <table class="cover-meta-table">
                    <tr>
                        <td class="lbl">Filter</td>
                        <td class="val">{{ $filterLabel }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Total entries</td>
                        <td class="val">{{ $totalEntries }} records</td>
                    </tr>
                    <tr>
                        <td class="lbl">Total spent</td>
                        <td class="val"><span class="inr">&#8377; {{ number_format($totalAmount, 2) }}</span></td>
                    </tr>
                    <tr>
                        <td class="lbl">Prepared</td>
                        <td class="val">{{ now()->timezone(config('app.timezone'))->format('M j, Y · g:i A') }}</td>
                    </tr>
                </table>
            </div>
            <p class="cover-footer-note">Chandla Book · Confidential expense extract</p>
        </div>
    </div>
</div>

{{-- Financial Summary --}}
<div class="page-shell">
    <h2 class="section-head serif-title"><span>Financial Summary</span></h2>
    <table class="summary-grid">
        <tr>
            <td>
                <div class="summary-kicker">Total Spent</div>
                <div class="summary-num"><span class="inr">&#8377; {{ number_format($totalAmount, 2) }}</span></div>
                <div class="summary-note">All expenses combined</div>
            </td>
            <td>
                <div class="summary-kicker">Total Entries</div>
                <div class="summary-num">{{ $totalEntries }}</div>
                <div class="summary-note">Expense records</div>
            </td>
            <td>
                <div class="summary-kicker">Events Covered</div>
                <div class="summary-num">{{ $byEvent->count() }}</div>
                <div class="summary-note">Unique events</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="summary-kicker">Cash</div>
                <div class="summary-num"><span class="inr">&#8377; {{ number_format($cashAmt, 2) }}</span></div>
                <div class="summary-note">{{ $expenses->where('payment_method','cash')->count() }} entries</div>
            </td>
            <td style="background:#f0fdf4;border:1px solid #6ee7b7;">
                <div class="summary-kicker" style="color:#065f46;">GPay / UPI</div>
                <div class="summary-num" style="color:#065f46;"><span class="inr">&#8377; {{ number_format($gpayAmt, 2) }}</span></div>
                <div class="summary-note">{{ $expenses->where('payment_method','gpay')->count() }} entries</div>
            </td>
            <td>
                <div class="summary-kicker">Bank / Cheque</div>
                <div class="summary-num"><span class="inr">&#8377; {{ number_format($bankAmt + $chequeAmt, 2) }}</span></div>
                <div class="summary-note">Transfer + cheque</div>
            </td>
        </tr>
    </table>

    @if($byCategory->count())
        <p class="mini-heading">Category-wise Breakdown</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:40%">Category</th>
                    <th style="width:20%">Count</th>
                    <th style="width:40%">Total (<span class="inr">&#8377;</span>)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byCategory as $cat => $items)
                <tr>
                    <td style="text-transform:capitalize;">{{ $cat }}</td>
                    <td>{{ $items->count() }}</td>
                    <td><span class="inr">&#8377; {{ number_format($items->sum('amount'), 2) }}</span></td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>Grand Total</td>
                    <td>{{ $totalEntries }}</td>
                    <td><span class="inr">&#8377; {{ number_format($totalAmount, 2) }}</span></td>
                </tr>
            </tbody>
        </table>
    @endif
</div>

<div class="page-break"></div>

{{-- Detailed Expense Register --}}
<div class="page-shell">
    <h2 class="section-head serif-title"><span>Expense Detail Register</span></h2>
    <p style="margin:0 0 10px;font-size:9px;color:#6b7280;">Sorted by date · All expenses included</p>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:10%">Date</th>
                <th style="width:20%">Event</th>
                <th style="width:20%">Title</th>
                <th style="width:12%">Category</th>
                <th style="width:12%">Payment</th>
                <th style="width:16%">Payee</th>
                <th style="width:10%">Amount (<span class="inr">&#8377;</span>)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
            <tr>
                <td class="whitespace-nowrap">{{ $expense->expense_date?->format('d/m/Y') }}</td>
                <td>{{ optional($expense->event)->title ?? '—' }}</td>
                <td>
                    {{ $expense->title }}
                    @if($expense->description)
                        <br><span style="font-size:8.5px;color:#9ca3af;">{{ $expense->description }}</span>
                    @endif
                </td>
                <td style="text-transform:capitalize;">{{ $expense->category }}</td>
                <td style="text-transform:capitalize;">{{ str_replace('_', ' ', $expense->payment_method) }}</td>
                <td>
                    {{ $expense->payee_name ?: '—' }}
                    @if($expense->payee_phone)
                        <br><span style="font-size:8.5px;color:#9ca3af;">{{ $expense->payee_phone }}</span>
                    @endif
                </td>
                <td style="text-align:right;font-weight:bold;">
                    <span class="inr">&#8377; {{ number_format($expense->amount, 2) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="muted">No expense entries found.</td>
            </tr>
            @endforelse
            @if($expenses->count() > 0)
            <tr class="total-row">
                <td colspan="6">Grand Total — {{ $totalEntries }} entries</td>
                <td style="text-align:right;"><span class="inr">&#8377; {{ number_format($totalAmount, 2) }}</span></td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

{{-- Event-wise Summary --}}
@if($byEvent->count() > 1)
<div class="page-break"></div>
<div class="page-shell">
    <h2 class="section-head serif-title"><span>Event-wise Summary</span></h2>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:40%">Event</th>
                <th style="width:20%">Entries</th>
                <th style="width:40%">Total Spent (<span class="inr">&#8377;</span>)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byEvent->sortByDesc(fn($g) => $g->sum('amount')) as $eventTitle => $items)
            <tr>
                <td>{{ $eventTitle }}</td>
                <td>{{ $items->count() }}</td>
                <td style="text-align:right;font-weight:bold;">
                    <span class="inr">&#8377; {{ number_format($items->sum('amount'), 2) }}</span>
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">Grand Total</td>
                <td style="text-align:right;"><span class="inr">&#8377; {{ number_format($totalAmount, 2) }}</span></td>
            </tr>
        </tbody>
    </table>
    <hr class="hr-decor">
    <p style="font-size: 9px; color: #9ca3af; text-align: center; margin: 8px 0 0;">
        End of register · Chandla Book · {{ config('app.name', 'Chandla Book') }}
    </p>
</div>
@endif

{{-- Promo / branding page --}}
<div class="promo-page">
    <h2 class="serif-title">Chandla Book — Plans, services &amp; contact</h2>
    <p class="promo-lead">
        Chandla Book is built for Indian weddings and community occasions: record cash, cover and gifts in one ledger,
        track event-wise expenses, manage inventory, share payment QR codes, and export registers like this PDF for your records.
    </p>
    @if($pdfContactEmail !== '' || $pdfContactPhone !== '' || !empty($pdfSiteHost))
        <table class="promo-contact">
            @if($pdfContactEmail !== '')
                <tr><td class="pt">Email</td><td>{{ $pdfContactEmail }}</td></tr>
            @endif
            @if($pdfContactPhone !== '')
                <tr><td class="pt">Phone</td><td>{{ $pdfContactPhone }}</td></tr>
            @endif
            @if(!empty($pdfSiteHost))
                <tr><td class="pt">Website</td><td>{{ rtrim(config('app.url'), '/') }}</td></tr>
            @endif
        </table>
    @endif
    <p style="margin:14px 0 0;font-size:8.5px;color:#9ca3af;text-align:center;line-height:1.45;">
        Chandla Book · {{ config('app.name', 'Chandla Book') }}
    </p>
</div>

</body>
</html>

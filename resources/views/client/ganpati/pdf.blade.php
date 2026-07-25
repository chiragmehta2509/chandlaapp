<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ganpati Chanda Register — {{ $event->title }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        font-size: 10pt;
        color: #1a1a1a;
        background: #fff;
    }

    /* ── Header ── */
    .header {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 60%, #c2410c 100%);
        color: #fff;
        padding: 18px 24px 14px;
        border-radius: 0 0 8px 8px;
        margin-bottom: 14px;
    }
    .header-top {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 8px;
    }
    .header-emoji {
        font-size: 28px;
        line-height: 1;
    }
    .header-title {
        font-size: 20pt;
        font-weight: 700;
        letter-spacing: -0.3px;
    }
    .header-sub {
        font-size: 9pt;
        opacity: 0.88;
    }
    .header-meta {
        display: flex;
        gap: 20px;
        font-size: 8pt;
        opacity: 0.90;
        margin-top: 6px;
        border-top: 1px solid rgba(255,255,255,0.3);
        padding-top: 6px;
    }
    .header-meta span { white-space: nowrap; }

    /* ── Summary Box ── */
    .summary-box {
        background: #fff7ed;
        border: 1.5px solid #fed7aa;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 16px;
        display: flex;
        gap: 0;
    }
    .summary-item {
        flex: 1;
        text-align: center;
        padding: 0 8px;
        border-right: 1px solid #fed7aa;
    }
    .summary-item:last-child { border-right: none; }
    .summary-label {
        font-size: 7pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #9a3412;
        margin-bottom: 3px;
    }
    .summary-value {
        font-size: 13pt;
        font-weight: 700;
        color: #c2410c;
    }

    /* ── Section heading ── */
    .section-heading {
        font-size: 9pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #ea580c;
        border-bottom: 1.5px solid #fed7aa;
        padding-bottom: 4px;
        margin: 14px 0 8px;
    }

    /* ── Table ── */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.5pt;
    }
    thead tr {
        background: #fff7ed;
        border-bottom: 1.5px solid #fed7aa;
    }
    thead th {
        padding: 6px 8px;
        text-align: left;
        font-size: 7.5pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #9a3412;
    }
    thead th.text-right { text-align: right; }
    tbody tr { border-bottom: 0.75px solid #f3f4f6; }
    tbody tr:nth-child(even) { background: #fffbf5; }
    tbody td { padding: 5px 8px; vertical-align: top; }
    tbody td.amount { text-align: right; font-weight: 600; font-variant-numeric: tabular-nums; }
    tbody td.serial { color: #94a3b8; font-size: 7pt; }
    tbody td.method {
        font-size: 7.5pt;
        font-weight: 600;
    }
    tfoot tr {
        background: #fff7ed;
        border-top: 2px solid #f97316;
    }
    tfoot td { padding: 7px 8px; font-weight: 700; }
    tfoot td.total-label { color: #9a3412; text-transform: uppercase; font-size: 8pt; letter-spacing: 0.08em; }
    tfoot td.total-value { text-align: right; color: #c2410c; font-size: 11pt; }

    .no-data {
        text-align: center;
        padding: 20px;
        color: #94a3b8;
        font-style: italic;
        font-size: 9pt;
    }

    /* ── Footer note ── */
    .footer-note {
        margin-top: 20px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        font-size: 7pt;
        color: #94a3b8;
        text-align: center;
    }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div class="header-top">
        <span class="header-emoji">🪔</span>
        <div>
            <div class="header-title">Ganpati Chanda Register</div>
            <div class="header-sub">{{ $event->title }}</div>
        </div>
    </div>
    <div class="header-meta">
        @if($event->event_date)
        <span>📅 {{ optional($event->event_date)->format('d M Y') }}</span>
        @endif
        @if($event->venue)
        <span>📍 {{ $event->venue }}</span>
        @endif
        <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
        <span>Total Entries: {{ $entries->count() }}</span>
    </div>
</div>

{{-- Summary --}}
@php
    $totalAll   = $entries->sum('amount');
    $cashTotal  = $entries->where('payment_method', 'cash')->sum('amount');
    $gpayTotal  = $entries->where('payment_method', 'gpay')->sum('amount');
    $otherTotal = $entries->whereNotIn('payment_method', ['cash', 'gpay'])->sum('amount');
@endphp
<div class="summary-box">
    <div class="summary-item">
        <div class="summary-label">Total Collection</div>
        <div class="summary-value">₹{{ number_format($totalAll, 0) }}</div>
    </div>
    <div class="summary-item">
        <div class="summary-label">Cash</div>
        <div class="summary-value" style="color:#16a34a;">₹{{ number_format($cashTotal, 0) }}</div>
    </div>
    <div class="summary-item">
        <div class="summary-label">GPay / UPI</div>
        <div class="summary-value" style="color:#2563eb;">₹{{ number_format($gpayTotal, 0) }}</div>
    </div>
    <div class="summary-item">
        <div class="summary-label">Other</div>
        <div class="summary-value" style="color:#9333ea;">₹{{ number_format($otherTotal, 0) }}</div>
    </div>
</div>

{{-- All Entries --}}
<div class="section-heading">All Chanda Entries</div>
@if($entries->isEmpty())
<p class="no-data">No entries recorded yet.</p>
@else
<table>
    <thead>
        <tr>
            <th style="width:28px;">#</th>
            <th>Donor Name</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Date</th>
            <th>Method</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($entries as $i => $chandla)
        <tr>
            <td class="serial">{{ $i + 1 }}</td>
            <td><strong>{{ $chandla->giver_name }}</strong></td>
            <td>{{ $chandla->giver_phone ?? '—' }}</td>
            <td style="max-width:120px; word-break:break-word;">{{ $chandla->giver_address ?? '—' }}</td>
            <td>{{ optional($chandla->received_date)->format('d/m/Y') ?? '—' }}</td>
            <td class="method">{{ strtoupper($chandla->payment_method ?? 'Other') }}</td>
            <td class="amount">₹{{ number_format((float)$chandla->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" class="total-label">Total Collection</td>
            <td class="total-value">₹{{ number_format($totalAll, 2) }}</td>
        </tr>
    </tfoot>
</table>
@endif

{{-- By Method breakdowns --}}
@if($cash->isNotEmpty())
<div class="section-heading" style="margin-top:20px;">Cash Entries</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;">#</th>
            <th>Donor Name</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Date</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cash as $i => $chandla)
        <tr>
            <td class="serial">{{ $i + 1 }}</td>
            <td><strong>{{ $chandla->giver_name }}</strong></td>
            <td>{{ $chandla->giver_phone ?? '—' }}</td>
            <td>{{ $chandla->giver_address ?? '—' }}</td>
            <td>{{ optional($chandla->received_date)->format('d/m/Y') ?? '—' }}</td>
            <td class="amount">₹{{ number_format((float)$chandla->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="total-label">Cash Total</td>
            <td class="total-value" style="color:#16a34a;">₹{{ number_format($cash->sum('amount'), 2) }}</td>
        </tr>
    </tfoot>
</table>
@endif

@if($gpay->isNotEmpty())
<div class="section-heading" style="margin-top:20px;">GPay / UPI Entries</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;">#</th>
            <th>Donor Name</th>
            <th>Phone</th>
            <th>Transaction ID</th>
            <th>Date</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($gpay as $i => $chandla)
        <tr>
            <td class="serial">{{ $i + 1 }}</td>
            <td><strong>{{ $chandla->giver_name }}</strong></td>
            <td>{{ $chandla->giver_phone ?? '—' }}</td>
            <td style="font-size:7.5pt;">{{ $chandla->gpay_transaction_id ?? '—' }}</td>
            <td>{{ optional($chandla->received_date)->format('d/m/Y') ?? '—' }}</td>
            <td class="amount">₹{{ number_format((float)$chandla->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="total-label">GPay Total</td>
            <td class="total-value" style="color:#2563eb;">₹{{ number_format($gpay->sum('amount'), 2) }}</td>
        </tr>
    </tfoot>
</table>
@endif

<div class="footer-note">
    Chandla Book · Ganpati Special · Generated {{ now()->format('d M Y h:i A') }} ·
    🙏 Ganpati Bappa Morya!
</div>

</body>
</html>

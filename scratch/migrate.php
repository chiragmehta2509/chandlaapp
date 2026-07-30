<?php
$sourcePath = 'c:\\Users\\Chirag\\Desktop\\New folder\\ChandlaBook\\resources\\views\\client\\chandlas\\pdf.blade.php';
$destPath = 'c:\\Users\\Chirag\\Desktop\\New folder\\ChandlaBook\\resources\\views\\client\\ganpati\\pdf.blade.php';

$content = file_get_contents($sourcePath);

// Title
$content = str_replace('Chandla register — {{ $event->title }}', 'Ganpati Chanda Register — {{ $event->title }}', $content);
$content = str_replace('Chandla register</p>', 'Ganpati Chanda Register</p>', $content);
$content = str_replace('Traditional collection ledger', 'Ganpati Chanda collection ledger', $content);

// Variables
$content = preg_replace('/\$notesOnHand = \[.*?\];/s', '', $content);
$content = preg_replace('/\$totalNotesCount = array_sum\(\$notesOnHand\);.*?foreach \(\$notesOnHand as \$denomination => \$count\) \{.*?\$totalCashOnHand \+= \$denomination \* \$count;.*?\}/s', '', $content);

$content = str_replace("\$totalCollected = \$event->chandlas->sum('amount');", "\$totalCollected = \$entries->sum('amount');", $content);

// Replacements for cover/gift to other
$content = str_replace('$coverTotal = $cover->sum(\'amount\');', '$otherTotal = $other->sum(\'amount\');', $content);
$content = str_replace('$giftCount  = $gift->count();', '$otherCount = $other->count();', $content);

// Summary table
$content = str_replace('Cash Handed Over', 'Cash Collected', $content);
$content = str_replace('{{ number_format($totalCashOnHand, 0) }}', '{{ number_format($cashTotal, 0) }}', $content);
// Remove note under cash
$content = preg_replace('/<div class="summary-note">.*?<\/div>/s', '', $content, 1);

$content = str_replace('Cover & Gift', 'Other Methods', $content);
$content = str_replace('{{ number_format($coverTotal, 0) }}', '{{ number_format($otherTotal, 0) }}', $content);
$content = str_replace('(+ {{ $giftCount }} gifts)', '({{ $otherCount }} entries)', $content);

// Now for the tables:
// Rename $cash to $entries for the first table (which is all entries) - wait, chandla has a cash inventory table. Let's just remove the inventory table completely.
$content = preg_replace('/<h3 class="section-head">.*?Cash Inventory \(Handed Over\).*?<\/table>\s*<\/div>/s', '', $content);

// Cash table uses $cash in chandlas, which is same in ganpati.
// GPay table uses $gpay, same in ganpati.
// Cover/Gift tables we remove and replace with Other table.
$content = preg_replace('/<h3 class="section-head">.*?Cover \(Envelopes\).*?<\/table>\s*<\/div>/s', '', $content);
$content = preg_replace('/<h3 class="section-head">.*?Gifts.*?<\/table>\s*<\/div>/s', '', $content);

// Add Other table
$otherTable = <<<EOD
    @if(\$other->isNotEmpty())
    <div class="page-shell">
        <h3 class="section-head"><span>Other Entries</span></h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:25px; text-align:center;">#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Method</th>
                    <th style="width:75px; text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\$other as \$i => \$row)
                <tr>
                    <td style="text-align:center; color:#9ca3af;">{{ \$i + 1 }}</td>
                    <td><strong>{{ \$row->giver_name }}</strong><br><span style="font-size:8.5px; color:#6b7280;">{{ \$row->giver_address }}</span></td>
                    <td>{{ \$row->giver_phone }}</td>
                    <td>{{ strtoupper(\$row->payment_method ?? 'Other') }}</td>
                    <td style="text-align:right; font-weight:bold;">{{ number_format((float)\$row->amount, 0) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" style="text-align:right; font-size:10px;">TOTAL OTHER</td>
                    <td style="text-align:right;"><span class="inr">₹</span> {{ number_format(\$otherTotal, 0) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
EOD;

$content = str_replace('<!-- END GIFT TABLE -->', $otherTable, $content); // wait, there is no <!-- END GIFT TABLE -->.
// I will just append it before <div class="promo-page">

$content = preg_replace('/(<div class="promo-page">)/', $otherTable . "\n\n$1", $content);

// One thing: In chandlas/pdf.blade.php, is there an "All Entries" table? No, they are split by cash, gpay, cover, gift.
// Ganpati has an "All Entries" table. 
// I'll just change the layout of ganpati/pdf.blade.php to be identical to chandlas/pdf.blade.php by keeping only cash, gpay, and other tables.

file_put_contents($destPath, $content);
echo "Migrated!\n";

<?php
$source = file_get_contents('resources/views/client/chandlas/pdf.blade.php');

// Remove inventory section from source (lines containing inventory)
// For simplicity, let's just create a new ganpati/pdf.blade.php based on the structure of chandlas/pdf.blade.php
// But wait, it might be easier to just copy the file and do string replacements.
$dest = $source;

// Replace title
$dest = str_replace('Chandla register — {{ $event->title }}', 'Ganpati Chanda Register — {{ $event->title }}', $dest);
$dest = str_replace('<p class="cover-title-main">Chandla register</p>', '<p class="cover-title-main">Ganpati Chanda Register</p>', $dest);
$dest = str_replace('<p class="cover-title-sub">Traditional collection ledger — event summary</p>', '<p class="cover-title-sub">Ganpati Chanda collection ledger — event summary</p>', $dest);

// Remove inventory logic
$inventory_logic = <<<'EOD'
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
EOD;
$dest = str_replace($inventory_logic, '', $dest);

// Replace variables
$dest = str_replace("\$totalCollected = \$event->chandlas->sum('amount');\n", "\$totalCollected = \$entries->sum('amount');\n", $dest);

$dest = str_replace("\$coverTotal = \$cover->sum('amount');", "\$otherTotal = \$other->sum('amount');", $dest);
$dest = str_replace("\$giftCount  = \$gift->count();", "\$otherCount = \$other->count();", $dest);

// Replace summary table headers & variables
$dest = str_replace('<div class="summary-kicker">Cash Handed Over</div>', '<div class="summary-kicker">Cash Collected</div>', $dest);
$dest = str_replace('<div class="summary-num"><span class="inr">₹</span>{{ number_format($totalCashOnHand, 0) }}</div>', '<div class="summary-num"><span class="inr">₹</span>{{ number_format($cashTotal, 0) }}</div>', $dest);
$dest = preg_replace('/<div class="summary-note">.*?<\/div>/s', '<div class="summary-note"></div>', $dest, 1); // Remove the matched notes breakdown note

$dest = str_replace('<div class="summary-kicker">Digital (GPay)</div>', '<div class="summary-kicker">Digital (GPay)</div>', $dest);
$dest = str_replace('<div class="summary-kicker">Cover & Gift</div>', '<div class="summary-kicker">Other Methods</div>', $dest);
$dest = str_replace('₹</span>{{ number_format($coverTotal, 0) }}</div>', '₹</span>{{ number_format($otherTotal, 0) }}</div>', $dest);
$dest = str_replace('(+ {{ $giftCount }} gifts)', '(+ {{ $otherCount }} entries)', $dest);

// We need to replace the data tables too.
// Instead of trying to parse it with regex, I'll just rewrite ganpati/pdf.blade.php locally using the CSS from chandlas/pdf.blade.php.

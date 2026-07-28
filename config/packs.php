<?php

use App\Models\SubscriptionPack;
use Illuminate\Support\Facades\Schema;

/**
 * Subscription packs configuration.
 *
 * Pulls dynamic pack data from the `subscription_packs` database table if available,
 * falling back to array defaults.
 */
$devUnlockEnv = env('PACKS_DEV_UNLOCK');
if ($devUnlockEnv === null || $devUnlockEnv === '') {
    $devUnlockAll = false;
} else {
    $devUnlockAll = filter_var($devUnlockEnv, FILTER_VALIDATE_BOOLEAN);
}

$defaultLevelNames = [
    0 => 'Starter Plan',
    1 => 'Celebration Pack',
    2 => 'Guest Contribution',
    3 => 'Host Plus Plan',
    4 => 'Family Plan',
    5 => 'Premium Host',
    6 => 'Professional',
    7 => 'Enterprise',
];

$defaultPacks = [
    'starter' => [
        'label'       => 'Starter Plan',
        'amount_inr'  => 0.0,
        'min_level'   => 0,
        'description' => 'Perfect for small, intimate family events and basic ledger management.',
        'features'    => [
            '1 Event Limit',
            'Up to 50 Gift/Chandla Entries',
            'Basic Ledger Management',
            'Standard PDF Export',
            'Cash & Cover Tracking',
            '3 Family Viewers (Read Only)',
        ],
        'limits' => [
            'events'       => 1,
            'entries'      => 50,
            'qrCollection' => false,
            'editors'      => 0,
        ],
        'live_payment_url' => '',
        'test_payment_url' => '',
    ],
    'celebration' => [
        'label'       => 'Celebration Pack',
        'amount_inr'  => (float) env('PACK_CELEBRATION_AMOUNT', 300),
        'min_level'   => 1,
        'description' => 'Enhance your celebration with printable invitations and graphic studio assets.',
        'features'    => [
            '10 Invitation Templates',
            'Printable Invitation Designs',
            'Event Story / Reel Video Creator',
            'Countdown Studio Access',
            'Event Graphics & Social Posts',
            'Everything in Starter Plan',
        ],
        'live_payment_url' => env('PACK_CELEBRATION_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_CELEBRATION_RAZORPAY_URL_TEST', ''),
    ],
    'ledger_duo' => [
        'label'       => 'Host Plus Plan',
        'amount_inr'  => (float) env('PACK_LEDGER_DUO_AMOUNT', 500),
        'min_level'   => 3,
        'description' => 'Manage multiple events with unlimited ledger logs and hosting tools.',
        'features'    => [
            'Up to 2 Events',
            'Unlimited Entries (All Events)',
            'Advanced Financial Reports',
            'Full Event PDF Downloads',
            'Additional Hosting & Seating Tools',
            'Everything in Guest Contribution',
        ],
        'live_payment_url' => env('PACK_LEDGER_DUO_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_LEDGER_DUO_RAZORPAY_URL_TEST', ''),
    ],
    'family' => [
        'label'       => 'Family Plan',
        'amount_inr'  => (float) env('PACK_FAMILY_AMOUNT', 600),
        'min_level'   => 4,
        'description' => 'Coordinate family functions together with multi-editor read/write accounts.',
        'features'    => [
            '3 Family Editors (Write Access)',
            'Shared Event Management Space',
            'Joint Family Hosting Support',
            'Role-Based Team Permissions',
            'Everything in Host Plus Plan',
        ],
        'live_payment_url' => env('PACK_FAMILY_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_FAMILY_RAZORPAY_URL_TEST', ''),
    ],
    'premium_bundle' => [
        'label'       => 'Premium Host',
        'amount_inr'  => (float) env('PACK_PREMIUM_BUNDLE_AMOUNT', 700),
        'min_level'   => 5,
        'description' => 'Our flagship plan. Elevates everything with premium custom templates and reports.',
        'features'    => [
            'Up to 3 Events',
            'Premium Invitation Templates',
            'Premium Video / Reels Templates',
            'Priority Email & Chat Support',
            'Full Data Export & Email Reports',
            'Everything in Family Plan',
        ],
        'live_payment_url' => env('PACK_PREMIUM_BUNDLE_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_PREMIUM_BUNDLE_RAZORPAY_URL_TEST', ''),
    ],
    'guest_pay_single' => [
        'label'       => 'Guest Contribution',
        'amount_inr'  => (float) env('PACK_GUEST_PAY_SINGLE_AMOUNT', 400),
        'min_level'   => 2,
        'description' => 'Direct payment collections and unlimited ledger entries for your single event.',
        'features'    => [
            'Personal UPI / QR Collection',
            'Guest Payment Tracking',
            'Unlimited Entries (1 Event)',
            'Collection Status Reports',
            'Full Event PDF',
            'Everything in Celebration Pack',
        ],
        'live_payment_url' => env('PACK_GUEST_PAY_SINGLE_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_GUEST_PAY_SINGLE_RAZORPAY_URL_TEST', ''),
    ],
    'professional' => [
        'label'       => 'Professional',
        'amount_inr'  => (float) env('PACK_PROFESSIONAL_AMOUNT', 999),
        'min_level'   => 6,
        'description' => 'For power users and professional coordinators running multiple large events.',
        'features'    => [
            'Up to 10 Events',
            'Unlimited Family Editors',
            'Advanced Analytics Dashboard',
            'Custom Branding (Remove logo)',
            'Event Backup & Restore Utilities',
            'Premium Support Channel',
            'Everything in Premium Host',
        ],
        'live_payment_url' => env('PACK_PROFESSIONAL_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_PROFESSIONAL_RAZORPAY_URL_TEST', ''),
    ],
    'enterprise' => [
        'label'       => 'Enterprise',
        'amount_inr'  => (float) env('PACK_ENTERPRISE_AMOUNT', 9999),
        'min_level'   => 7,
        'description' => 'Bespoke integration, white labeling, and dedicated hosting for large organizations.',
        'features'    => [
            'Unlimited Events & Editors',
            'Organization Dashboard & Team Management',
            'White Label & Custom Domain Solution',
            'Full REST API Integrations',
            'Dedicated Account Manager',
            'Everything in Professional',
        ],
        'live_payment_url' => env('PACK_ENTERPRISE_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_ENTERPRISE_RAZORPAY_URL_TEST', ''),
    ],
];

// Query database if table exists
try {
    if (Schema::hasTable('subscription_packs')) {
        $dbPacks = SubscriptionPack::all();
        if ($dbPacks->isNotEmpty()) {
            foreach ($dbPacks as $pack) {
                if ($pack->slug === 'starter') {
                    $defaultLevelNames[0] = $pack->name;
                    continue;
                }
                $defaultLevelNames[$pack->min_level] = $pack->name;
                $defaultPacks[$pack->slug] = [
                    'label'            => $pack->name,
                    'amount_inr'       => (float) $pack->amount_inr,
                    'min_level'        => (int) $pack->min_level,
                    'description'      => $pack->description,
                    'badge'            => $pack->badge,
                    'is_popular'       => (bool) $pack->is_popular,
                    'features'         => $pack->features ?? [],
                    'limits'           => $pack->limits ?? [],
                    'live_payment_url' => $pack->live_payment_url ?? env('PACK_' . strtoupper($pack->slug) . '_RAZORPAY_URL', ''),
                    'test_payment_url' => $pack->test_payment_url ?? env('PACK_' . strtoupper($pack->slug) . '_RAZORPAY_URL_TEST', ''),
                ];
            }
        }
    }
} catch (\Throwable $e) {
    // Fallback to array defaults if DB connection is unavailable
}

return array_merge([
    'dev_unlock_all' => $devUnlockAll,
    'level_names'    => $defaultLevelNames,
], $defaultPacks);


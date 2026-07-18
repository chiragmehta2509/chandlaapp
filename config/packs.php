<?php

/**
 * Subscription packs configuration.
 *
 * Each pack defines:
 *   - label         : Display name used throughout the UI
 *   - amount_inr    : Price in Indian Rupees (overridable via .env)
 *   - description   : Short tagline shown on checkout and upgrade banners
 *   - features      : Bullet-point list shown on the checkout page
 *   - min_level     : The planLevel() integer this pack unlocks (used by feature gates)
 *   - live_payment_url  : Razorpay Payment Page URL for live payments (set in .env)
 *   - test_payment_url  : Razorpay Payment Page URL for test payments (set in .env)
 *
 * PACKS_DEV_UNLOCK: when true, all plan checks treat everyone as fully paid (local testing only).
 * Default false — set PACKS_DEV_UNLOCK=true in .env only for intentional dev overrides.
 */
$devUnlockEnv = env('PACKS_DEV_UNLOCK');
if ($devUnlockEnv === null || $devUnlockEnv === '') {
    $devUnlockAll = false;
} else {
    $devUnlockAll = filter_var($devUnlockEnv, FILTER_VALIDATE_BOOLEAN);
}

return [
    'dev_unlock_all' => $devUnlockAll,

    // ── Plan level boundaries (matches User::planLevel()) ────────────────────
    // Used by CheckPlanFeature middleware and upgrade banners.
    'level_names' => [
        0 => 'Starter Plan',
        1 => 'Celebration Pack',
        2 => 'Guest Contribution',
        3 => 'Host Plus Plan',
        4 => 'Family Plan',
        5 => 'Premium Host',
        6 => 'Professional',
        7 => 'Enterprise',
    ],

    // ── Purchasable packs ────────────────────────────────────────────────────

    'celebration' => [
        'label'       => 'Celebration Pack',
        'amount_inr'  => (float) env('PACK_CELEBRATION_AMOUNT', 300),
        'min_level'   => 1,
        'description' => 'Enhance your celebration with printable invitations and graphic studio assets.',
        'features'    => [
            '10 printable invitation templates',
            'Event Story / Reel Creator',
            'Countdown Studio access',
            'Event Graphics builder',
            'Everything in Starter Plan',
        ],
        'live_payment_url' => env('PACK_CELEBRATION_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_CELEBRATION_RAZORPAY_URL_TEST', ''),
    ],

    /**
     * Host Plus Plan: up to 2 events, unlimited chandla entries across all events.
     * Internal config key kept as 'ledger_duo' for backward compatibility.
     */
    'ledger_duo' => [
        'label'       => 'Host Plus Plan',
        'amount_inr'  => (float) env('PACK_LEDGER_DUO_AMOUNT', 500),
        'min_level'   => 3,
        'description' => 'Manage multiple events with unlimited ledger logs and hosting tools.',
        'features'    => [
            'Up to 2 events on your account',
            'Unlimited chandla entries across all events',
            'Advanced Financial Reports',
            'Full Event PDF downloads',
            'Seating & Hosting tools',
            'Everything in Guest Contribution',
        ],
        'live_payment_url' => env('PACK_LEDGER_DUO_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_LEDGER_DUO_RAZORPAY_URL_TEST', ''),
    ],

    /**
     * Family Plan: 2 events, unlimited chandla + up to 3 family editor accounts.
     */
    'family' => [
        'label'       => 'Family Plan',
        'amount_inr'  => (float) env('PACK_FAMILY_AMOUNT', 600),
        'min_level'   => 4,
        'description' => 'Coordinate family functions together with multi-editor read/write accounts.',
        'features'    => [
            '3 Family Editors with full write access',
            'Shared event workspace',
            'Joint Family Hosting support',
            'Everything in Host Plus Plan',
        ],
        'live_payment_url' => env('PACK_FAMILY_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_FAMILY_RAZORPAY_URL_TEST', ''),
    ],

    /**
     * Premium Host Plan: flagship plan with up to 3 events, premium templates, priority support.
     */
    'premium_bundle' => [
        'label'       => 'Premium Host',
        'amount_inr'  => (float) env('PACK_PREMIUM_BUNDLE_AMOUNT', 700),
        'min_level'   => 5,
        'description' => 'Elevates everything with premium custom templates, video reels, and priority support.',
        'features'    => [
            'Up to 3 events on your account',
            'Premium invitation & video/reels templates',
            'Priority customer support',
            'Everything in Family Plan',
        ],
        'live_payment_url' => env('PACK_PREMIUM_BUNDLE_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_PREMIUM_BUNDLE_RAZORPAY_URL_TEST', ''),
    ],

    /**
     * Guest Contribution: Direct GPay / UPI guest QR + unlimited chandla for 1 event.
     * After payment, open "Unlock Direct QR" on an event and use "Apply pack credit".
     */
    'guest_pay_single' => [
        'label'       => 'Guest Contribution',
        'amount_inr'  => (float) env('PACK_GUEST_PAY_SINGLE_AMOUNT', 400),
        'min_level'   => 2,
        'description' => 'Direct payment collections and unlimited ledger entries for your single event.',
        'features'    => [
            'Personal UPI/QR Collection for 1 event',
            'Guest payment tracking',
            'Unlimited chandla entries (1 event)',
            'Collection status reports',
            'Full Event PDF download',
            'Everything in Celebration Pack',
        ],
        'live_payment_url' => env('PACK_GUEST_PAY_SINGLE_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_GUEST_PAY_SINGLE_RAZORPAY_URL_TEST', ''),
    ],

    /**
     * Professional Plan: Up to 10 events, unlimited family editors, advanced analytics, custom branding.
     */
    'professional' => [
        'label'       => 'Professional',
        'amount_inr'  => (float) env('PACK_PROFESSIONAL_AMOUNT', 999),
        'min_level'   => 6,
        'description' => 'For power users and professional coordinators running multiple large events.',
        'features'    => [
            'Up to 10 events on your account',
            'Unlimited Family Editors',
            'Advanced Analytics Dashboard',
            'Custom branding (no Chandla Book logo)',
            'Everything in Premium Host',
        ],
        'live_payment_url' => env('PACK_PROFESSIONAL_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_PROFESSIONAL_RAZORPAY_URL_TEST', ''),
    ],

    /**
     * Enterprise Plan: Custom pricing, unlimited events, white-label, API access, dedicated manager.
     */
    'enterprise' => [
        'label'       => 'Enterprise',
        'amount_inr'  => (float) env('PACK_ENTERPRISE_AMOUNT', 9999),
        'min_level'   => 7,
        'description' => 'Bespoke integration, white labeling, and dedicated hosting for large organizations.',
        'features'    => [
            'Unlimited events & editors',
            'Organization Dashboard',
            'White-label / Custom domain solution',
            'REST API integration',
            'Dedicated Account Manager',
            'Everything in Professional',
        ],
        'live_payment_url' => env('PACK_ENTERPRISE_RAZORPAY_URL', ''),
        'test_payment_url' => env('PACK_ENTERPRISE_RAZORPAY_URL_TEST', ''),
    ],
];

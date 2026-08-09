<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'team_id' => env('APPLE_TEAM_ID'),
        'key_id' => env('APPLE_KEY_ID'),
        'private_key' => env('APPLE_PRIVATE_KEY'),
    ],

    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID'),
        'key_secret' => env('RAZORPAY_KEY_SECRET'),
        /** True when key starts with rzp_test_ — set automatically from the key. */
        'is_test_mode' => str_starts_with((string) env('RAZORPAY_KEY_ID', ''), 'rzp_test_'),
        /** Shown in Razorpay Dashboard → Webhooks → your endpoint (HMAC of raw body) */
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET', ''),
        /**
         * Test-mode amount override (paise). When > 0, every programmatic Razorpay
         * order creation AND verification uses this amount instead of the real plan amount.
         * Example: RAZORPAY_TEST_FORCE_PAISE=100 → all charges become ₹1.
         * Leave unset or 0 in production.
         */
        'test_force_paise' => (int) env('RAZORPAY_TEST_FORCE_PAISE', 0),
        /**
         * Test-only Payment Page link (rzp.io/rzp/...). When set, programmatic flows
         * (event plan unlock, Direct GPay unlock) redirect to this hosted page instead
         * of opening Razorpay's in-page checkout. The hosted page handles UPI QR + card
         * + netbanking reliably. Plans won't auto-unlock (no webhook tying back to
         * specific event); use this for UI/flow testing only.
         */
        'test_payment_link' => env('RAZORPAY_TEST_PAYMENT_LINK', ''),
    ],

    'msg91' => [
        'auth_key' => env('MSG91_AUTH_KEY'),
        'sender_id' => env('MSG91_SENDER_ID'),
    ],

    'upi' => [
        'id' => env('UPI_ID'),
        'name' => env('UPI_NAME', 'Chandla Book'),
    ],

    /**
     * One-time unlock for Direct GPay QR per event (Razorpay; default ₹400).
     * @deprecated UPI+admin path removed; use DIRECT_GPAY_UNLOCK_AMOUNT in .env if you need a different price.
     */
    'direct_gpay_unlock' => [
        'amount' => (float) env('DIRECT_GPAY_UNLOCK_AMOUNT', 400),
        'auto_verify' => filter_var(env('DIRECT_GPAY_UNLOCK_AUTO_VERIFY', false), FILTER_VALIDATE_BOOLEAN),
        'admin_notify_email' => env('ADMIN_NOTIFICATION_EMAIL', env('MAIL_FROM_ADDRESS', '')),
    ],
    'whatsapp' => [
        'token'            => env('WHATSAPP_TOKEN'),
        'phone_number_id'  => env('WHATSAPP_PHONE_NUMBER_ID'),
        'verify_token'     => env('WHATSAPP_VERIFY_TOKEN', 'chandlabook_whatsapp_verify'),
    ],
];


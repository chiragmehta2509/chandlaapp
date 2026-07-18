<?php

return [
    /** Receives “matrimonial UPI pending” (legacy) emails. */
    'admin_notify_email' => env('MATRIMONIAL_ADMIN_EMAIL', env('ADMIN_NOTIFICATION_EMAIL', env('MAIL_FROM_ADDRESS', ''))),

    /**
     * Find Partner plans. Keys are stored in matrimonial_plans.plan_type.
     * payment_link: Razorpay Payment Link URL (rzp.io) — set in .env or here.
     * Webhook: POST /webhooks/razorpay with RAZORPAY_WEBHOOK_SECRET; matches payer email/phone to a user.
     */
    'plans' => [
        '500' => [
            'label' => 'Plan — ₹500',
            'price_inr' => 500,
            'months' => 6,
            'payment_link' => env('MATRIMONIAL_RAZORPAY_LINK_500', 'https://rzp.io/rzp/GMpa5KoY'),
        ],
        '200' => [
            'label' => 'Plan — ₹200',
            'price_inr' => 200,
            'months' => 3,
            'payment_link' => env('MATRIMONIAL_RAZORPAY_LINK_200', 'https://rzp.io/rzp/zLd67Wb'),
        ],
    ],
];

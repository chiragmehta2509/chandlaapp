<?php

namespace App\Support;

/**
 * Centralized override for programmatic Razorpay charges in test mode.
 *
 * Set env RAZORPAY_TEST_FORCE_PAISE (e.g. 100 for ₹1) to force every programmatic
 * Razorpay order creation AND verification to use that amount instead of the real
 * plan/event amount. Charge and verify stay in agreement.
 *
 * Affects: event plan unlock (PlanPaymentController), Direct GPay unlock,
 * matrimonial plans, and anywhere else that calls $api->order->create with an amount.
 *
 * Leave unset (or set to 0) in production.
 */
class RazorpayTestAmount
{
    public static function isOverridden(): bool
    {
        return self::forcedPaise() > 0;
    }

    public static function forcedPaise(): int
    {
        return (int) config('services.razorpay.test_force_paise', 0);
    }

    /**
     * Returns the paise amount to actually use — either the forced test value or the original.
     */
    public static function resolve(int $originalPaise): int
    {
        $forced = self::forcedPaise();
        return $forced > 0 ? $forced : $originalPaise;
    }

    /**
     * Same but for floats (rupees).
     */
    public static function resolveRupees(float $originalRupees): float
    {
        $forced = self::forcedPaise();
        return $forced > 0 ? ($forced / 100) : $originalRupees;
    }

    /**
     * Test-only hosted payment page (rzp.io/rzp/...). Empty string when not configured.
     * Programmatic flows (plan unlock, Direct GPay) should redirect here if set —
     * the in-page Razorpay checkout's UPI QR doesn't work without a webhook, so a
     * Payment Page is the reliable test path.
     */
    public static function testPaymentLink(): string
    {
        return (string) config('services.razorpay.test_payment_link', '');
    }

    public static function hasTestPaymentLink(): bool
    {
        return self::testPaymentLink() !== '';
    }
}

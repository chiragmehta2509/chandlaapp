<?php

namespace App\Services;

use App\Models\MarriageInvitation;
use App\Support\RazorpayPayerUser;
use Illuminate\Support\Facades\Log;

/**
 * Razorpay Payment Link dedicated to marriage invitations only (configure payment_link id from Razorpay Dashboard → Payment Links → copy plink_…).
 * Runs before PackPurchase in webhook when {@see config('marriage_invitations.razorpay_payment_link_id')} is set.
 */
final class MarriageInvitationRazorpayCompletion
{
    public static function tryApply(array $payment): bool
    {
        $plinkExpected = trim((string) config('marriage_invitations.razorpay_payment_link_id', ''));
        if ($plinkExpected === '') {
            return false;
        }

        $paymentLinkId = (string) ($payment['payment_link_id'] ?? '');
        if ($paymentLinkId === '' || $paymentLinkId !== $plinkExpected) {
            return false;
        }

        $amountPaise = (int) ($payment['amount'] ?? 0);
        $expectedPaise = (int) round((float) config('marriage_invitations.amount', 300) * 100);
        if ($amountPaise !== $expectedPaise || $expectedPaise <= 0) {
            return false;
        }

        $user = RazorpayPayerUser::findFromPayment($payment);
        if ($user === null) {
            Log::info('Marriage invitation Razorpay: no user match', [
                'pay_id' => $payment['id'] ?? null,
                'plink' => $paymentLinkId,
            ]);

            return false;
        }

        $now = now();
        $affected = MarriageInvitation::where('user_id', $user->id)->whereNull('paid_at')->update(['paid_at' => $now]);

        return $affected > 0;
    }
}

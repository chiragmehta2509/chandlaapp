<?php

namespace App\Services;

use App\Models\Event;
use App\Models\UPITransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DirectGpayUnlockRazorpayCompletion
{
    /**
     * Record completed direct GPay unlock for this event (idempotent by Razorpay payment id).
     */
    public static function applyIfNeeded(Event $event, int $userId, string $razorpayPaymentId, ?string $razorpayOrderId): bool
    {
        if (UPITransaction::query()->where('transaction_id', $razorpayPaymentId)->exists()) {
            return true;
        }

        $event->refresh();
        if ($event->hasDirectGpayQrUnlocked()) {
            return true;
        }

        if ((int) $event->user_id !== $userId) {
            Log::warning('Direct GPay unlock Razorpay: user mismatch', ['event_id' => $event->id, 'user_id' => $userId]);

            return false;
        }

        $amount = (float) config('services.direct_gpay_unlock.amount', 400);

        try {
            DB::transaction(function () use ($event, $userId, $razorpayPaymentId, $razorpayOrderId, $amount) {
                UPITransaction::create([
                    'user_id' => $userId,
                    'event_id' => $event->id,
                    'transaction_id' => $razorpayPaymentId,
                    'razorpay_order_id' => $razorpayOrderId,
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'amount' => $amount,
                    'status' => 'completed',
                    'payment_method' => 'upi',
                    'description' => 'Direct GPay QR unlock — ' . $event->title . ' (Razorpay)',
                    'paid_at' => now(),
                    'metadata' => [
                        'type' => 'direct_gpay_unlock',
                        'event_id' => $event->id,
                        'event_title' => $event->title,
                        'expected_amount' => $amount,
                        'source' => 'razorpay',
                    ],
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Direct GPay unlock Razorpay apply failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        return true;
    }
}

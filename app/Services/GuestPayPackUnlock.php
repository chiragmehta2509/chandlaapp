<?php

namespace App\Services;

use App\Models\Event;
use App\Models\UPITransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GuestPayPackUnlock
{
    /**
     * Apply ₹400 pack redemption: Direct GPay unlock for this event + unlimited chandla for this event (via metadata flag).
     */
    public static function applyFromPackCredit(Event $event, User $user): bool
    {
        $event->refresh();
        if ($event->hasDirectGpayQrUnlocked()) {
            return true;
        }

        if ((int) $event->user_id !== (int) $user->id) {
            return false;
        }

        $packInr = (float) config('packs.guest_pay_single.amount_inr', 400);
        $txnId = 'guest_pay_pack_' . $event->id . '_' . Str::lower(Str::random(12));

        try {
            DB::transaction(function () use ($event, $user, $packInr, $txnId) {
                UPITransaction::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'transaction_id' => $txnId,
                    'razorpay_order_id' => null,
                    'razorpay_payment_id' => null,
                    'amount' => $packInr,
                    'status' => 'completed',
                    'payment_method' => 'upi',
                    'description' => 'Guest Contribution single-event pack — ' . $event->title,
                    'paid_at' => now(),
                    'metadata' => [
                        'type' => 'direct_gpay_unlock',
                        'source' => 'guest_pay_single_pack',
                        'event_id' => $event->id,
                        'event_title' => $event->title,
                        'pack_amount_inr' => $packInr,
                    ],
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Guest Contribution unlock failed', [
                'event_id' => $event->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        return true;
    }
}

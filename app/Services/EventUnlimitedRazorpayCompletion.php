<?php

namespace App\Services;

use App\Models\Event;
use App\Models\UPITransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PlanActivatedMail;

class EventUnlimitedRazorpayCompletion
{
    /**
     * Mark event as unlimited + completed UPI row (for admin list / audit). Idempotent by payment id.
     * Runs referral reward same as admin “Mark completed” for plan payments.
     */
    public static function applyIfNeeded(Event $event, int $userId, string $razorpayPaymentId, ?string $razorpayOrderId): bool
    {
        if (UPITransaction::query()->where('transaction_id', $razorpayPaymentId)->exists()) {
            return true;
        }

        $event->refresh();
        if ($event->pricing_plan === 'unlimited' && $event->unlimited_purchased_at) {
            return true;
        }

        if ((int) $event->user_id !== $userId) {
            Log::warning('Event unlimited Razorpay: user mismatch', ['event_id' => $event->id, 'user_id' => $userId]);

            return false;
        }

        $amount = (float) ($event->unlimited_price ?? 500);

        try {
            DB::transaction(function () use ($event, $userId, $razorpayPaymentId, $razorpayOrderId, $amount) {
                $event->pricing_plan = 'unlimited';
                $event->unlimited_purchased_at = now();
                $event->save();

                UPITransaction::create([
                    'user_id' => $userId,
                    'event_id' => $event->id,
                    'transaction_id' => $razorpayPaymentId,
                    'razorpay_order_id' => $razorpayOrderId,
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'amount' => $amount,
                    'status' => 'completed',
                    'payment_method' => 'card',
                    'description' => 'Event plan upgrade — Unlimited (Razorpay)',
                    'paid_at' => now(),
                    'metadata' => [
                        'type' => 'plan',
                        'plan' => 'unlimited',
                        'event_id' => $event->id,
                        'source' => 'razorpay',
                    ],
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Event unlimited Razorpay apply failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        $event->refresh();
        $buyer = $event->user;
        
        if ($buyer && filter_var($buyer->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($buyer->email)->send(new PlanActivatedMail($event));
            } catch (\Exception $e) {
                Log::error('Failed to send PlanActivatedMail', ['error' => $e->getMessage(), 'user_id' => $buyer->id]);
            }
        }

        if ($buyer && $buyer->referred_by && $buyer->referral_rewarded_at === null) {
            $referrer = User::find($buyer->referred_by);
            if ($referrer) {
                $referrer->free_event_credits = ((int) $referrer->free_event_credits) + 1;
                $referrer->save();
                $buyer->referral_rewarded_at = now();
                $buyer->save();
            }
        }

        return true;
    }
}

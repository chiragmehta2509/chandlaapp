<?php

namespace App\Services;

use App\Models\MarriageInvitation;
use App\Models\PackPaymentReceipt;
use App\Support\RazorpayPayerUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class PackPurchaseRazorpayCompletion
{
    /**
     * Apply celebration (₹300), Guest Contribution single event (₹400), Host Plus Plan ledger (₹500), or Premium Host Plan (₹700).
     * ₹500 requires PACK_LEDGER_DUO_PAYMENT_LINK_ID to match payment payment_link_id (Find Partner also uses ₹500).
     */
    public static function tryApply(array $payment): bool
    {
        $paymentId = (string) ($payment['id'] ?? '');
        if ($paymentId === '') {
            return false;
        }

        if (PackPaymentReceipt::query()->where('razorpay_payment_id', $paymentId)->exists()) {
            return true;
        }

        $amountPaise = (int) ($payment['amount'] ?? 0);
        if ($amountPaise <= 0) {
            return false;
        }

        $celebrationPaise = (int) round((float) config('packs.celebration.amount_inr', 300) * 100);
        $guestPayPaise = (int) round((float) config('packs.guest_pay_single.amount_inr', 400) * 100);
        $ledgerPaise = (int) round((float) config('packs.ledger_duo.amount_inr', 500) * 100);
        $familyPaise = (int) round((float) config('packs.family.amount_inr', 600) * 100);
        $bundlePaise = (int) round((float) config('packs.premium_bundle.amount_inr', 700) * 100);
        $payPlink = (string) ($payment['payment_link_id'] ?? '');
        $ledgerPlink = trim((string) config('packs.ledger_duo.payment_link_id', ''));
        $guestPayPlink = trim((string) config('packs.guest_pay_single.payment_link_id', ''));
        $familyPlink = trim((string) config('packs.family.payment_link_id', ''));

        $packType = null;
        if ($amountPaise === $celebrationPaise) {
            $packType = 'celebration';
        } elseif ($amountPaise === $guestPayPaise) {
            if ($guestPayPlink !== '' && $payPlink !== $guestPayPlink) {
                return false;
            }
            $packType = 'guest_pay_single';
        } elseif ($amountPaise === $bundlePaise) {
            $packType = 'premium_bundle';
        } elseif ($amountPaise === $familyPaise) {
            // ₹600 Family Plan — disambiguate via payment_link_id if set (other plans may share ₹600).
            if ($familyPlink !== '' && $payPlink !== $familyPlink) {
                return false;
            }
            $packType = 'family';
        } elseif ($amountPaise === $ledgerPaise) {
            if ($ledgerPlink === '') {
                Log::warning('Pack Razorpay: ₹500 payment skipped — set PACK_LEDGER_DUO_PAYMENT_LINK_ID (plink_…) for Host Plus Plan.');

                return false;
            }
            if ($payPlink !== $ledgerPlink) {
                return false;
            }
            $packType = 'ledger_duo';
        } else {
            return false;
        }

        if (! empty($payment['order_id'])) {
            $key = (string) config('services.razorpay.key_id', '');
            $secret = (string) config('services.razorpay.key_secret', '');
            if ($key !== '' && $secret !== '') {
                try {
                    $api = new Api($key, $secret);
                    $ord = $api->order->fetch((string) $payment['order_id']);
                    $n = $ord['notes'] ?? [];
                    $ct = (string) ($n['chandla_type'] ?? '');
                    if (in_array($ct, ['event_unlimited', 'direct_gpay_unlock'], true)) {
                        return false;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Pack Razorpay: could not fetch order for exclusion check', [
                        'order_id' => $payment['order_id'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $user = RazorpayPayerUser::findFromPayment($payment);
        if ($user === null) {
            Log::info('Pack Razorpay: no user match', [
                'pay_id' => $paymentId,
                'amount_paise' => $amountPaise,
                'email' => $payment['email'] ?? null,
            ]);

            return false;
        }

        try {
            DB::transaction(function () use ($user, $packType, $paymentId, $amountPaise) {
                PackPaymentReceipt::create([
                    'user_id' => $user->id,
                    'pack_type' => $packType,
                    'razorpay_payment_id' => $paymentId,
                    'amount_paise' => $amountPaise,
                ]);

                $now = now();
                if ($packType === 'celebration') {
                    if ($user->celebration_pack_paid_at === null) {
                        $user->celebration_pack_paid_at = $now;
                    }
                    $user->save();

                    return;
                }

                if ($packType === 'ledger_duo') {
                    if ($user->ledger_duo_pack_paid_at === null) {
                        $user->ledger_duo_pack_paid_at = $now;
                    }
                    $user->save();

                    return;
                }

                if ($packType === 'family') {
                    if ($user->family_pack_paid_at === null) {
                        $user->family_pack_paid_at = $now;
                    }
                    // Family Plan also includes the Host Plus Plan ledger benefits.
                    if ($user->ledger_duo_pack_paid_at === null) {
                        $user->ledger_duo_pack_paid_at = $now;
                    }
                    $user->save();

                    return;
                }

                if ($packType === 'premium_bundle') {
                    if ($user->premium_bundle_paid_at === null) {
                        $user->premium_bundle_paid_at = $now;
                    }
                    if ($user->celebration_pack_paid_at === null) {
                        $user->celebration_pack_paid_at = $now;
                    }
                    // Premium Host Plan also includes Host Plus Plan ledger and Family Plan (full-access editors).
                    if ($user->ledger_duo_pack_paid_at === null) {
                        $user->ledger_duo_pack_paid_at = $now;
                    }
                    if ($user->family_pack_paid_at === null) {
                        $user->family_pack_paid_at = $now;
                    }
                    $user->save();
                    MarriageInvitation::where('user_id', $user->id)->whereNull('paid_at')->update(['paid_at' => $now]);

                    return;
                }

                if ($packType === 'guest_pay_single') {
                    $user->guest_pay_single_event_credits = (int) ($user->guest_pay_single_event_credits ?? 0) + 1;
                    $user->save();
                }
            });
        } catch (\Throwable $e) {
            Log::error('Pack Razorpay: apply failed', [
                'pay_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        return true;
    }
}

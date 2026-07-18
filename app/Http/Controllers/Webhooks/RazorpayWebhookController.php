<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\MarriageInvitation;
use App\Models\MatrimonialPlan;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\RazorpayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Unified Razorpay Webhook handler.
 *
 * Register at: POST /webhooks/razorpay-payments
 * Set RAZORPAY_WEBHOOK_SECRET in your .env + Razorpay Dashboard → Webhooks.
 *
 * Idempotent: every handler checks "already processed" before acting.
 */
class RazorpayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // ── 1. Verify webhook signature ───────────────────────────────────────
        $secret = (string) config('services.razorpay.webhook_secret', '');
        $sig    = $request->header('X-Razorpay-Signature', '');

        if ($secret !== '' && $sig !== '') {
            try {
                $rzp = new RazorpayService();
                $rzp->verifyWebhookSignature($request->getContent(), $sig, $secret);
            } catch (\Throwable $e) {
                Log::warning('Razorpay webhook signature verification failed', ['sig' => $sig]);
                return response()->json(['error' => 'Invalid signature.'], 400);
            }
        }

        // ── 2. Decode payload ────────────────────────────────────────────────
        $payload = $request->json()->all();
        $event   = $payload['event'] ?? '';
        $entity  = $payload['payload']['payment']['entity']  ?? null;
        $order   = $payload['payload']['order']['entity']    ?? null;
        $refund  = $payload['payload']['refund']['entity']   ?? null;

        Log::info("Razorpay webhook received: {$event}", [
            'order_id'   => $entity['order_id'] ?? ($order['id'] ?? null),
            'payment_id' => $entity['id'] ?? null,
        ]);

        // ── 3. Route to correct handler ───────────────────────────────────────
        match ($event) {
            'payment.authorized' => $this->onPaymentAuthorized($entity),
            'payment.captured'   => $this->onPaymentCaptured($entity),
            'payment.failed'     => $this->onPaymentFailed($entity),
            'order.paid'         => $this->onOrderPaid($order, $entity),
            'refund.created',
            'refund.processed'   => $this->onRefund($refund, $entity),
            default              => null,
        };

        return response()->json(['status' => 'ok']);
    }

    // ── Event handlers ────────────────────────────────────────────────────────

    private function onPaymentAuthorized(array $payment): void
    {
        $orderId = $payment['order_id'] ?? null;
        if (!$orderId) return;

        $txn = PaymentTransaction::where('razorpay_order_id', $orderId)->first();
        if (!$txn) return;

        // Only move from pending → processing (idempotent)
        if ($txn->status === PaymentTransaction::STATUS_PENDING) {
            $txn->update(['status' => PaymentTransaction::STATUS_PROCESSING]);
        }
    }

    private function onPaymentCaptured(array $payment): void
    {
        $orderId   = $payment['order_id']   ?? null;
        $paymentId = $payment['id']          ?? null;

        if (!$orderId || !$paymentId) return;

        $txn = PaymentTransaction::where('razorpay_order_id', $orderId)->first();
        if (!$txn) {
            Log::warning("Webhook payment.captured: no transaction found for order {$orderId}");
            return;
        }

        // Idempotency: already processed
        if ($txn->isSuccess()) {
            Log::info("Webhook payment.captured: already success, skipping", ['order_id' => $orderId]);
            return;
        }

        try {
            DB::transaction(function () use ($txn, $payment, $paymentId) {
                $txn->update([
                    'razorpay_payment_id' => $paymentId,
                    'razorpay_signature'  => null, // webhook doesn't provide signature
                    'payment_method'      => $payment['method'] ?? null,
                    'status'              => PaymentTransaction::STATUS_SUCCESS,
                    'gateway_response'    => $this->safeGatewayResponse($payment),
                    'paid_at'             => now(),
                ]);

                $this->activatePackFromTxn($txn, $paymentId);
            });
        } catch (\Throwable $e) {
            Log::error("Webhook payment.captured: activation failed for order {$orderId}", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function onPaymentFailed(array $payment): void
    {
        $orderId = $payment['order_id'] ?? null;
        if (!$orderId) return;

        $txn = PaymentTransaction::where('razorpay_order_id', $orderId)->first();
        if (!$txn) return;

        // Don't overwrite a success
        if ($txn->isSuccess()) return;

        $reason = ($payment['error_code'] ?? '') . ' ' . ($payment['error_description'] ?? '');

        $txn->update([
            'status'           => PaymentTransaction::STATUS_FAILED,
            'failure_reason'   => trim($reason) ?: 'Payment failed',
            'gateway_response' => $this->safeGatewayResponse($payment),
        ]);
    }

    private function onOrderPaid(array $order, ?array $payment): void
    {
        $orderId = $order['id'] ?? null;
        if (!$orderId) return;

        $txn = PaymentTransaction::where('razorpay_order_id', $orderId)->first();
        if (!$txn || $txn->isSuccess()) return;

        $paymentId = $payment['id'] ?? null;

        try {
            DB::transaction(function () use ($txn, $order, $payment, $paymentId) {
                $txn->update([
                    'razorpay_payment_id' => $paymentId,
                    'payment_method'      => $payment['method'] ?? null,
                    'status'              => PaymentTransaction::STATUS_SUCCESS,
                    'gateway_response'    => $this->safeGatewayResponse($order),
                    'paid_at'             => now(),
                ]);

                $this->activatePackFromTxn($txn, (string) $paymentId);
            });
        } catch (\Throwable $e) {
            Log::error("Webhook order.paid: activation failed for order {$orderId}", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function onRefund(?array $refund, ?array $payment): void
    {
        if (!$refund) return;

        $paymentId = $refund['payment_id'] ?? ($payment['id'] ?? null);
        if (!$paymentId) return;

        PaymentTransaction::where('razorpay_payment_id', $paymentId)
            ->where('status', PaymentTransaction::STATUS_SUCCESS)
            ->update([
                'status'           => PaymentTransaction::STATUS_REFUNDED,
                'gateway_response' => $this->safeGatewayResponse($refund),
            ]);
    }

    // ── Package activation ────────────────────────────────────────────────────

    private function activatePackFromTxn(PaymentTransaction $txn, string $paymentId): void
    {
        $userId     = $txn->user_id;
        $packageKey = $txn->package_key;
        $now        = now();

        /** @var User|null $owner */
        $owner = User::find($userId);
        if (!$owner) return;

        switch ($packageKey) {
            case PaymentTransaction::PKG_CELEBRATION:
                $owner->celebration_pack_paid_at ??= $now;
                $owner->save();
                break;

            case PaymentTransaction::PKG_LEDGER_DUO:
                $owner->ledger_duo_pack_paid_at ??= $now;
                $owner->save();
                break;

            case PaymentTransaction::PKG_FAMILY:
                $owner->family_pack_paid_at    ??= $now;
                $owner->ledger_duo_pack_paid_at ??= $now;
                $owner->save();
                break;

            case PaymentTransaction::PKG_PREMIUM_BUNDLE:
                $owner->premium_bundle_paid_at  ??= $now;
                $owner->celebration_pack_paid_at ??= $now;
                $owner->ledger_duo_pack_paid_at  ??= $now;
                $owner->family_pack_paid_at      ??= $now;
                $owner->save();
                MarriageInvitation::where('user_id', $owner->id)->whereNull('paid_at')->update(['paid_at' => $now]);
                break;

            case PaymentTransaction::PKG_GUEST_PAY_SINGLE:
                $owner->guest_pay_single_event_credits = ((int) ($owner->guest_pay_single_event_credits ?? 0)) + 1;
                $owner->save();
                break;

            case PaymentTransaction::PKG_MARRIAGE_INVITATION:
                $invId = (int) ($txn->reference_id ?? 0);
                if ($invId > 0) {
                    MarriageInvitation::where('id', $invId)->where('user_id', $userId)->whereNull('paid_at')
                        ->update(['paid_at' => $now]);
                } else {
                    MarriageInvitation::where('user_id', $userId)->whereNull('paid_at')
                        ->orderByDesc('created_at')->limit(1)->update(['paid_at' => $now]);
                }
                break;

            case PaymentTransaction::PKG_MATRIMONIAL_200:
            case PaymentTransaction::PKG_MATRIMONIAL_500:
                $planType = $packageKey === PaymentTransaction::PKG_MATRIMONIAL_200 ? '200' : '500';
                $planDef  = config("matrimonial.plans.{$planType}");
                if ($planDef && !MatrimonialPlan::where('payment_id', $paymentId)->exists()) {
                    $start  = Carbon::today();
                    $expiry = (clone $start)->addMonths((int) ($planDef['months'] ?? 1))->endOfDay();
                    MatrimonialPlan::create([
                        'user_id'            => $userId,
                        'upi_transaction_id' => null,
                        'plan_type'          => $planType,
                        'price'              => (float) $planDef['price_inr'],
                        'start_date'         => $start,
                        'expiry_date'        => $expiry->toDateString(),
                        'payment_id'         => $paymentId,
                        'razorpay_order_id'  => $txn->razorpay_order_id,
                    ]);
                }
                break;

            default:
                Log::info("Webhook activatePackFromTxn: no activation handler for package '{$packageKey}'");
                break;
        }

        // Backward-compat: write PackPaymentReceipt for pack purchases
        if (in_array($packageKey, [
            PaymentTransaction::PKG_CELEBRATION,
            PaymentTransaction::PKG_LEDGER_DUO,
            PaymentTransaction::PKG_FAMILY,
            PaymentTransaction::PKG_PREMIUM_BUNDLE,
            PaymentTransaction::PKG_GUEST_PAY_SINGLE,
        ], true)) {
            \App\Models\PackPaymentReceipt::firstOrCreate(
                ['razorpay_payment_id' => $paymentId],
                [
                    'user_id'      => $userId,
                    'pack_type'    => $packageKey,
                    'amount_paise' => (int) round((float) $txn->amount_inr * 100),
                ]
            );
        }
    }

    private function safeGatewayResponse(array $data): array
    {
        // Store only safe, non-sensitive fields
        $keep = ['id', 'entity', 'status', 'amount', 'currency', 'method', 'order_id',
                 'payment_id', 'captured', 'description', 'error_code', 'error_description',
                 'created_at', 'acquirer_data'];
        return array_intersect_key($data, array_flip($keep));
    }
}

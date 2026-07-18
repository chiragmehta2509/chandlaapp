<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\MatrimonialPlan;
use App\Models\User;
use App\Services\DirectGpayUnlockRazorpayCompletion;
use App\Services\EventUnlimitedRazorpayCompletion;
use App\Services\MarriageInvitationRazorpayCompletion;
use App\Services\PackPurchaseRazorpayCompletion;
use App\Support\RazorpayPayerUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class MatrimonialRazorpayWebhookController extends Controller
{
    /**
     * Auto-activate Find Partner (matrimonial) plan from Razorpay Payment Link or payment events.
     * Set RAZORPAY_WEBHOOK_SECRET in .env to the secret shown in Razorpay Dashboard → Webhooks.
     * Subscribed events: at least payment.captured; payment_link.paid is also handled if present in payload.
     */
    public function __invoke(Request $request): Response
    {
        $secret = (string) config('services.razorpay.webhook_secret', '');
        if ($secret === '') {
            Log::warning('Razorpay webhook received but RAZORPAY_WEBHOOK_SECRET is not set.');

            return response('Webhook secret not configured', 503);
        }

        $body = $request->getContent();
        $sig = (string) $request->header('X-Razorpay-Signature', '');

        $expected = hash_hmac('sha256', $body, $secret);
        if (! hash_equals($expected, $sig)) {
            Log::warning('Razorpay webhook invalid signature.');

            return response('Invalid signature', 400);
        }

        $json = json_decode($body, true);
        if (! is_array($json)) {
            return response('Invalid JSON', 400);
        }

        $event = (string) ($json['event'] ?? '');
        if (! in_array($event, [
            'payment.captured',
            'payment.authorized',
            'order.paid',
            'payment_link.paid',
        ], true)) {
            return response('Ignored', 200);
        }

        $payment = $this->extractPaymentEntity($json);
        if (! is_array($payment) || empty($payment['id'])) {
            return response('No payment in payload', 200);
        }

        if ($this->tryCompleteDirectGpayUnlockFromOrder($payment)) {
            return response('OK', 200);
        }

        if ($this->tryCompleteEventUnlimitedFromOrder($payment)) {
            return response('OK', 200);
        }

        if (MarriageInvitationRazorpayCompletion::tryApply($payment)) {
            return response('OK', 200);
        }

        if (PackPurchaseRazorpayCompletion::tryApply($payment)) {
            return response('OK', 200);
        }

        $this->processMatrimonialPayment($payment, $event);

        return response('OK', 200);
    }

    /**
     * Direct GPay QR unlock (per event) — Order API notes.
     */
    private function tryCompleteDirectGpayUnlockFromOrder(array $payment): bool
    {
        $orderId = $payment['order_id'] ?? null;
        if ($orderId === null || $orderId === '') {
            return false;
        }

        $key = (string) config('services.razorpay.key_id', '');
        $secret = (string) config('services.razorpay.key_secret', '');
        if ($key === '' || $secret === '') {
            return false;
        }

        try {
            $api = new Api($key, $secret);
            $order = $api->order->fetch($orderId);
        } catch (\Throwable $e) {
            Log::warning('Razorpay webhook: could not fetch order (direct gpay)', ['order_id' => $orderId, 'error' => $e->getMessage()]);

            return false;
        }

        $notes = $order['notes'] ?? [];
        if (($notes['chandla_type'] ?? '') !== 'direct_gpay_unlock') {
            return false;
        }

        $eventId = (int) ($notes['event_id'] ?? 0);
        $userId = (int) ($notes['user_id'] ?? 0);
        if ($eventId < 1 || $userId < 1) {
            return false;
        }

        $event = Event::find($eventId);
        if (! $event || (int) $event->user_id !== $userId) {
            return false;
        }

        $expectedPaise = (int) round((float) config('services.direct_gpay_unlock.amount', 400) * 100);
        if ((int) ($order['amount'] ?? 0) !== $expectedPaise) {
            return false;
        }

        $paymentId = (string) $payment['id'];

        return DirectGpayUnlockRazorpayCompletion::applyIfNeeded($event, $userId, $paymentId, (string) $orderId);
    }

    /**
     * Razorpay Order API (event plan page) includes order_id + notes on the order.
     */
    private function tryCompleteEventUnlimitedFromOrder(array $payment): bool
    {
        $orderId = $payment['order_id'] ?? null;
        if ($orderId === null || $orderId === '') {
            return false;
        }

        $key = (string) config('services.razorpay.key_id', '');
        $secret = (string) config('services.razorpay.key_secret', '');
        if ($key === '' || $secret === '') {
            return false;
        }

        try {
            $api = new Api($key, $secret);
            $order = $api->order->fetch($orderId);
        } catch (\Throwable $e) {
            Log::warning('Razorpay webhook: could not fetch order', ['order_id' => $orderId, 'error' => $e->getMessage()]);

            return false;
        }

        $notes = $order['notes'] ?? [];
        if (($notes['chandla_type'] ?? '') !== 'event_unlimited') {
            return false;
        }

        $eventId = (int) ($notes['event_id'] ?? 0);
        $userId = (int) ($notes['user_id'] ?? 0);
        if ($eventId < 1 || $userId < 1) {
            return false;
        }

        $event = Event::find($eventId);
        if (! $event || (int) $event->user_id !== $userId) {
            return false;
        }

        $expectedPaise = (int) round((float) ($event->unlimited_price ?? 500) * 100);
        if ((int) ($order['amount'] ?? 0) !== $expectedPaise) {
            return false;
        }

        $paymentId = (string) $payment['id'];

        return EventUnlimitedRazorpayCompletion::applyIfNeeded($event, $userId, $paymentId, (string) $orderId);
    }

    private function extractPaymentEntity(array $json): ?array
    {
        $candidates = [
            data_get($json, 'payload.payment.entity'),
            data_get($json, 'payload.payment'),
        ];
        foreach ($candidates as $p) {
            if (is_array($p) && ! empty($p['id'])) {
                return $p;
            }
        }

        return null;
    }

    private function processMatrimonialPayment(array $payment, string $event): void
    {
        $paymentId = (string) $payment['id'];
        if (MatrimonialPlan::query()->where('payment_id', $paymentId)->exists()) {
            return;
        }

        $payPlink = (string) ($payment['payment_link_id'] ?? '');
        $ledgerPlink = trim((string) config('packs.ledger_duo.payment_link_id', ''));
        $guestPayPlink = trim((string) config('packs.guest_pay_single.payment_link_id', ''));
        if ($ledgerPlink !== '' && $payPlink === $ledgerPlink) {
            return;
        }
        if ($guestPayPlink !== '' && $payPlink === $guestPayPlink) {
            return;
        }

        // Marriage invitation Payment Link — handled by webhook / celebration flow
        $marriagePlink = trim((string) config('marriage_invitations.razorpay_payment_link_id', ''));
        if ($marriagePlink !== '' && $payPlink === $marriagePlink) {
            return;
        }

        // Do not attribute Order API payments (event unlimited, etc.) to matrimonial by amount.
        if (! empty($payment['order_id'])) {
            $secret = (string) config('services.razorpay.key_secret', '');
            if ($secret !== '') {
                try {
                    $api = new Api((string) config('services.razorpay.key_id'), $secret);
                    $ord = $api->order->fetch((string) $payment['order_id']);
                    $n = $ord['notes'] ?? [];
                    $ct = (string) ($n['chandla_type'] ?? '');
                    if ($ct === 'event_unlimited' || $ct === 'direct_gpay_unlock') {
                        return;
                    }
                } catch (\Throwable $e) {
                    // ignore; continue to matrimonial only if amount matches and no wrong order
                }
            }
        }

        $amountPaise = (int) ($payment['amount'] ?? 0);
        if ($amountPaise <= 0) {
            return;
        }

        $rupees = $amountPaise / 100.0;
        $planKey = null;
        $planDef = null;
        foreach (config('matrimonial.plans', []) as $key => $def) {
            $expected = (float) ($def['price_inr'] ?? 0);
            if (abs($rupees - $expected) < 0.01) {
                $planKey = (string) $key;
                $planDef = $def;
                break;
            }
        }
        if ($planKey === null || $planDef === null) {
            return;
        }

        $user = RazorpayPayerUser::findFromPayment($payment);
        if ($user === null) {
            Log::info('Razorpay matrimonial: no user match for payment', [
                'pay_id' => $paymentId,
                'email' => $payment['email'] ?? null,
                'contact' => $payment['contact'] ?? null,
            ]);

            return;
        }

        $start = Carbon::today();
        $months = (int) ($planDef['months'] ?? 1);
        if ($months < 1) {
            $months = 1;
        }
        $expiry = (clone $start)->addMonths($months)->endOfDay();

        try {
            MatrimonialPlan::create([
                'user_id' => $user->id,
                'upi_transaction_id' => null,
                'plan_type' => $planKey,
                'price' => (float) $planDef['price_inr'],
                'start_date' => $start,
                'expiry_date' => $expiry->toDateString(),
                'payment_id' => $paymentId,
                'razorpay_order_id' => isset($payment['order_id']) ? (string) $payment['order_id'] : null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Razorpay matrimonial: failed to create plan', [
                'error' => $e->getMessage(),
                'pay_id' => $paymentId,
            ]);
        }
    }

}

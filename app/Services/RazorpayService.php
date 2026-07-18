<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Throwable;

class RazorpayService
{
    private Api $api;
    private string $keyId;
    private string $keySecret;

    public function __construct()
    {
        $this->keyId     = (string) config('services.razorpay.key_id', '');
        $this->keySecret = (string) config('services.razorpay.key_secret', '');

        if ($this->keyId === '' || $this->keySecret === '') {
            throw new \RuntimeException('Razorpay API keys are not configured. Set RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET in .env');
        }

        $this->api = new Api($this->keyId, $this->keySecret);
    }

    // ── Static factory that returns null if keys not set (safe for optional use) ─
    public static function make(): ?static
    {
        try {
            return new static();
        } catch (Throwable) {
            return null;
        }
    }

    public function getKeyId(): string
    {
        return $this->keyId;
    }

    public function isTestMode(): bool
    {
        return str_starts_with($this->keyId, 'rzp_test_');
    }

    /**
     * Create a fresh Razorpay Order. Every payment attempt must call this.
     * Never reuse order IDs.
     *
     * @param  int    $amountPaise  Amount in paise (INR × 100)
     * @param  string $receipt      Unique receipt string for this attempt
     * @param  array  $notes        Optional key→value notes (shown in dashboard)
     * @return array  Razorpay order array including 'id', 'amount', 'currency'
     */
    public function createOrder(int $amountPaise, string $receipt, array $notes = []): array
    {
        $amountPaise = $this->resolveTestAmount($amountPaise);

        $order = $this->api->order->create([
            'amount'          => $amountPaise,
            'currency'        => 'INR',
            'receipt'         => substr($receipt, 0, 40), // Razorpay max 40 chars
            'payment_capture' => 1,
            'notes'           => array_slice($notes, 0, 15), // Razorpay allows max 15
        ]);

        return $order->toArray();
    }

    /**
     * Verify Razorpay payment signature (HMAC-SHA256).
     * Throws SignatureVerificationError on failure.
     */
    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        $this->api->utility->verifyPaymentSignature([
            'razorpay_order_id'   => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature'  => $signature,
        ]);

        return true; // throws if invalid
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $body, string $signature, string $secret): bool
    {
        $this->api->utility->verifyWebhookSignature($body, $signature, $secret);
        return true;
    }

    /**
     * Fetch a payment object from Razorpay.
     */
    public function fetchPayment(string $paymentId): array
    {
        return $this->api->payment->fetch($paymentId)->toArray();
    }

    /**
     * Fetch an order object from Razorpay.
     */
    public function fetchOrder(string $orderId): array
    {
        return $this->api->order->fetch($orderId)->toArray();
    }

    /**
     * Resolve amount — in test mode with RAZORPAY_TEST_FORCE_PAISE set, override the amount.
     */
    public function resolveTestAmount(int $amountPaise): int
    {
        $forced = (int) config('services.razorpay.test_force_paise', 0);
        if ($forced > 0 && $this->isTestMode()) {
            return $forced;
        }
        return $amountPaise;
    }

    /**
     * Generate a unique receipt string.
     *
     * @param string $prefix  e.g. 'cel', 'mat', 'inv'
     * @param int    $userId
     */
    public static function generateReceipt(string $prefix, int $userId): string
    {
        return substr($prefix, 0, 3) . '_' . $userId . '_' . Str::lower(Str::random(8));
    }

    /**
     * Create a pending PaymentTransaction record before opening checkout.
     * This ensures we always have a record even if user abandons payment.
     */
    public function createPendingTransaction(
        int    $userId,
        string $packageKey,
        float  $amountInr,
        string $razorpayOrderId,
        ?string $referenceId = null,
        array   $metadata = []
    ): PaymentTransaction {
        return PaymentTransaction::create([
            'user_id'          => $userId,
            'package_key'      => $packageKey,
            'package_name'     => PaymentTransaction::packageName($packageKey) . ' ₹' . number_format($amountInr, 0),
            'amount_inr'       => $amountInr,
            'currency'         => 'INR',
            'razorpay_order_id' => $razorpayOrderId,
            'status'           => PaymentTransaction::STATUS_PENDING,
            'reference_id'     => $referenceId,
            'metadata'         => $metadata,
        ]);
    }

    /**
     * Mark a transaction successful after signature verification.
     */
    public function markSuccess(
        PaymentTransaction $txn,
        string $paymentId,
        string $signature,
        ?string $paymentMethod = null,
        array $gatewayResponse = []
    ): PaymentTransaction {
        $txn->update([
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature'  => $signature,
            'payment_method'      => $paymentMethod,
            'status'              => PaymentTransaction::STATUS_SUCCESS,
            'gateway_response'    => $gatewayResponse ?: null,
            'paid_at'             => now(),
        ]);

        return $txn->fresh();
    }

    /**
     * Mark a transaction failed.
     */
    public function markFailed(
        PaymentTransaction $txn,
        string $reason = '',
        array $gatewayResponse = []
    ): PaymentTransaction {
        $txn->update([
            'status'           => PaymentTransaction::STATUS_FAILED,
            'failure_reason'   => $reason ?: null,
            'gateway_response' => $gatewayResponse ?: null,
        ]);

        return $txn->fresh();
    }

    /**
     * Find a pending transaction by Razorpay order ID.
     */
    public function findPendingByOrderId(string $orderId): ?PaymentTransaction
    {
        return PaymentTransaction::where('razorpay_order_id', $orderId)
            ->whereIn('status', [PaymentTransaction::STATUS_PENDING, PaymentTransaction::STATUS_PROCESSING])
            ->first();
    }
}

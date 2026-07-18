<?php

namespace App\Services;

use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $api;

    public function __construct()
    {
        $keyId = config('services.razorpay.key_id');
        $keySecret = config('services.razorpay.key_secret');

        if ($keyId && $keySecret) {
            $this->api = new Api($keyId, $keySecret);
        } else {
            Log::warning('Razorpay credentials not configured');
        }
    }

    public function createOrder(array $params)
    {
        if (!$this->api) {
            throw new \Exception('Razorpay API not initialized');
        }

        try {
            $order = $this->api->order->create($params);
            return [
                'id' => $order['id'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
                'status' => $order['status'],
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay Order Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        if (!$this->api) {
            return false;
        }

        try {
            $attributes = [
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ];

            $this->api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (\Exception $e) {
            Log::error('Razorpay Signature Verification Error: ' . $e->getMessage());
            return false;
        }
    }

    public function refund(string $paymentId, float $amount)
    {
        if (!$this->api) {
            return false;
        }

        try {
            $refund = $this->api->payment->fetch($paymentId)->refund([
                'amount' => $amount * 100, // Convert to paise
            ]);

            return [
                'id' => $refund['id'],
                'amount' => $refund['amount'] / 100,
                'status' => $refund['status'],
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay Refund Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getPayment(string $paymentId)
    {
        if (!$this->api) {
            return null;
        }

        try {
            $payment = $this->api->payment->fetch($paymentId);
            return [
                'id' => $payment['id'],
                'amount' => $payment['amount'] / 100,
                'status' => $payment['status'],
                'method' => $payment['method'],
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay Get Payment Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getOrder(string $orderId)
    {
        if (!$this->api) {
            return null;
        }

        try {
            $order = $this->api->order->fetch($orderId);
            return [
                'id' => $order['id'],
                'amount' => $order['amount'] / 100,
                'status' => $order['status'],
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay Get Order Error: ' . $e->getMessage());
            return null;
        }
    }
}


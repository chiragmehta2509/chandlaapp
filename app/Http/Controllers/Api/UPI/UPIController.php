<?php

namespace App\Http\Controllers\Api\UPI;

use App\Http\Controllers\Controller;
use App\Models\UPITransaction;
use App\Models\Event;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UPIController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $userId = $request->user()->dataOwnerId();
        $transactions = UPITransaction::where('user_id', $userId)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function show(Request $request, $id)
    {
        $userId = $request->user()->dataOwnerId();
        $transaction = UPITransaction::where('user_id', $userId)
            ->with('event')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'event_id' => 'nullable|exists:events,id',
            'description' => 'nullable|string',
            'payment_method' => 'nullable|string|in:upi,card,netbanking,wallet',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->dataOwnerId();

        if ($request->event_id) {
            $event = Event::where('user_id', $userId)->findOrFail($request->event_id);
        }

        $transaction = UPITransaction::create([
            'user_id' => $userId,
            'event_id' => $request->event_id,
            'transaction_id' => 'TXN' . Str::random(16),
            'amount' => $request->amount,
            'status' => 'pending',
            'payment_method' => $request->payment_method ?? 'upi',
            'description' => $request->description,
        ]);

        // Create Razorpay order
        $order = $this->paymentService->createOrder([
            'amount' => $request->amount * 100, // Convert to paise
            'currency' => 'INR',
            'receipt' => $transaction->transaction_id,
        ]);

        $transaction->update([
            'razorpay_order_id' => $order['id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => [
                'transaction' => $transaction,
                'razorpay_order_id' => $order['id'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
            ]
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->dataOwnerId();
        $transaction = UPITransaction::where('user_id', $userId)
            ->where('razorpay_order_id', $request->razorpay_order_id)
            ->firstOrFail();

        // Verify signature
        $isValid = $this->paymentService->verifySignature(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        );

        if (!$isValid) {
            $transaction->update(['status' => 'failed']);
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed'
            ], 400);
        }

        $transaction->update([
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature,
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully',
            'data' => $transaction
        ]);
    }

    public function refund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|exists:upi_transactions,transaction_id',
            'amount' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->dataOwnerId();
        $transaction = UPITransaction::where('user_id', $userId)
            ->where('transaction_id', $request->transaction_id)
            ->where('status', 'completed')
            ->firstOrFail();

        $refundAmount = $request->amount ?? $transaction->amount;

        // Process refund via Razorpay
        $refund = $this->paymentService->refund($transaction->razorpay_payment_id, $refundAmount);

        if ($refund) {
            $transaction->update(['status' => 'refunded']);
            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'data' => $transaction
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Refund failed'
        ], 400);
    }

    public function getHistory(Request $request)
    {
        $userId = $request->user()->dataOwnerId();
        $transactions = UPITransaction::where('user_id', $userId)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function getStats(Request $request)
    {
        $userId = $request->user()->dataOwnerId();
        $transactionsQuery = UPITransaction::where('user_id', $userId);

        $stats = [
            'total_transactions' => (clone $transactionsQuery)->count(),
            'completed_transactions' => (clone $transactionsQuery)->completed()->count(),
            'pending_transactions' => (clone $transactionsQuery)->pending()->count(),
            'failed_transactions' => (clone $transactionsQuery)->failed()->count(),
            'total_amount' => (clone $transactionsQuery)->completed()->sum('amount'),
            'this_month_amount' => (clone $transactionsQuery)
                ->completed()
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

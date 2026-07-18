<?php

namespace App\Http\Controllers\Matrimonial;

use App\Http\Controllers\Controller;
use App\Models\MatrimonialPlan as MatrimonialPlanModel;
use App\Models\PaymentTransaction;
use App\Services\RazorpayService;
use App\Support\MatrimonialPlan;
use App\Support\RazorpayTestAmount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MatrimonialPlanController extends Controller
{
    public function showPlans()
    {
        $user = Auth::user();
        $active = MatrimonialPlan::activePlanFor($user->id);
        $plans = config('matrimonial.plans', []);

        $razorpay = RazorpayService::make();
        $keyId = $razorpay ? $razorpay->getKeyId() : null;

        return view('client.matrimonial.plans', compact('active', 'plans', 'keyId', 'user'));
    }

    /**
     * Optional: legacy Razorpay Orders API checkout (not used when only payment links are shown).
     */
    public function createOrder(Request $request)
    {
        $keys = array_keys(config('matrimonial.plans', []));
        $request->validate([
            'plan' => 'required|in:' . implode(',', $keys),
        ]);

        $planKey = $request->input('plan');
        $planDef = config("matrimonial.plans.{$planKey}");
        if (!$planDef) {
            abort(400);
        }

        $razorpay = RazorpayService::make();
        if (!$razorpay) {
            return response()->json(['message' => 'Payment gateway is not configured.'], 503);
        }

        $amountInr = (float) $planDef['price_inr'];
        $amountPaise = (int) round($amountInr * 100);
        $packageKey = 'matrimonial_' . $planKey;
        $receipt = RazorpayService::generateReceipt($packageKey, Auth::id());

        try {
            $order = $razorpay->createOrder($amountPaise, $receipt, [
                'user_id' => (string) Auth::id(),
                'plan'    => $planKey,
            ]);

            // Save pending transaction
            $razorpay->createPendingTransaction(
                userId:          Auth::id(),
                packageKey:      $packageKey,
                amountInr:       $amountInr,
                razorpayOrderId: $order['id'],
                metadata:        ['plan' => $planKey]
            );

            return response()->json([
                'order_id' => $order['id'],
                'amount'   => $razorpay->resolveTestAmount($amountPaise),
                'key_id'   => $razorpay->getKeyId(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Could not create payment order.'], 500);
        }
    }

    public function verify(Request $request)
    {
        $keys = array_keys(config('matrimonial.plans', []));
        $validated = $request->validate([
            'plan' => 'required|in:' . implode(',', $keys),
            'razorpay_order_id' => 'required|string|max:64',
            'razorpay_payment_id' => 'required|string|max:64',
            'razorpay_signature' => 'required|string|max:255',
        ]);

        $planDef = config("matrimonial.plans.{$validated['plan']}");
        if (!$planDef) {
            abort(400);
        }

        $razorpay = RazorpayService::make();
        if (!$razorpay) {
            return back()->withErrors(['payment' => 'Payment verification unavailable.']);
        }

        // Find the pending transaction
        $packageKey = 'matrimonial_' . $validated['plan'];
        $txn = PaymentTransaction::where('razorpay_order_id', $validated['razorpay_order_id'])
            ->where('user_id', Auth::id())
            ->first();

        if (MatrimonialPlanModel::where('payment_id', $validated['razorpay_payment_id'])->exists()) {
            if ($txn && !$txn->isSuccess()) {
                $razorpay->markSuccess($txn, $validated['razorpay_payment_id'], $validated['razorpay_signature']);
            }
            return redirect()->route('client.matrimonial.plans')->with('success', 'This payment was already applied.');
        }

        try {
            $fetched = $razorpay->fetchOrder($validated['razorpay_order_id']);
            $expectedPaise = $razorpay->resolveTestAmount((int) round((float) $planDef['price_inr'] * 100));
            if (isset($fetched['amount']) && (int) $fetched['amount'] !== $expectedPaise) {
                return back()->withErrors(['payment' => 'Order amount does not match the selected plan.']);
            }
        } catch (\Throwable $e) {
            return back()->withErrors(['payment' => 'Could not confirm your order with the payment provider.']);
        }

        try {
            $razorpay->verifySignature(
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature']
            );
        } catch (\Throwable $e) {
            if ($txn) {
                $razorpay->markFailed($txn, 'Signature verification failed: ' . $e->getMessage());
            }
            return back()->withErrors(['payment' => 'Payment verification failed. If money was debited, contact support with your UPI/transaction id.']);
        }

        // Fetch payment to get method
        $paymentData = [];
        $paymentMethod = null;
        try {
            $paymentData = $razorpay->fetchPayment($validated['razorpay_payment_id']);
            $paymentMethod = $paymentData['method'] ?? null;
        } catch (\Throwable) {}

        if (!$txn) {
            // Create transaction in case it didn't exist
            $txn = PaymentTransaction::create([
                'user_id' => Auth::id(),
                'package_key' => $packageKey,
                'package_name' => PaymentTransaction::packageName($packageKey) . ' ₹' . number_format($planDef['price_inr'], 0),
                'amount_inr' => (float) $planDef['price_inr'],
                'currency' => 'INR',
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'status' => PaymentTransaction::STATUS_PENDING,
            ]);
        }

        $razorpay->markSuccess($txn, $validated['razorpay_payment_id'], $validated['razorpay_signature'], $paymentMethod, $paymentData);

        $start = Carbon::today();
        $months = max(1, (int) ($planDef['months'] ?? 1));
        $expiry = (clone $start)->addMonths($months)->endOfDay();

        MatrimonialPlanModel::create([
            'user_id' => Auth::id(),
            'plan_type' => $validated['plan'],
            'price' => (float) $planDef['price_inr'],
            'start_date' => $start,
            'expiry_date' => $expiry->toDateString(),
            'payment_id' => $validated['razorpay_payment_id'],
            'razorpay_order_id' => $validated['razorpay_order_id'],
        ]);

        return redirect()
            ->route('client.matrimonial.index')
            ->with('success', 'Your plan is active. You can now view full profiles and send interests.');
    }
}

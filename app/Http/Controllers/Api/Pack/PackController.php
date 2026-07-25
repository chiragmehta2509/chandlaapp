<?php

namespace App\Http\Controllers\Api\Pack;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MarriageInvitation;
use App\Models\PaymentTransaction;
use App\Models\PackPaymentReceipt;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PackController extends Controller
{
    private static array $packMap = [
        'celebration'      => 'celebration',
        'host-duo'         => 'ledger_duo',
        'family'           => 'family',
        'bundle'           => 'premium_bundle',
        'guest-pay-single' => 'guest_pay_single',
        'professional'     => 'professional',
        'enterprise'       => 'enterprise',
    ];

    private function userHasPack(User $user, string $configKey): bool
    {
        if ($configKey === 'guest_pay_single') {
            return $user->planLevel() > 2;
        }

        $tierMap = [
            'celebration'    => 1,
            'ledger_duo'     => 3,
            'family'         => 4,
            'premium_bundle' => 5,
            'professional'   => 6,
            'enterprise'     => 7,
        ];

        $targetTier = $tierMap[$configKey] ?? 999;
        return $user->planLevel() >= $targetTier;
    }

    public function index(Request $request)
    {
        $packsConfig = config('packs');
        $packs = [];

        foreach (self::$packMap as $slug => $configKey) {
            $packConfig = $packsConfig[$configKey] ?? null;
            if ($packConfig) {
                $packs[] = [
                    'slug' => $slug,
                    'config_key' => $configKey,
                    'label' => $packConfig['label'] ?? '',
                    'amount_inr' => (float) ($packConfig['amount_inr'] ?? 0),
                    'description' => $packConfig['description'] ?? '',
                    'features' => $packConfig['features'] ?? [],
                    'min_level' => $packConfig['min_level'] ?? 1,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $packs
        ]);
    }

    public function createOrder(Request $request, string $slug)
    {
        $configKey = self::$packMap[$slug] ?? null;
        if (!$configKey) {
            return response()->json(['success' => false, 'message' => 'Unknown package.'], 400);
        }

        $packConfig = config("packs.{$configKey}");
        if (!$packConfig) {
            return response()->json(['success' => false, 'message' => 'Package not configured.'], 400);
        }

        /** @var User $user */
        $user = $request->user();

        if ($this->userHasPack($user, $configKey)) {
            return response()->json(['success' => false, 'message' => 'You already have this package active.'], 400);
        }

        $amountInr  = (float) ($packConfig['amount_inr'] ?? 0);
        $amountPaise = (int) round($amountInr * 100);

        try {
            $razorpay = new RazorpayService();
            $receipt  = RazorpayService::generateReceipt($configKey, $user->id);

            $order = $razorpay->createOrder($amountPaise, $receipt, [
                'user_id'    => (string) $user->dataOwnerId(),
                'user_email' => (string) ($user->email ?? ''),
                'pack'       => $configKey,
            ]);

            // Create a pending transaction
            $razorpay->createPendingTransaction(
                userId:      $user->dataOwnerId(),
                packageKey:  $configKey,
                amountInr:   $amountInr,
                razorpayOrderId: $order['id'],
                metadata: ['pack_label' => $packConfig['label'] ?? $configKey]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id'   => $order['id'],
                    'amount'     => $razorpay->resolveTestAmount($amountPaise),
                    'currency'   => 'INR',
                    'key_id'     => $razorpay->getKeyId(),
                    'user_name'  => (string) ($user->name ?? ''),
                    'user_email' => (string) ($user->email ?? ''),
                    'user_phone' => (string) ($user->phone ?? ''),
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('API PackController::createOrder failed', [
                'pack'  => $slug,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Could not create payment order.'], 500);
        }
    }

    public function verifyPayment(Request $request, string $slug)
    {
        $configKey = self::$packMap[$slug] ?? null;
        if (!$configKey) {
            return response()->json(['success' => false, 'message' => 'Unknown package.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'razorpay_order_id'   => 'required|string|max:64',
            'razorpay_payment_id' => 'required|string|max:64',
            'razorpay_signature'  => 'required|string|max:512',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        /** @var User $user */
        $user    = $request->user();
        $ownerId = $user->dataOwnerId();

        $txn = PaymentTransaction::where('razorpay_order_id', $request->razorpay_order_id)
            ->where('user_id', $ownerId)
            ->first();

        if (!$txn) {
            return response()->json(['success' => false, 'message' => 'Transaction record not found.'], 404);
        }

        if ($txn->isSuccess()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment already verified and applied.',
            ]);
        }

        try {
            $razorpay = new RazorpayService();

            $razorpay->verifySignature(
                $request->razorpay_order_id,
                $request->razorpay_payment_id,
                $request->razorpay_signature
            );

            $paymentData   = [];
            $paymentMethod = null;
            try {
                $paymentData   = $razorpay->fetchPayment($request->razorpay_payment_id);
                $paymentMethod = $paymentData['method'] ?? null;
            } catch (\Throwable $e) {
                // Non-fatal
            }

            $razorpay->markSuccess(
                $txn,
                $request->razorpay_payment_id,
                $request->razorpay_signature,
                $paymentMethod,
                array_intersect_key($paymentData, array_flip(['id', 'status', 'method', 'amount', 'order_id', 'captured', 'created_at']))
            );

            $this->activatePack($configKey, $ownerId, $request->razorpay_payment_id);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully and package activated.',
                'data' => $txn
            ]);
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            $txn->update([
                'status'         => PaymentTransaction::STATUS_FAILED,
                'failure_reason' => 'Signature verification failed: ' . $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Payment verification failed.'], 422);
        } catch (\Throwable $e) {
            Log::error('API PackController::verifyPayment failed', [
                'pack'     => $slug,
                'order_id' => $request->razorpay_order_id,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Payment verification error.'], 500);
        }
    }

    /** Public wrapper used by UserController::updatePlan */
    public function activatePackPublic(string $configKey, int $ownerId, string $paymentId): void
    {
        $this->activatePack($configKey, $ownerId, $paymentId);
    }

    private function activatePack(string $configKey, int $ownerId, string $paymentId): void
    {
        DB::transaction(function () use ($configKey, $ownerId, $paymentId) {
            /** @var User $owner */
            $owner = User::findOrFail($ownerId);
            $now   = now();

            switch ($configKey) {
                case 'celebration':
                    $owner->celebration_pack_paid_at ??= $now;
                    break;
                case 'ledger_duo':
                    $owner->ledger_duo_pack_paid_at ??= $now;
                    break;
                case 'family':
                    $owner->family_pack_paid_at   ??= $now;
                    $owner->ledger_duo_pack_paid_at ??= $now;
                    break;
                case 'premium_bundle':
                    $owner->premium_bundle_paid_at  ??= $now;
                    $owner->celebration_pack_paid_at ??= $now;
                    $owner->ledger_duo_pack_paid_at  ??= $now;
                    $owner->family_pack_paid_at      ??= $now;
                    MarriageInvitation::where('user_id', $owner->id)
                        ->whereNull('paid_at')
                        ->update(['paid_at' => $now]);
                    break;
                case 'guest_pay_single':
                    $owner->guest_pay_single_event_credits =
                        ((int) ($owner->guest_pay_single_event_credits ?? 0)) + 1;
                    break;
                case 'professional':
                    $owner->professional_pack_paid_at ??= $now;
                    $owner->premium_bundle_paid_at  ??= $now;
                    $owner->celebration_pack_paid_at ??= $now;
                    $owner->ledger_duo_pack_paid_at  ??= $now;
                    $owner->family_pack_paid_at      ??= $now;
                    MarriageInvitation::where('user_id', $owner->id)
                        ->whereNull('paid_at')
                        ->update(['paid_at' => $now]);
                    break;
                case 'enterprise':
                    $owner->enterprise_pack_paid_at ??= $now;
                    $owner->professional_pack_paid_at ??= $now;
                    $owner->premium_bundle_paid_at  ??= $now;
                    $owner->celebration_pack_paid_at ??= $now;
                    $owner->ledger_duo_pack_paid_at  ??= $now;
                    $owner->family_pack_paid_at      ??= $now;
                    MarriageInvitation::where('user_id', $owner->id)
                        ->whereNull('paid_at')
                        ->update(['paid_at' => $now]);
                    break;
            }

            $owner->save();

            $amountInr = (float) config("packs.{$configKey}.amount_inr", 0);
            PackPaymentReceipt::firstOrCreate(
                ['razorpay_payment_id' => $paymentId],
                [
                    'user_id'    => $ownerId,
                    'pack_type'  => $configKey,
                    'amount_paise' => (int) round($amountInr * 100),
                ]
            );
        });
    }
}

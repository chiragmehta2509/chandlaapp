<?php

namespace App\Http\Controllers\Api\Subscription;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PackPaymentReceipt;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Subscription API Controller
 *
 * Base URL: /api/v1/subscription
 *
 * Endpoints:
 *   GET  /api/v1/subscription              → current plan overview
 *   GET  /api/v1/subscription/plans        → all available plans/packs with pricing
 *   GET  /api/v1/subscription/history      → payment history for this user
 *   POST /api/v1/subscription/purchase     → create a Razorpay order for a plan
 *   POST /api/v1/subscription/verify       → verify Razorpay payment & activate plan
 *   POST /api/v1/subscription/cancel       → cancel legacy subscription (sets status=expired)
 */
class SubscriptionController extends Controller
{
    // ── slug → internal config key map ───────────────────────────────────────
    private static array $packMap = [
        'celebration'      => 'celebration',
        'host-duo'         => 'ledger_duo',
        'family'           => 'family',
        'bundle'           => 'premium_bundle',
        'guest-pay-single' => 'guest_pay_single',
        'professional'     => 'professional',
        'enterprise'       => 'enterprise',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/subscription
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns the authenticated user's full subscription / plan status.
     *
     * Response:
     * {
     *   success: true,
     *   data: {
     *     plan_level:         <int 0–7>,
     *     plan_name:          <string>,
     *     max_events:         <int>,
     *     max_family_editors: <int>,
     *     legacy_subscription: {
     *       status:     "free"|"premium"|"expired",
     *       expires_at: <datetime|null>,
     *       is_active:  <bool>
     *     },
     *     features: { ... },
     *     active_packs: [ { key, label, activated_at } ... ],
     *     guest_pay_credits: <int>
     *   }
     * }
     */
    public function current(Request $request)
    {
        $user        = $request->user();
        $level       = $user->planLevel();
        $levelNames  = config('packs.level_names', []);

        // Build active packs list
        $activePacks = [];
        $packDefs = [
            'celebration'    => ['label' => 'Celebration Pack',   'field' => 'celebration_pack_paid_at'],
            'ledger_duo'     => ['label' => 'Host Plus Plan',      'field' => 'ledger_duo_pack_paid_at'],
            'family'         => ['label' => 'Family Plan',         'field' => 'family_pack_paid_at'],
            'premium_bundle' => ['label' => 'Premium Host',        'field' => 'premium_bundle_paid_at'],
            'professional'   => ['label' => 'Professional',        'field' => 'professional_pack_paid_at'],
            'enterprise'     => ['label' => 'Enterprise',          'field' => 'enterprise_pack_paid_at'],
        ];

        foreach ($packDefs as $key => $def) {
            $paidAt = $user->{$def['field']};
            if ($paidAt !== null) {
                $activePacks[] = [
                    'key'          => $key,
                    'label'        => $def['label'],
                    'activated_at' => $paidAt,
                ];
            }
        }

        // Guest pay credits
        $guestCredits = (int) ($user->guest_pay_single_event_credits ?? 0);
        if ($guestCredits > 0) {
            $activePacks[] = [
                'key'     => 'guest_pay_single',
                'label'   => 'Guest Contribution',
                'credits' => $guestCredits,
            ];
        }

        // Legacy subscription
        $legacySub = [
            'status'     => $user->subscription_status ?? 'free',
            'expires_at' => $user->subscription_expires_at,
            'is_active'  => $user->subscription_status === 'premium'
                            && $user->subscription_expires_at
                            && $user->subscription_expires_at->isFuture(),
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'plan_level'          => $level,
                'plan_name'           => $levelNames[$level] ?? 'Free',
                'max_events'          => $user->maxEventsAllowed(),
                'max_family_editors'  => $user->maxFamilyEditorsAllowed(),
                'legacy_subscription' => $legacySub,
                'features'            => [
                    'celebration_pack'       => $user->hasCelebrationPackAccess(),
                    'direct_gpay_qr'         => $user->hasDirectGpayQrUnlocked(),
                    'unlimited_chandla'      => $user->hasLedgerUnlimitedChandla(),
                    'premium_chandla_bundle' => $user->hasPremiumChandlaBundle(),
                    'advanced_analytics'     => $user->hasAdvancedAnalytics(),
                    'can_add_family_editors' => $user->canAddFamilyEditors(),
                ],
                'active_packs'        => $activePacks,
                'guest_pay_credits'   => $guestCredits,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/subscription/plans
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns all available subscription plans with pricing and features.
     * Marks which ones the current user already has.
     *
     * Response:
     * {
     *   success: true,
     *   data: [
     *     {
     *       slug, config_key, label, amount_inr, description, features[],
     *       level, already_owned: bool
     *     }, ...
     *   ]
     * }
     */
    public function plans(Request $request)
    {
        $user        = $request->user();
        $packsConfig = config('packs', []);
        $plans       = [];

        foreach (self::$packMap as $slug => $configKey) {
            $cfg = $packsConfig[$configKey] ?? null;
            if (!$cfg) {
                continue;
            }

            $tierMap = [
                'celebration'    => 1,
                'ledger_duo'     => 3,
                'family'         => 4,
                'premium_bundle' => 5,
                'professional'   => 6,
                'enterprise'     => 7,
                'guest_pay_single' => 2,
            ];

            $plans[] = [
                'slug'         => $slug,
                'config_key'   => $configKey,
                'label'        => $cfg['label']       ?? '',
                'amount_inr'   => (float) ($cfg['amount_inr']    ?? 0),
                'description'  => $cfg['description'] ?? '',
                'features'     => $cfg['features']    ?? [],
                'plan_level'   => $tierMap[$configKey] ?? 0,
                'already_owned'=> $this->userHasPack($user, $configKey),
            ];
        }

        // Sort by plan_level ascending
        usort($plans, fn($a, $b) => $a['plan_level'] <=> $b['plan_level']);

        return response()->json([
            'success' => true,
            'data'    => $plans,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/v1/subscription/history
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns the user's subscription/pack payment history.
     *
     * Query params:
     *   per_page  (default 15)
     *
     * Response:
     * {
     *   success: true,
     *   data: {
     *     transactions: [ paginated PaymentTransaction objects ],
     *     receipts:     [ PackPaymentReceipt objects ]
     *   }
     * }
     */
    public function history(Request $request)
    {
        $userId  = $request->user()->dataOwnerId();
        $perPage = (int) $request->get('per_page', 15);

        $transactions = PaymentTransaction::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $receipts = PackPaymentReceipt::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => [
                'id'                 => $r->id,
                'pack_type'          => $r->pack_type,
                'amount_inr'         => round($r->amount_paise / 100, 2),
                'razorpay_payment_id'=> $r->razorpay_payment_id,
                'purchased_at'       => $r->created_at,
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'transactions' => $transactions,
                'receipts'     => $receipts,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/v1/subscription/purchase
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Creates a Razorpay order for a given plan slug.
     *
     * Body:
     *   plan_slug  (required) – one of: celebration, host-duo, family, bundle,
     *                           guest-pay-single, professional, enterprise
     *
     * Response:
     * {
     *   success: true,
     *   data: {
     *     order_id, amount, currency, key_id,
     *     plan_slug, plan_label, amount_inr,
     *     user_name, user_email, user_phone
     *   }
     * }
     */
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_slug' => 'required|string|in:' . implode(',', array_keys(self::$packMap)),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $slug      = $request->plan_slug;
        $configKey = self::$packMap[$slug];
        $packCfg   = config("packs.{$configKey}");

        if (!$packCfg) {
            return response()->json(['success' => false, 'message' => 'Package not configured.'], 400);
        }

        /** @var User $user */
        $user = $request->user();

        if ($this->userHasPack($user, $configKey)) {
            return response()->json([
                'success' => false,
                'message' => 'You already own this plan.',
            ], 400);
        }

        $amountInr   = (float) ($packCfg['amount_inr'] ?? 0);
        $amountPaise = (int) round($amountInr * 100);

        try {
            $razorpay = new RazorpayService();
            $receipt  = RazorpayService::generateReceipt($configKey, $user->id);

            $order = $razorpay->createOrder($amountPaise, $receipt, [
                'user_id'    => (string) $user->dataOwnerId(),
                'user_email' => (string) ($user->email ?? ''),
                'pack'       => $configKey,
            ]);

            $razorpay->createPendingTransaction(
                userId:          $user->dataOwnerId(),
                packageKey:      $configKey,
                amountInr:       $amountInr,
                razorpayOrderId: $order['id'],
                metadata:        ['pack_label' => $packCfg['label'] ?? $configKey]
            );

            return response()->json([
                'success' => true,
                'data'    => [
                    'order_id'    => $order['id'],
                    'amount'      => $razorpay->resolveTestAmount($amountPaise),
                    'currency'    => 'INR',
                    'key_id'      => $razorpay->getKeyId(),
                    'plan_slug'   => $slug,
                    'plan_label'  => $packCfg['label'] ?? $configKey,
                    'amount_inr'  => $amountInr,
                    'user_name'   => (string) ($user->name  ?? ''),
                    'user_email'  => (string) ($user->email ?? ''),
                    'user_phone'  => (string) ($user->phone ?? ''),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('SubscriptionController::purchase failed', [
                'slug'  => $slug,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Could not create payment order.'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/v1/subscription/verify
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Verifies a Razorpay payment, activates the plan, and logs the receipt.
     *
     * Body:
     *   razorpay_order_id   (required)
     *   razorpay_payment_id (required)
     *   razorpay_signature  (required)
     *
     * Response:
     * {
     *   success: true,
     *   message: "Plan activated successfully.",
     *   data: {
     *     plan_level, plan_name, receipt { ... }
     *   }
     * }
     */
    public function verify(Request $request)
    {
        if (!$request->has('razorpay_payment_id') && $request->has('payment_id')) {
            $request->merge(['razorpay_payment_id' => $request->payment_id]);
        }
        if (!$request->has('razorpay_order_id') && $request->has('order_id')) {
            $request->merge(['razorpay_order_id' => $request->order_id]);
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
                'errors'  => $validator->errors(),
            ], 422);
        }

        /** @var User $user */
        $user    = $request->user();
        $ownerId = $user->dataOwnerId();

        $txn = PaymentTransaction::where('razorpay_order_id', $request->razorpay_order_id)
            ->where('user_id', $ownerId)
            ->first();

        if (!$txn) {
            return response()->json(['success' => false, 'message' => 'Transaction not found.'], 404);
        }

        if ($txn->isSuccess()) {
            $freshUser = $user->fresh();
            $level     = $freshUser->planLevel();
            return response()->json([
                'success' => true,
                'message' => 'Payment already verified and plan is active.',
                'data'    => [
                    'plan_level' => $level,
                    'plan_name'  => (config('packs.level_names') ?? [])[$level] ?? 'Unknown',
                ],
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
            } catch (\Throwable) {
                // Non-fatal
            }

            $razorpay->markSuccess(
                $txn,
                $request->razorpay_payment_id,
                $request->razorpay_signature,
                $paymentMethod,
                array_intersect_key($paymentData, array_flip(['id', 'status', 'method', 'amount', 'order_id', 'captured', 'created_at']))
            );

            // Activate the pack
            $this->activatePack($txn->package_key, $ownerId, $request->razorpay_payment_id);

            $freshUser = $user->fresh();
            $level     = $freshUser->planLevel();

            ActivityLog::create([
                'user_id'    => $user->id,
                'action'     => 'subscription_activated',
                'model_type' => PaymentTransaction::class,
                'model_id'   => $txn->id,
                'new_values' => ['pack' => $txn->package_key, 'plan_level' => $level],
                'ip_address' => $request->ip(),
            ]);

            $receipt = PackPaymentReceipt::where('razorpay_payment_id', $request->razorpay_payment_id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Plan activated successfully.',
                'data'    => [
                    'plan_level' => $level,
                    'plan_name'  => (config('packs.level_names') ?? [])[$level] ?? 'Unknown',
                    'receipt'    => $receipt ? [
                        'id'          => $receipt->id,
                        'pack_type'   => $receipt->pack_type,
                        'amount_inr'  => round($receipt->amount_paise / 100, 2),
                        'payment_id'  => $receipt->razorpay_payment_id,
                        'issued_at'   => $receipt->created_at,
                    ] : null,
                ],
            ]);
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            $txn->update([
                'status'         => PaymentTransaction::STATUS_FAILED,
                'failure_reason' => 'Signature verification failed.',
            ]);
            return response()->json(['success' => false, 'message' => 'Payment signature verification failed.'], 422);
        } catch (\Throwable $e) {
            Log::error('SubscriptionController::verify failed', [
                'order_id' => $request->razorpay_order_id,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Plan activation failed. Contact support.'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/v1/subscription/cancel
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Cancels the legacy (subscription_status) subscription only.
     * Pack purchases are permanent and are not revoked by this endpoint.
     *
     * Response:
     * { success: true, message: "Subscription cancelled." }
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        $user->update(['subscription_status' => 'expired']);

        ActivityLog::create([
            'user_id'    => $user->id,
            'action'     => 'subscription_cancelled',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled. Your purchased packs remain active.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function userHasPack(User $user, string $configKey): bool
    {
        if ($configKey === 'guest_pay_single') {
            return $user->guest_pay_single_event_credits > 0;
        }

        $tierMap = [
            'celebration'    => 1,
            'ledger_duo'     => 3,
            'family'         => 4,
            'premium_bundle' => 5,
            'professional'   => 6,
            'enterprise'     => 7,
        ];

        return $user->planLevel() >= ($tierMap[$configKey] ?? 999);
    }

    private function activatePack(string $configKey, int $ownerId, string $paymentId): void
    {
        DB::transaction(function () use ($configKey, $ownerId, $paymentId) {
            /** @var User $owner */
            $owner = User::findOrFail($ownerId);
            $now   = now();

            switch ($configKey) {
                case 'celebration':
                    $owner->celebration_pack_paid_at   ??= $now;
                    break;
                case 'ledger_duo':
                    $owner->ledger_duo_pack_paid_at    ??= $now;
                    break;
                case 'family':
                    $owner->family_pack_paid_at        ??= $now;
                    $owner->ledger_duo_pack_paid_at    ??= $now;
                    break;
                case 'premium_bundle':
                    $owner->premium_bundle_paid_at     ??= $now;
                    $owner->celebration_pack_paid_at   ??= $now;
                    $owner->ledger_duo_pack_paid_at    ??= $now;
                    $owner->family_pack_paid_at        ??= $now;
                    break;
                case 'guest_pay_single':
                    $owner->guest_pay_single_event_credits =
                        ((int) ($owner->guest_pay_single_event_credits ?? 0)) + 1;
                    break;
                case 'professional':
                    $owner->professional_pack_paid_at  ??= $now;
                    $owner->premium_bundle_paid_at     ??= $now;
                    $owner->celebration_pack_paid_at   ??= $now;
                    $owner->ledger_duo_pack_paid_at    ??= $now;
                    $owner->family_pack_paid_at        ??= $now;
                    break;
                case 'enterprise':
                    $owner->enterprise_pack_paid_at    ??= $now;
                    $owner->professional_pack_paid_at  ??= $now;
                    $owner->premium_bundle_paid_at     ??= $now;
                    $owner->celebration_pack_paid_at   ??= $now;
                    $owner->ledger_duo_pack_paid_at    ??= $now;
                    $owner->family_pack_paid_at        ??= $now;
                    break;
            }

            $owner->save();

            $amountInr = (float) config("packs.{$configKey}.amount_inr", 0);
            PackPaymentReceipt::firstOrCreate(
                ['razorpay_payment_id' => $paymentId],
                [
                    'user_id'      => $ownerId,
                    'pack_type'    => $configKey,
                    'amount_paise' => (int) round($amountInr * 100),
                ]
            );
        });
    }
}

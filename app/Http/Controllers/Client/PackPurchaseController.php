<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\MarriageInvitation;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\RazorpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PackPurchaseController extends Controller
{
    /**
     * All valid purchasable pack keys and their config keys.
     */
    private static array $packMap = [
        'celebration'      => 'celebration',
        'host-duo'         => 'ledger_duo',
        'family'           => 'family',
        'bundle'           => 'premium_bundle',
        'guest-pay-single' => 'guest_pay_single',
        'professional'     => 'professional',
        'enterprise'       => 'enterprise',
    ];

    // ── OLD redirect routes → redirect to new checkout ────────────────────────

    public function celebrationRedirect(): RedirectResponse
    {
        return redirect()->route('client.packs.checkout', 'celebration');
    }

    public function bundleRedirect(): RedirectResponse
    {
        return redirect()->route('client.packs.checkout', 'bundle');
    }

    public function hostDuoRedirect(): RedirectResponse
    {
        return redirect()->route('client.packs.checkout', 'host-duo');
    }

    public function familyRedirect(): RedirectResponse
    {
        return redirect()->route('client.packs.checkout', 'family');
    }

    public function guestPaySingleRedirect(): RedirectResponse
    {
        return redirect()->route('client.packs.checkout', 'guest-pay-single');
    }

    public function professionalRedirect(): RedirectResponse
    {
        return redirect()->route('client.packs.checkout', 'professional');
    }

    public function enterpriseRedirect(): RedirectResponse
    {
        // Enterprise uses a contact form page instead of direct checkout
        return redirect()->route('client.packs.checkout', 'enterprise');
    }

    // ── NEW Razorpay Orders checkout flow ─────────────────────────────────────

    /**
     * Check if the user already owns this pack (or a higher one).
     */
    private function userHasPack(User $user, string $configKey): bool
    {
        if ($configKey === 'guest_pay_single') {
            return $user->planLevel() > 2; // Block only if they have a strictly higher plan (level 3+)
        }

        $tierMap = [
            'celebration' => 1,
            'ledger_duo' => 3,
            'family' => 4,
            'premium_bundle' => 5,
            'professional' => 6,
            'enterprise' => 7,
        ];

        $targetTier = $tierMap[$configKey] ?? 999;
        return $user->planLevel() >= $targetTier;
    }

    /**
     * Show the checkout page for a pack.
     * GET /client/packs/{pack}/checkout
     */
    public function showCheckout(string $pack): View|RedirectResponse
    {
        $configKey = self::$packMap[$pack] ?? null;
        if (!$configKey) {
            return redirect()->route('client.plans')->withErrors(['pack' => 'Unknown package.']);
        }

        $packConfig = config("packs.{$configKey}");
        if (!$packConfig) {
            return redirect()->route('client.plans')->withErrors(['pack' => 'Package not configured.']);
        }

        /** @var User $user */
        $user    = Auth::user();

        if ($this->userHasPack($user, $configKey)) {
            return redirect()->route('client.plans')->with('error', 'You already have this package (or a higher-tier plan) active.');
        }

        $amountInr = (float) ($packConfig['amount_inr'] ?? 0);

        $razorpay = RazorpayService::make();
        if (!$razorpay) {
            return redirect()->route('client.plans')->withErrors(['pack' => 'Payment gateway is not configured. Please contact support.']);
        }

        // Resolve payment page URLs for dual-button experience
        $isTestMode      = $razorpay->isTestMode();
        $livePaymentUrl  = (string) ($packConfig['live_payment_url'] ?? '');
        $testPaymentUrl  = (string) ($packConfig['test_payment_url'] ?? '');

        // Upgrade context: tell the user what plan they currently have
        $levelNames       = config('packs.level_names', []);
        $currentLevel     = $user->planLevel();
        $currentPlanName  = $levelNames[$currentLevel] ?? 'Starter Plan';
        $targetLevel      = (int) ($packConfig['min_level'] ?? 1);
        $targetPlanName   = $packConfig['label'] ?? ucfirst($configKey);
        $isUpgrade        = $currentLevel > 0 && $targetLevel > $currentLevel;

        return view('client.packs.checkout', [
            'pack'            => $pack,
            'configKey'       => $configKey,
            'packConfig'      => $packConfig,
            'amountInr'       => $amountInr,
            'user'            => $user,
            'razorpayKey'     => $razorpay->getKeyId(),
            'isTestMode'      => $isTestMode,
            'livePaymentUrl'  => $livePaymentUrl,
            'testPaymentUrl'  => $testPaymentUrl,
            'currentLevel'    => $currentLevel,
            'currentPlanName' => $currentPlanName,
            'targetLevel'     => $targetLevel,
            'targetPlanName'  => $targetPlanName,
            'isUpgrade'       => $isUpgrade,
        ]);
    }

    /**
     * Create a new Razorpay Order for a pack.
     * POST /client/packs/{pack}/order  (AJAX)
     */
    public function createOrder(Request $request, string $pack): JsonResponse
    {
        $configKey = self::$packMap[$pack] ?? null;
        if (!$configKey) {
            return response()->json(['error' => 'Unknown package.'], 400);
        }

        $packConfig = config("packs.{$configKey}");
        if (!$packConfig) {
            return response()->json(['error' => 'Package not configured.'], 400);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($this->userHasPack($user, $configKey)) {
            return response()->json(['error' => 'You already have this package active.'], 400);
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

            // Always create a pending transaction BEFORE the user pays
            $razorpay->createPendingTransaction(
                userId:      $user->dataOwnerId(),
                packageKey:  $configKey,
                amountInr:   $amountInr,
                razorpayOrderId: $order['id'],
                metadata: ['pack_label' => $packConfig['label'] ?? $configKey]
            );

            return response()->json([
                'order_id'   => $order['id'],
                'amount'     => $razorpay->resolveTestAmount($amountPaise),
                'currency'   => 'INR',
                'key_id'     => $razorpay->getKeyId(),
                'user_name'  => (string) ($user->name ?? ''),
                'user_email' => (string) ($user->email ?? ''),
                'user_phone' => (string) ($user->phone ?? ''),
            ]);
        } catch (\Throwable $e) {
            Log::error('PackPurchaseController::createOrder failed', [
                'pack'  => $pack,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Could not create payment order. Please try again.'], 500);
        }
    }

    /**
     * Verify Razorpay payment and activate the pack.
     * POST /client/packs/{pack}/verify
     */
    public function verifyPayment(Request $request, string $pack): JsonResponse
    {
        $configKey = self::$packMap[$pack] ?? null;
        if (!$configKey) {
            return response()->json(['error' => 'Unknown package.'], 400);
        }

        $validated = $request->validate([
            'razorpay_order_id'   => 'required|string|max:64',
            'razorpay_payment_id' => 'required|string|max:64',
            'razorpay_signature'  => 'required|string|max:512',
        ]);

        /** @var User $user */
        $user    = Auth::user();
        $ownerId = $user->dataOwnerId();

        // Find the pending transaction we created during order creation
        $txn = PaymentTransaction::where('razorpay_order_id', $validated['razorpay_order_id'])
            ->where('user_id', $ownerId)
            ->first();

        if (!$txn) {
            return response()->json(['error' => 'Transaction record not found.'], 404);
        }

        // Idempotency: already processed
        if ($txn->isSuccess()) {
            return response()->json([
                'success'      => true,
                'redirect_url' => route('client.packs.thanks'),
                'message'      => 'Payment already applied.',
            ]);
        }

        try {
            $razorpay = new RazorpayService();

            // Server-side signature verification — NEVER trust frontend
            $razorpay->verifySignature(
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature']
            );

            // Fetch payment details to get method
            $paymentData   = [];
            $paymentMethod = null;
            try {
                $paymentData   = $razorpay->fetchPayment($validated['razorpay_payment_id']);
                $paymentMethod = $paymentData['method'] ?? null;
            } catch (\Throwable) {
                // Non-fatal — we still have signature verification
            }

            // Mark transaction success
            $razorpay->markSuccess(
                $txn,
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature'],
                $paymentMethod,
                array_intersect_key($paymentData, array_flip(['id', 'status', 'method', 'amount', 'order_id', 'captured', 'created_at']))
            );

            // Activate the pack
            $this->activatePack($configKey, $ownerId, $validated['razorpay_payment_id']);

            return response()->json([
                'success'      => true,
                'redirect_url' => route('client.packs.thanks'),
            ]);
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            $txn->update([
                'status'         => PaymentTransaction::STATUS_FAILED,
                'failure_reason' => 'Signature verification failed: ' . $e->getMessage(),
            ]);
            return response()->json(['error' => 'Payment verification failed. If money was debited, contact support.'], 422);
        } catch (\Throwable $e) {
            Log::error('PackPurchaseController::verifyPayment failed', [
                'pack'     => $pack,
                'order_id' => $validated['razorpay_order_id'] ?? null,
                'error'    => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Payment verification error. Please contact support.'], 500);
        }
    }

    /**
     * Thanks / success page.
     * GET /client/packs/thanks
     */
    public function thanks(Request $request): View
    {
        return view('client.packs.thanks', [
            'appliedKind'     => null,
            'razorpayPaymentId' => null,
        ]);
    }

    // ── Pack activation logic ─────────────────────────────────────────────────

    /**
     * Activate the correct pack after a verified successful payment.
     * Uses ??= so a second purchase for the same pack simply records a new
     * transaction without resetting the original activation timestamp.
     */
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
                    // Unlock any unpaid marriage invitations
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
            \App\Models\PackPaymentReceipt::firstOrCreate(
                ['razorpay_payment_id' => $paymentId],
                [
                    'user_id'    => $ownerId,
                    'pack_type'  => $configKey,
                    'amount_paise' => (int) round($amountInr * 100),
                ]
            );
        });

        if (!empty($owner->phone)) {
            try {
                $packLabel = config("packs.{$configKey}.label", ucfirst($configKey));
                $amountInr = (float) config("packs.{$configKey}.amount_inr", 0);
                $validityDate = now()->addMonths(6)->format('d M Y');

                $waService = new \App\Services\WhatsAppService();
                $cleanPhone = preg_replace('/^\+?91/', '', $owner->phone);
                $waService->sendTemplateMessage(
                    to: '91' . $cleanPhone,
                    templateName: 'plan_purchase_confirmation',
                    languageCode: 'en',
                    components: [
                        [
                            'type' => 'body',
                            'parameters' => [
                                \App\Services\WhatsAppService::formatTextParameter($owner->name ?? 'User'),
                                \App\Services\WhatsAppService::formatTextParameter((string) $amountInr),
                                \App\Services\WhatsAppService::formatTextParameter($packLabel),
                                \App\Services\WhatsAppService::formatTextParameter($validityDate)
                            ]
                        ]
                    ]
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('WhatsApp plan_purchase_confirmation failed for pack', [
                    'user_id' => $ownerId,
                    'pack' => $configKey,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}

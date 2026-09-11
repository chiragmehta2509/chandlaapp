<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\AppleIapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * AppleWebhookController
 *
 * Handles Apple In-App Purchase (StoreKit 2) endpoints:
 *   - POST /api/v1/payments/apple/verify   (authenticated)
 *   - POST /api/v1/payments/apple/restore  (authenticated)
 *   - POST /api/v1/webhooks/apple/notifications  (public, unauthenticated)
 *
 * The webhook endpoint MUST always return HTTP 200 immediately. Apple will
 * retry with exponential backoff if it receives any other status code.
 */
class AppleWebhookController extends Controller
{
    public function __construct(private readonly AppleIapService $appleService)
    {
    }

    // ── Authenticated endpoints ────────────────────────────────────────────────

    /**
     * POST /api/v1/payments/apple/verify
     *
     * Called by the iOS app immediately after a successful StoreKit 2 purchase.
     * Verifies the transaction with Apple, activates the pack, and records the payment.
     *
     * Request body:
     *   - transaction_id          (required) StoreKit transactionId
     *   - product_id              (required) Apple product identifier
     *   - signed_transaction_info (optional) JWS from StoreKit (preferred for faster verification)
     */
    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_id'          => 'required|string|max:255',
            'product_id'              => 'required|string|max:255',
            'signed_transaction_info' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user   = $request->user();
            $result = $this->appleService->processPurchase(
                $user,
                $validated['transaction_id'],
                $validated['product_id'],
                $validated['signed_transaction_info'] ?? null
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Apple In-App Purchase verified and activated successfully.',
                'data'    => $result,
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Apple IAP Verification Failed', [
                'user_id'        => $request->user()?->id,
                'transaction_id' => $validated['transaction_id'] ?? null,
                'error'          => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Failed to verify Apple purchase. Please try again.',
            ], 422);
        }
    }

    /**
     * POST /api/v1/payments/apple/restore
     *
     * Called by the iOS app when the user taps "Restore Purchases".
     * Looks up an existing verified purchase by originalTransactionId and re-credits it.
     *
     * Request body:
     *   - original_transaction_id (required) Apple originalTransactionId from StoreKit
     */
    public function restore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'original_transaction_id' => 'required|string|max:255',
        ]);

        try {
            $result = $this->appleService->restorePurchase(
                $request->user(),
                $validated['original_transaction_id']
            );

            return response()->json([
                'success' => true,
                'message' => 'Purchases restored successfully.',
                'data'    => $result,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Apple IAP Restore Failed', [
                'user_id'                => $request->user()?->id,
                'original_transaction_id' => $validated['original_transaction_id'] ?? null,
                'error'                  => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Failed to restore purchases.',
            ], 422);
        }
    }

    // ── Apple Webhook (unauthenticated, public) ────────────────────────────────

    /**
     * POST /api/v1/webhooks/apple/notifications
     *
     * Receives App Store Server Notifications V2 from Apple.
     * Configure in App Store Connect → App Information → App Store Server Notifications.
     *
     * CRITICAL: Always responds with HTTP 200 immediately. Apple will retry on non-200.
     * Processing happens synchronously (or can be queued for async if needed).
     */
    public function webhook(Request $request): JsonResponse
    {
        $signedPayload = $request->input('signedPayload');

        if (!$signedPayload) {
            Log::warning('Apple Webhook: received request without signedPayload');
            // Still return 200 to prevent Apple from retrying a malformed request
            return response()->json(['status' => 'received'], 200);
        }

        // Process notification — service catches all exceptions internally
        $this->appleService->handleServerNotification($signedPayload);

        return response()->json(['status' => 'success'], 200);
    }
}

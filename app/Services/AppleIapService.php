<?php

namespace App\Services;

use App\Models\AppleWebhookLog;
use App\Models\MarriageInvitation;
use App\Models\PackPaymentReceipt;
use App\Models\PaymentTransaction;
use App\Models\User;
use AppStoreServerLibrary\AppStoreServerAPIClient;
use AppStoreServerLibrary\AppStoreServerAPIClient\APIException;
use AppStoreServerLibrary\Models\Environment;
use AppStoreServerLibrary\SignedDataVerifier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * AppleIapService
 *
 * Handles StoreKit 2 transaction verification, pack activation, purchase restore,
 * and App Store Server Notifications V2 webhook processing.
 *
 * Uses hoels/app-store-server-library-php (mirrors Apple's official SDK).
 *
 * Key design decisions:
 * - Sandbox fallback: if production API returns 4040010, retries against Sandbox (App Review support).
 * - Pack activation mirrors PackController::activatePack() exactly.
 * - All DB operations are wrapped in transactions.
 * - Apple webhook always returns 200 to prevent retries; all exceptions are caught internally.
 */
class AppleIapService
{
    // ── Apple error code: transaction belongs to sandbox ────────────────────────
    private const APPLE_ERR_SANDBOX_TXN = 4040010;

    // ── Slug → config_key map (mirrors PackController) ────────────────────────
    private static array $packMap = [
        'celebration'      => 'celebration',
        'host-duo'         => 'ledger_duo',
        'ledger_duo'       => 'ledger_duo',
        'family'           => 'family',
        'bundle'           => 'premium_bundle',
        'premium_bundle'   => 'premium_bundle',
        'guest-pay-single' => 'guest_pay_single',
        'guest_pay_single' => 'guest_pay_single',
        'professional'     => 'professional',
        'enterprise'       => 'enterprise',
    ];

    // ── Public API ─────────────────────────────────────────────────────────────

    /**
     * Verify, activate, and record an Apple IAP purchase.
     *
     * @throws \RuntimeException on validation failure
     */
    public function processPurchase(
        User $user,
        string $transactionId,
        string $productId,
        ?string $signedTransactionInfo = null
    ): array {
        // 1. Verify the transaction with Apple
        $txnData = $this->verifyAndDecodeTransaction($transactionId, $signedTransactionInfo);

        // 2. Validate bundle ID
        $this->validateBundleId($txnData);

        // 3. Map product ID → pack config key
        $configKey = $this->resolveConfigKey($productId);

        // 4. Idempotency: already processed by this user?
        $existingTxn = PaymentTransaction::where('apple_transaction_id', $transactionId)
            ->where('status', PaymentTransaction::STATUS_SUCCESS)
            ->first();

        if ($existingTxn) {
            if ((int) $existingTxn->user_id === (int) $user->dataOwnerId()) {
                return ['message' => 'Already activated.', 'pack' => $configKey];
            }
            Log::warning('Apple IAP: transactionId already linked to another user', [
                'transaction_id'     => $transactionId,
                'requesting_user_id' => $user->id,
                'owner_user_id'      => $existingTxn->user_id,
            ]);
            throw new \RuntimeException('This transaction has already been used by another account.');
        }

        // 5. Fraud guard: originalTransactionId across users
        $originalTxnId = $txnData['originalTransactionId'] ?? $transactionId;
        $this->guardOriginalTransactionId($originalTxnId, $user->dataOwnerId());

        // 6. DB transaction: record + activate
        $result = DB::transaction(function () use ($user, $transactionId, $originalTxnId, $configKey, $productId, $txnData) {
            $amountInr   = (float) config("packs.{$configKey}.amount_inr", 0);
            $environment = $txnData['environment'] ?? 'Sandbox';

            PaymentTransaction::create([
                'user_id'                      => $user->dataOwnerId(),
                'package_key'                  => $configKey,
                'package_name'                 => PaymentTransaction::packageName($configKey),
                'amount_inr'                   => $amountInr,
                'currency'                     => 'USD',
                'payment_gateway'              => PaymentTransaction::GATEWAY_APPLE_IAP,
                'apple_transaction_id'         => $transactionId,
                'apple_original_transaction_id' => $originalTxnId,
                'apple_environment'            => $environment,
                'payment_method'               => 'apple_iap',
                'status'                       => PaymentTransaction::STATUS_SUCCESS,
                'paid_at'                      => now(),
                'metadata'                     => [
                    'product_id'    => $productId,
                    'purchase_date' => $txnData['purchaseDate'] ?? null,
                    'expires_date'  => $txnData['expiresDate'] ?? null,
                ],
            ]);

            $this->activatePack($configKey, $user->dataOwnerId(), $transactionId);

            return [
                'pack'           => $configKey,
                'transaction_id' => $transactionId,
                'environment'    => $environment,
            ];
        });

        Log::info('Apple IAP: purchase activated', [
            'user_id'        => $user->id,
            'pack'           => $configKey,
            'transaction_id' => $transactionId,
        ]);

        return $result;
    }

    /**
     * Restore an existing purchase on a new device.
     *
     * @throws \RuntimeException if purchase not found or belongs to another user
     */
    public function restorePurchase(User $user, string $originalTransactionId): array
    {
        $txn = PaymentTransaction::where('apple_original_transaction_id', $originalTransactionId)
            ->where('status', PaymentTransaction::STATUS_SUCCESS)
            ->first();

        if (!$txn) {
            throw new \RuntimeException('No verified purchase found for this transaction ID.');
        }

        if ((int) $txn->user_id !== (int) $user->dataOwnerId()) {
            Log::warning('Apple IAP restore: originalTransactionId belongs to different user', [
                'original_txn_id'    => $originalTransactionId,
                'requesting_user_id' => $user->id,
                'owner_user_id'      => $txn->user_id,
            ]);
            throw new \RuntimeException('This purchase is linked to a different account.');
        }

        DB::transaction(function () use ($txn, $user) {
            $this->activatePack($txn->package_key, $user->dataOwnerId(), $txn->apple_transaction_id);
        });

        return [
            'pack'           => $txn->package_key,
            'transaction_id' => $txn->apple_transaction_id,
            'message'        => 'Purchase restored successfully.',
        ];
    }

    /**
     * Process an App Store Server Notification V2.
     * Always logs the event; never throws — caller returns 200 to Apple.
     */
    public function handleServerNotification(string $signedPayload): void
    {
        $notificationData = [];

        try {
            $notificationData = $this->decodeNotificationPayload($signedPayload);
        } catch (\Throwable $e) {
            Log::error('Apple Webhook: failed to decode signedPayload', ['error' => $e->getMessage()]);
            return;
        }

        $uuid    = $notificationData['notificationUUID'] ?? null;
        $type    = $notificationData['notificationType'] ?? 'UNKNOWN';
        $subtype = $notificationData['subtype']           ?? null;
        $txnData = $notificationData['transactionInfo']   ?? [];

        $log = AppleWebhookLog::firstOrNew(['notification_uuid' => $uuid ?? uniqid('apple_', true)]);

        if ($log->exists && $log->status === AppleWebhookLog::STATUS_PROCESSED) {
            $log->markIgnored('Duplicate notification UUID');
            return;
        }

        $log->fill([
            'notification_type' => $type,
            'subtype'           => $subtype,
            'payload'           => $notificationData,
            'status'            => AppleWebhookLog::STATUS_PENDING,
        ]);
        $log->save();

        try {
            $this->dispatchNotificationEvent($type, $subtype, $txnData);
            $log->markProcessed();
        } catch (\Throwable $e) {
            Log::error('Apple Webhook: event handling failed', [
                'type'  => $type,
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);
            $log->markFailed($e->getMessage());
        }
    }

    // ── Transaction Verification ───────────────────────────────────────────────

    /**
     * Verify a StoreKit 2 transaction.
     *
     * Priority:
     *   1. Decode the signedTransactionInfo provided by iOS app directly (fastest, no API call).
     *   2. Fall back to Apple App Store Server API call.
     *
     * Production → Sandbox fallback: if Apple returns 4040010 (sandbox tx on prod server),
     * automatically retries against Sandbox API. Required for Apple App Review.
     *
     * @return array Decoded transaction payload
     * @throws \RuntimeException
     */
    public function verifyAndDecodeTransaction(string $transactionId, ?string $signedTransactionInfo = null): array
    {
        // Prefer decoding the JWS the iOS app already has (no extra API call)
        if ($signedTransactionInfo) {
            try {
                return $this->decodeSignedTransaction($signedTransactionInfo);
            } catch (\Throwable $e) {
                Log::warning('Apple IAP: could not decode provided signedTransactionInfo, falling back to API', [
                    'transaction_id' => $transactionId,
                    'error'          => $e->getMessage(),
                ]);
            }
        }

        $configuredEnv = config('apple.environment', 'sandbox');

        try {
            return $this->fetchTransactionFromApi($transactionId, $configuredEnv);
        } catch (\RuntimeException $e) {
            // Production received a sandbox transaction (Apple Review traffic) → retry sandbox
            if ($configuredEnv === 'production' && str_contains($e->getMessage(), (string) self::APPLE_ERR_SANDBOX_TXN)) {
                Log::info('Apple IAP: prod received sandbox tx (App Review), retrying against Sandbox', [
                    'transaction_id' => $transactionId,
                ]);
                return $this->fetchTransactionFromApi($transactionId, 'sandbox');
            }
            throw $e;
        }
    }

    // ── Private: API & JWS helpers ────────────────────────────────────────────

    private function buildEnvironment(string $envString): Environment
    {
        return $envString === 'production' ? Environment::PRODUCTION : Environment::SANDBOX;
    }

    private function fetchTransactionFromApi(string $transactionId, string $environment): array
    {
        $privateKey = $this->loadPrivateKey();
        $keyId      = config('apple.key_id');
        $issuerId   = config('apple.issuer_id');
        $bundleId   = config('apple.bundle_id');
        $appAppleId = (int) config('apple.app_apple_id');
        $appleEnv   = $this->buildEnvironment($environment);

        $client = new AppStoreServerAPIClient(
            signingKey: $privateKey,
            keyId:      $keyId,
            issuerId:   $issuerId,
            bundleId:   $bundleId,
            environment: $appleEnv
        );

        try {
            $response = $client->getTransactionInfo($transactionId);
        } catch (APIException $e) {
            throw new \RuntimeException(
                "Apple API error [{$e->getApiError()}]: {$e->getMessage()}"
            );
        }

        // Verify the returned signed transaction
        $decoder = new SignedDataVerifier(
            rootCertificates: $this->loadAppleRootCerts(),
            enableOnlineChecks: false,  // disable OCSP online checks (requires internet on server)
            environment: $appleEnv,
            bundleId: $bundleId,
            appAppleId: $appAppleId
        );

        $txnInfo = $decoder->verifyAndDecodeSignedTransaction($response->getSignedTransactionInfo());

        return $this->transactionToArray($txnInfo);
    }

    private function decodeSignedTransaction(string $signedTransactionInfo): array
    {
        $bundleId   = config('apple.bundle_id');
        $appAppleId = (int) config('apple.app_apple_id');
        $appleEnv   = $this->buildEnvironment(config('apple.environment', 'sandbox'));

        $decoder = new SignedDataVerifier(
            rootCertificates: $this->loadAppleRootCerts(),
            enableOnlineChecks: false,
            environment: $appleEnv,
            bundleId: $bundleId,
            appAppleId: $appAppleId
        );

        $txnInfo = $decoder->verifyAndDecodeSignedTransaction($signedTransactionInfo);

        return $this->transactionToArray($txnInfo);
    }

    private function decodeNotificationPayload(string $signedPayload): array
    {
        $bundleId   = config('apple.bundle_id');
        $appAppleId = (int) config('apple.app_apple_id');
        $appleEnv   = $this->buildEnvironment(config('apple.environment', 'sandbox'));

        $decoder = new SignedDataVerifier(
            rootCertificates: $this->loadAppleRootCerts(),
            enableOnlineChecks: false,
            environment: $appleEnv,
            bundleId: $bundleId,
            appAppleId: $appAppleId
        );

        $notification = $decoder->verifyAndDecodeNotification($signedPayload);

        $txnData = [];
        $notificationData = $notification->getData();
        if ($notificationData && $notificationData->getSignedTransactionInfo()) {
            try {
                $txnData = $this->decodeSignedTransaction($notificationData->getSignedTransactionInfo());
            } catch (\Throwable $e) {
                Log::warning('Apple Webhook: could not decode nested signedTransactionInfo', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'notificationUUID' => $notification->getNotificationUUID(),
            'notificationType' => $notification->getNotificationType()?->getValue() ?? 'UNKNOWN',
            'subtype'          => $notification->getSubtype()?->getValue(),
            'transactionInfo'  => $txnData,
        ];
    }

    // ── Notification event dispatching ────────────────────────────────────────

    private function dispatchNotificationEvent(string $type, ?string $subtype, array $txnData): void
    {
        match ($type) {
            'SUBSCRIBED'                => $this->onSubscribed($txnData, $subtype),
            'DID_RENEW'                 => $this->onDidRenew($txnData),
            'EXPIRED',
            'DID_FAIL_TO_RENEW'        => $this->onExpired($txnData, $type),
            'REFUND',
            'REVOKE'                    => $this->onRefundOrRevoke($txnData, $type),
            'DID_CHANGE_RENEWAL_STATUS' => $this->onRenewalStatusChange($txnData),
            default => Log::info("Apple Webhook: unhandled notification type '{$type}'", ['subtype' => $subtype]),
        };
    }

    private function onSubscribed(array $txnData, ?string $subtype): void
    {
        // Safety net for cases where /verify was not called (e.g., missed API call)
        Log::info('Apple Webhook SUBSCRIBED received', [
            'original_txn_id' => $txnData['originalTransactionId'] ?? null,
            'subtype'         => $subtype,
        ]);
    }

    private function onDidRenew(array $txnData): void
    {
        // Current packs are one-time; log only
        Log::info('Apple Webhook DID_RENEW received (one-time packs; no action required)', [
            'original_txn_id' => $txnData['originalTransactionId'] ?? null,
        ]);
    }

    private function onExpired(array $txnData, string $type): void
    {
        Log::info("Apple Webhook {$type}: (one-time packs; no action required)", [
            'original_txn_id' => $txnData['originalTransactionId'] ?? null,
        ]);
    }

    private function onRefundOrRevoke(array $txnData, string $type): void
    {
        $originalTxnId = $txnData['originalTransactionId'] ?? null;
        if (!$originalTxnId) return;

        PaymentTransaction::where('apple_original_transaction_id', $originalTxnId)
            ->where('status', PaymentTransaction::STATUS_SUCCESS)
            ->update([
                'status'      => PaymentTransaction::STATUS_REFUNDED,
                'is_refunded' => true,
                'revoked_at'  => now(),
            ]);

        Log::info("Apple Webhook {$type}: transaction marked as refunded/revoked", [
            'original_txn_id' => $originalTxnId,
        ]);

        // Pack access is intentionally not revoked automatically. Admin review is preferred.
    }

    private function onRenewalStatusChange(array $txnData): void
    {
        Log::info('Apple Webhook DID_CHANGE_RENEWAL_STATUS (one-time packs; no action required)', [
            'original_txn_id' => $txnData['originalTransactionId'] ?? null,
        ]);
    }

    // ── Pack activation ────────────────────────────────────────────────────────

    /**
     * Credit the purchased pack to the user.
     * Mirrors PackController::activatePack() exactly, but writes apple_ prefixed receipt ID.
     */
    private function activatePack(string $configKey, int $ownerId, string $appleTransactionId): void
    {
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
                $owner->family_pack_paid_at    ??= $now;
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
                $owner->premium_bundle_paid_at    ??= $now;
                $owner->celebration_pack_paid_at  ??= $now;
                $owner->ledger_duo_pack_paid_at   ??= $now;
                $owner->family_pack_paid_at       ??= $now;
                MarriageInvitation::where('user_id', $owner->id)
                    ->whereNull('paid_at')
                    ->update(['paid_at' => $now]);
                break;
            case 'enterprise':
                $owner->enterprise_pack_paid_at   ??= $now;
                $owner->professional_pack_paid_at ??= $now;
                $owner->premium_bundle_paid_at    ??= $now;
                $owner->celebration_pack_paid_at  ??= $now;
                $owner->ledger_duo_pack_paid_at   ??= $now;
                $owner->family_pack_paid_at       ??= $now;
                MarriageInvitation::where('user_id', $owner->id)
                    ->whereNull('paid_at')
                    ->update(['paid_at' => $now]);
                break;
            default:
                Log::warning("Apple IAP activatePack: no handler for configKey '{$configKey}'");
                return;
        }

        $owner->save();

        // Write PackPaymentReceipt for backward compatibility with existing pack checks
        $amountInr = (float) config("packs.{$configKey}.amount_inr", 0);
        PackPaymentReceipt::firstOrCreate(
            ['razorpay_payment_id' => 'apple_' . $appleTransactionId],
            [
                'user_id'      => $ownerId,
                'pack_type'    => $configKey,
                'amount_paise' => (int) round($amountInr * 100),
            ]
        );
    }

    // ── Validation helpers ────────────────────────────────────────────────────

    private function validateBundleId(array $txnData): void
    {
        $expected = config('apple.bundle_id');
        $actual   = $txnData['bundleId'] ?? '';

        if ($expected && $actual && $actual !== $expected) {
            throw new \RuntimeException("Bundle ID mismatch: expected '{$expected}', got '{$actual}'.");
        }
    }

    /**
     * Resolve an Apple product ID to an internal config key.
     *
     * Lookup order:
     *   1. SubscriptionPack.apple_product_id in DB (exact match)
     *   2. Convention: productId ends with .<configKey> or .<slug>
     *
     * @throws \RuntimeException if no match found
     */
    private function resolveConfigKey(string $productId): string
    {
        // DB lookup
        if (Schema::hasColumn('subscription_packs', 'apple_product_id')) {
            $pack = \App\Models\SubscriptionPack::where('apple_product_id', $productId)->first();
            if ($pack) {
                $configKey = self::$packMap[$pack->slug] ?? $pack->slug;
                if ($configKey) {
                    return $configKey;
                }
            }
        }

        // Convention-based fallback
        foreach (self::$packMap as $slug => $configKey) {
            if (str_ends_with($productId, '.' . $configKey) || str_ends_with($productId, '.' . $slug)) {
                return $configKey;
            }
        }

        throw new \RuntimeException("Unknown Apple product ID: '{$productId}'. Please set apple_product_id in the subscription_packs table.");
    }

    private function guardOriginalTransactionId(string $originalTxnId, int $userId): void
    {
        $existing = PaymentTransaction::where('apple_original_transaction_id', $originalTxnId)
            ->where('status', PaymentTransaction::STATUS_SUCCESS)
            ->where('user_id', '!=', $userId)
            ->first();

        if ($existing) {
            Log::warning('Apple IAP fraud guard: originalTransactionId belongs to another user', [
                'original_txn_id'    => $originalTxnId,
                'requesting_user_id' => $userId,
                'owner_user_id'      => $existing->user_id,
            ]);
            throw new \RuntimeException('This Apple purchase is already associated with another account.');
        }
    }

    // ── Utility ───────────────────────────────────────────────────────────────

    private function loadPrivateKey(): string
    {
        $path = base_path(config('apple.private_key_path'));

        if (!file_exists($path)) {
            throw new \RuntimeException("Apple IAP private key not found at: {$path}");
        }

        return file_get_contents($path);
    }

    /**
     * Load Apple Root CA certificates in DER format.
     *
     * Apple's Root CAs are embedded here as base64-encoded DER.
     * These are the Apple Root CA – G3 and Apple Root CA certs.
     *
     * Source: https://www.apple.com/certificateauthority/
     * These are public certificates and safe to embed.
     */
    private function loadAppleRootCerts(): array
    {
        // Try loading from bundled cert files first (if you place them in storage/keys/)
        $certDir  = storage_path('keys/apple-root-certs');
        $certFiles = [];

        if (is_dir($certDir)) {
            $certFiles = glob($certDir . '/*.cer') ?: glob($certDir . '/*.der') ?: [];
        }

        if (!empty($certFiles)) {
            return array_map('file_get_contents', $certFiles);
        }

        // Embedded Apple Root CA – G3 (DER, base64-encoded)
        // Downloaded from: https://www.apple.com/certificateauthority/AppleRootCA-G3.cer
        $appleRootG3Base64 = 'MIICQzCCAcmgAwIBAgIILcX8iNLFS5UwCgYIKoZIzj0EAwMwZzEbMBkGA1UEAwwS' .
            'QXBwbGUgUm9vdCBDQSAtIEczMSYwJAYDVQQLDB1BcHBsZSBDZXJ0aWZpY2F0aW9u' .
            'IEF1dGhvcml0eTETMBEGA1UECgwKQXBwbGUgSW5jLjELMAkGA1UEBhMCVVMwHhcN' .
            'MTQwNDMwMTgxOTA2WhcNMzkwNDMwMTgxOTA2WjBnMRswGQYDVQQDDBJBcHBsZSBS' .
            'b290IENBIC0gRzMxJjAkBgNVBAsMHUFwcGxlIENlcnRpZmljYXRpb24gQXV0aG9y' .
            'aXR5MRMwEQYDVQQKDApBcHBsZSBJbmMuMQswCQYDVQQGEwJVUzB2MBAGByqGSM49' .
            'AgEGBSuBBAAiA2IABJjpLz1AcqTtkyJygnnkNkA0KisFcrr61jTHApnOPdIJFN4A' .
            'EEpvfLsz8uVuX0M89R7SWKM9Kqvdxz/EzaAEYy3kEuKnEFMJXQNFTfnbQ2BSNI3' .
            'NJkHnBq3TNXqalPDzaNjMGEwHQYDVR0OBBYEFLuw3qFYM4iapIqZ3r6//PT9uh6v' .
            'MA8GA1UdEwEB/wQFMAMBAf8wHwYDVR0jBBgwFoAUu7DeoVgziJqkipnevr/09P26' .
            'Hq8wDgYDVR0PAQH/BAQDAgGGMAoGCCqGSM49BAMDA2gAMGUCMQCD6cHEFl4aXTQY' .
            '2e38v1ml/YdlVIz9AQGHsJR5pXHHVBqhZeJpYuyca+A2coBCMCMAnbPkRm/pnOX6' .
            'pj1Ds5kG3SicUi7BQJF10bQCY9gy0w3hcBazQWG/VBhAA==';

        return [base64_decode($appleRootG3Base64)];
    }

    private function transactionToArray(object $txnInfo): array
    {
        return [
            'transactionId'         => $txnInfo->getTransactionId(),
            'originalTransactionId' => $txnInfo->getOriginalTransactionId(),
            'bundleId'              => $txnInfo->getBundleId(),
            'productId'             => $txnInfo->getProductId(),
            'purchaseDate'          => $txnInfo->getPurchaseDate(),
            'originalPurchaseDate'  => $txnInfo->getOriginalPurchaseDate(),
            'expiresDate'           => $txnInfo->getExpiresDate(),
            'revocationDate'        => $txnInfo->getRevocationDate(),
            'environment'           => $txnInfo->getEnvironment()?->getValue() ?? 'Sandbox',
            'type'                  => $txnInfo->getType()?->getValue() ?? 'Non-Consumable',
            'quantity'              => $txnInfo->getQuantity() ?? 1,
        ];
    }
}

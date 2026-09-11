<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Apple In-App Purchase (StoreKit 2) Configuration
    |--------------------------------------------------------------------------
    |
    | These values are used to authenticate with the Apple App Store Server API
    | for verifying StoreKit 2 transactions and handling App Store Server
    | Notifications V2 (webhooks).
    |
    | Keys can be obtained from App Store Connect:
    |   Users and Access → Integrations → In-App Purchase
    |
    */

    // Issuer ID from App Store Connect (UUID format)
    'issuer_id' => env('APPLE_IAP_ISSUER_ID', ''),

    // Key ID (10-character string from App Store Connect)
    'key_id' => env('APPLE_IAP_KEY_ID', ''),

    // Your app's Bundle ID (e.g., com.skylighttech.chandlaBook.ios)
    'bundle_id' => env('APPLE_IAP_BUNDLE_ID', 'com.skylighttech.chandlaBook.ios'),

    // Your app's Apple ID (numeric, from App Store Connect → App Information)
    'app_apple_id' => env('APPLE_IAP_APP_APPLE_ID', 6796605523),

    // Path to the .p8 private key file (relative to Laravel base_path())
    'private_key_path' => env('APPLE_IAP_PRIVATE_KEY_PATH', 'storage/keys/SubscriptionKey_DVGNDYGW3Y.p8'),

    // Environment: 'sandbox' for testing/Apple Review, 'production' for live
    // IMPORTANT: Always set to 'production' on live server. The service automatically
    // falls back to sandbox for Apple App Review traffic (error code 4040010).
    'environment' => env('APPLE_IAP_ENVIRONMENT', 'sandbox'),
];

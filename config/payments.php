<?php

/**
 * ──────────────────────────────────────────────────────────────────────────────
 * PAYMENTS FEATURE FLAG
 * ──────────────────────────────────────────────────────────────────────────────
 *
 * PAYMENTS_ENABLED controls whether the frontend shows payment restrictions,
 * paywalls, upgrade prompts, and subscription checks to users.
 *
 * When set to false:
 *   - All users get full frontend access regardless of subscription status.
 *   - Payment walls, upgrade prompts, and plan-based locks are hidden.
 *   - The underlying payment code/APIs remain completely intact.
 *
 * To DISABLE payment restrictions  →  set PAYMENTS_ENABLED=false in .env
 * To RE-ENABLE payment restrictions →  set PAYMENTS_ENABLED=true in .env
 *
 * ⚠️  This flag only affects the FRONTEND UI layer.
 *     Backend API endpoints, webhooks, and database logic are NOT changed.
 * ──────────────────────────────────────────────────────────────────────────────
 */

return [
    'enabled' => filter_var(env('PAYMENTS_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
];

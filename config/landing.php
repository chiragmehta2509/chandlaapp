<?php

/**
 * Public marketing copy / amounts for the home page (keep in sync with app where noted).
 */
return [
    /** Marriage invitation unlock — printable cards for social sharing */
    'invitation_card_inr' => (float) env('PUBLIC_INVITATION_CARD_PRICE', 200),

    /**
     * Marketing bundle on the home page: 10 invitation layouts + 1 video + pre-wedding studio.
     * (In-app checkout may still use separate line items; align copy when billing is unified.)
     */
    'celebration_pack_inr' => (float) env('PUBLIC_CELEBRATION_PACK_PRICE', 300),

    /**
     * Host Duo pack: 2 events, unlimited chandla (see packs.ledger_duo).
     */
    'host_duo_pack_inr' => (float) env('PUBLIC_HOST_DUO_PACK_PRICE', 500),

    /** Guest pay single-event pack (see packs.guest_pay_single). */
    'guest_pay_single_inr' => (float) env('PUBLIC_GUEST_PAY_SINGLE_PRICE', 400),

    /**
     * Complete host pack: celebration + ledger perks + PDF (see packs.premium_bundle).
     */
    'premium_bundle_inr' => (float) env('PUBLIC_PREMIUM_BUNDLE_PRICE', 700),

    /**
     * Marketing copy for in-app per-event unlock (Direct GPay / unlimited-style upgrade).
     * Prefer keeping this aligned with DIRECT_GPAY_UNLOCK_AMOUNT / services.direct_gpay_unlock.amount.
     */
    'event_full_inr' => (float) env('PUBLIC_EVENT_FULL_PRICE', 400),
];

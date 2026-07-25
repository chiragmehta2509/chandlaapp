<?php

/**
 * Marriage invitation: one shared form; templates are print / PNG layouts only.
 *
 * MARRIAGE_INVITATION_BYPASS_EMAILS (comma-separated): emails allowed to skip invitation payment for exports.
 * When unset, no bypass list is applied — add explicitly if you need test accounts.
 */
$bypassRaw = env('MARRIAGE_INVITATION_BYPASS_EMAILS', '');

$devUnlockEnv = env('MARRIAGE_INVITATION_DEV_UNLOCK');
if ($devUnlockEnv === null || $devUnlockEnv === '') {
    $devUnlockAll = in_array(env('APP_ENV', 'production'), ['local', 'testing'], true);
} else {
    $devUnlockAll = filter_var($devUnlockEnv, FILTER_VALIDATE_BOOLEAN);
}

$isTestMode = str_starts_with((string) env('RAZORPAY_KEY_ID', ''), 'rzp_test_');

return [

    /**
     * Blade key for the single “social / Canva-style” video export only.
     * File: resources/views/client/marriage-invitations/templates/{key}.blade.php
     * Swap this template’s HTML/CSS to match your own Canva frames if you like.
     */
    'video_export_template' => env('MARRIAGE_INVITATION_VIDEO_TEMPLATE', 'canva_reel'),

    /** Length of the generated clip (browser MediaRecorder + canvas). */
    'video_export_duration_sec' => max(5, min(120, (int) env('MARRIAGE_INVITATION_VIDEO_SECONDS', 30))),

    'amount' => (float) env('MARRIAGE_INVITATION_PRICE', 300),

    /**
     * Razorpay Payment Page link (rzp.io). Same checkout used by celebration pack when ₹300.
     * Live:  MARRIAGE_INVITATION_RAZORPAY_LINK
     * Test:  MARRIAGE_INVITATION_RAZORPAY_LINK_TEST  ← used when RAZORPAY_KEY_ID starts with rzp_test_
     */
    'razorpay_payment_link' => (function () use ($isTestMode): string {
        if ($isTestMode) {
            $test = trim((string) env('MARRIAGE_INVITATION_RAZORPAY_LINK_TEST', ''));
            if ($test !== '') {
                return $test;
            }
        }
        return trim((string) env('MARRIAGE_INVITATION_RAZORPAY_LINK', 'https://rzp.io/rzp/hA4TbSAZ'));
    })(),

    /**
     * Optional: Razorpay Payment Link id (plink_…) from Dashboard — enable MarriageInvitationRazorpayCompletion when this link is ONLY for invitation unlock (same amount as amount above).
     */
    'razorpay_payment_link_id' => env('MARRIAGE_INVITATION_RAZORPAY_PAYMENT_LINK_ID', ''),
    'dev_unlock_all' => $devUnlockAll,

    'bypass_payment_emails' => array_values(array_filter(array_map('strtolower', array_map('trim', explode(',', (string) $bypassRaw))))),

    /**
     * Sample data for template preview iframes (marketing + style grid). Not stored on invitations.
     * demo_couple_image_url: HTTPS image used as a stand-in photo in thumbnails (templates use MarriageInvitationCard::viewData).
     */
    'demo_card_data' => [
        'groom_name' => 'Oliver',
        'bride_name' => 'Amelia',
        'parent_groom' => 'Mr. James & Mrs. Claire Bennett',
        'parent_bride' => 'Mr. William & Mrs. Sophie Morgan',
        'wedding_date' => '2026-11-28',
        'wedding_time' => '18:30',
        'venue_name' => 'The Grand Ballroom, Savoy Place',
        'venue_address' => "1 Savoy Place\nLondon WC2R 0BP, United Kingdom",
        'rsvp_contact' => '+44 20 7946 0958',
        'tagline' => 'Together with our families',
        'schedule_events' => [
            ['title' => 'Haldi', 'date' => '2026-11-27', 'time' => '10:00 AM'],
            ['title' => 'Wedding & reception', 'date' => '2026-11-28', 'time' => '6:30 PM'],
        ],
        'demo_couple_image_url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=640&q=80',
    ],

    'shared_fields' => [
        'groom_name' => ['label' => 'Groom name', 'required' => true],
        'bride_name' => ['label' => 'Bride name', 'required' => true],
        'parent_groom' => ['label' => 'Parents of groom (optional)', 'required' => false],
        'parent_bride' => ['label' => 'Parents of bride (optional)', 'required' => false],
        'wedding_date' => ['label' => 'Wedding date', 'required' => true, 'type' => 'date'],
        'wedding_time' => ['label' => 'Time', 'required' => false, 'type' => 'time'],
        'venue_name' => ['label' => 'Venue / Hall name', 'required' => true],
        'venue_address' => ['label' => 'Address', 'required' => true, 'type' => 'textarea'],
        'rsvp_contact' => ['label' => 'RSVP / Contact', 'required' => false],
        'tagline' => ['label' => 'Short line (e.g. With love)', 'required' => false],
        'schedule_events' => ['label' => 'Schedule of events (optional)', 'required' => false, 'type' => 'schedule'],
        'couple_image' => ['label' => 'Couple / engagement photo (optional, JPG or PNG)', 'required' => false, 'type' => 'image'],
    ],

    /**
     * Printable layouts only. Keys must match Blade: resources/views/client/marriage-invitations/templates/{key}.blade.php
     * badge: short label for marketing grid (single letter or emoji-style hint)
     */
    'templates' => [
        'heritage' => [
            'name' => 'Heritage gold',
            'description' => 'Classic cream card, gold frame and script names — timeless for traditional weddings.',
            'badge' => 'H',
            'badge_class' => 'bg-amber-200/80 text-amber-900',
        ],
        'minimal' => [
            'name' => 'Minimal bloom',
            'description' => 'Soft glassmorphism, gradient accent bar — clean for WhatsApp and modern venues.',
            'badge' => 'M',
            'badge_class' => 'bg-rose-200/60 text-rose-900',
        ],
        'royal_indigo' => [
            'name' => 'Royal indigo',
            'description' => 'Deep jewel tones, gold trim, editorial serif — luxe Canva-style statement.',
            'badge' => 'R',
            'badge_class' => 'bg-indigo-200/90 text-indigo-950',
        ],
        'garden_blush' => [
            'name' => 'Garden blush',
            'description' => 'Botanical soft pink and sage, rounded photo — Pinterest garden wedding.',
            'badge' => 'G',
            'badge_class' => 'bg-pink-200/70 text-pink-950',
        ],
        'midnight_glam' => [
            'name' => 'Midnight glam',
            'description' => 'Black canvas, champagne type, thin gold rule — evening black-tie energy.',
            'badge' => '★',
            'badge_class' => 'bg-zinc-800 text-amber-200',
        ],
        'coastal_breeze' => [
            'name' => 'Coastal breeze',
            'description' => 'Airy blues and sea-glass accents — beach and destination friendly.',
            'badge' => 'C',
            'badge_class' => 'bg-sky-200/80 text-sky-950',
        ],
        'terracotta_sun' => [
            'name' => 'Terracotta sun',
            'description' => 'Warm earth, sun-bleached cream — Mediterranean and outdoor rituals.',
            'badge' => 'T',
            'badge_class' => 'bg-orange-200/80 text-orange-950',
        ],
        'lavender_dream' => [
            'name' => 'Lavender dream',
            'description' => 'Lilac gradients and soft curves — romantic and whimsical.',
            'badge' => 'L',
            'badge_class' => 'bg-violet-200/80 text-violet-950',
        ],
        'monochrome_chic' => [
            'name' => 'Monochrome chic',
            'description' => 'Bold black & white, magazine layout — minimal typography focus.',
            'badge' => 'B',
            'badge_class' => 'bg-zinc-900 text-white',
        ],
        'saffron_festival' => [
            'name' => 'Saffron festival',
            'description' => 'Vibrant saffron, magenta and gold flourishes — festive Indian celebration.',
            'badge' => '✦',
            'badge_class' => 'bg-amber-400/90 text-amber-950',
        ],
        'emerald_palace' => [
            'name' => 'Emerald palace',
            'description' => 'Deep emerald green, mughal arches, and vintage gold details — royal Indian heritage.',
            'badge' => 'E',
            'badge_class' => 'bg-emerald-800 text-amber-200',
        ],
        'vintage_rose' => [
            'name' => 'Vintage rose',
            'description' => 'Dusty pink background, classical scrollwork, and elegant script — timeless romance.',
            'badge' => 'V',
            'badge_class' => 'bg-rose-100 text-rose-800',
        ],
        'modern_arch' => [
            'name' => 'Modern arch',
            'description' => 'Minimalist pastel arches, earthy terracotta accents, and modern serif typography.',
            'badge' => 'A',
            'badge_class' => 'bg-orange-100 text-orange-800',
        ],
        'luxury_champagne' => [
            'name' => 'Luxury champagne',
            'description' => 'Sleek champagne gold, silk borders, and sophisticated typography for a resort wedding vibe.',
            'badge' => 'L',
            'badge_class' => 'bg-yellow-50 text-yellow-800',
        ],
        'celestial_indigo' => [
            'name' => 'Celestial indigo',
            'description' => 'Deep twilight blue, gold constellations, and whimsical star dust borders.',
            'badge' => '★',
            'badge_class' => 'bg-indigo-950 text-amber-200',
        ],
    ],
];


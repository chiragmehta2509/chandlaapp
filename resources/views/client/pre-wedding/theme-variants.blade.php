{{-- Visual themes: each milestone maps to one. Typography + scrim + accents differ. --}}
<style>
    .capture-root {
        position: relative;
        width: 405px;
        max-width: 100%;
        aspect-ratio: 9 / 16;
        margin: 0 auto;
        overflow: hidden;
        border-radius: 2px;
        box-sizing: border-box;
    }
    .pw-bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        filter: saturate(1.02);
    }
    .pw-scrim {
        position: absolute;
        inset: 0;
        pointer-events: none;
    }
    .pw-content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 22px 18px 20px;
        box-sizing: border-box;
    }
    .pw-headblock { width: 100%; }
    .pw-headline-row {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        gap: 6px 10px;
        line-height: 0.95;
    }
    .pw-h-main {
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    .pw-h-side {
        font-weight: 600;
        opacity: 0.95;
    }
    .pw-subline {
        margin-top: 6px;
        font-size: 0.92rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    .pw-quote {
        margin: 14px 0 0;
        font-size: 0.58rem;
        line-height: 1.55;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        max-width: 100%;
    }
    /* --- Themes --- */
    .pw-theme-misty_dusk .pw-content { justify-content: flex-start; }
    .pw-theme-misty_dusk .pw-scrim { background: linear-gradient(180deg, rgba(15,23,42,.75) 0%, rgba(15,23,42,.25) 45%, rgba(15,23,42,.55) 100%); }
    .pw-theme-misty_dusk .pw-h-main { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 3.35rem; color: #f8fafc; text-shadow: 0 2px 24px rgba(0,0,0,.4); }
    .pw-theme-misty_dusk .pw-h-side { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.45rem; color: #e2e8f0; }
    .pw-theme-misty_dusk .pw-subline { color: #cbd5e1; }
    .pw-theme-misty_dusk .pw-quote { color: rgba(248,250,252,.88); font-family: 'Libre Baskerville', Georgia, serif; }

    .pw-theme-golden_hour .pw-content { justify-content: flex-end; padding-bottom: 28px; }
    .pw-theme-golden_hour .pw-scrim { background: linear-gradient(0deg, rgba(69,26,3,.82) 0%, rgba(120,53,15,.35) 40%, rgba(254,243,199,.2) 100%); }
    .pw-theme-golden_hour .pw-h-main { font-family: 'Playfair Display', Georgia, serif; font-size: 3.5rem; color: #fffbeb; }
    .pw-theme-golden_hour .pw-h-side { font-family: 'Playfair Display', Georgia, serif; font-size: 1.5rem; color: #fde68a; }
    .pw-theme-golden_hour .pw-subline { color: #fef3c7; }
    .pw-theme-golden_hour .pw-quote { color: rgba(255,251,235,.9); }

    .pw-theme-garden_bloom .pw-content { justify-content: flex-start; }
    .pw-theme-garden_bloom .pw-scrim { background: linear-gradient(165deg, rgba(20,83,45,.72) 0%, transparent 50%, rgba(190,24,93,.45) 100%); }
    .pw-theme-garden_bloom .pw-h-main { font-family: 'Fraunces', Georgia, serif; font-size: 3.1rem; color: #ecfccb; }
    .pw-theme-garden_bloom .pw-h-side { font-family: 'Fraunces', Georgia, serif; font-size: 1.35rem; color: #f9a8d4; }
    .pw-theme-garden_bloom .pw-subline { color: #d9f99d; }
    .pw-theme-garden_bloom .pw-quote { color: rgba(240,253,244,.92); }

    .pw-theme-midnight_rose .pw-content { justify-content: center; text-align: center; }
    .pw-theme-midnight_rose .pw-headblock { text-align: center; }
    .pw-theme-midnight_rose .pw-headline-row { justify-content: center; }
    .pw-theme-midnight_rose .pw-scrim { background: radial-gradient(ellipse at 50% 30%, rgba(251,113,133,.25) 0%, rgba(9,9,11,.88) 65%); }
    .pw-theme-midnight_rose .pw-h-main { font-family: 'Playfair Display', Georgia, serif; font-size: 3.25rem; color: #fda4af; }
    .pw-theme-midnight_rose .pw-h-side { font-family: 'Playfair Display', Georgia, serif; font-size: 1.4rem; color: #fce7f3; }
    .pw-theme-midnight_rose .pw-subline { color: #fbcfe8; }
    .pw-theme-midnight_rose .pw-quote { color: rgba(253,242,248,.85); }

    .pw-theme-coastal_fog .pw-content { justify-content: flex-start; }
    .pw-theme-coastal_fog .pw-scrim { background: linear-gradient(200deg, rgba(12,74,110,.7) 0%, rgba(255,255,255,.15) 55%, rgba(14,116,144,.5) 100%); }
    .pw-theme-coastal_fog .pw-h-main { font-family: 'DM Sans', sans-serif; font-size: 3rem; color: #f0f9ff; font-weight: 800; }
    .pw-theme-coastal_fog .pw-h-side { font-family: 'DM Sans', sans-serif; font-size: 1.25rem; color: #bae6fd; }
    .pw-theme-coastal_fog .pw-subline { color: #e0f2fe; }
    .pw-theme-coastal_fog .pw-quote { color: rgba(240,249,255,.88); letter-spacing: 0.12em; }

    .pw-theme-saffron_edge .pw-content { justify-content: flex-start; }
    .pw-theme-saffron_edge .pw-scrim { background: linear-gradient(180deg, rgba(254,243,199,.92) 0%, rgba(254,243,199,.35) 35%, rgba(69,26,3,.65) 100%); }
    .pw-theme-saffron_edge .pw-h-main { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 3.6rem; color: #1c1917; font-weight: 700; }
    .pw-theme-saffron_edge .pw-h-side { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.5rem; color: #292524; }
    .pw-theme-saffron_edge .pw-subline { color: #44403c; }
    .pw-theme-saffron_edge .pw-quote { color: #292524; font-weight: 600; }

    .pw-theme-lavender_veil .pw-content { justify-content: flex-end; }
    .pw-theme-lavender_veil .pw-scrim { background: linear-gradient(0deg, rgba(76,29,149,.85) 0%, rgba(167,139,250,.35) 50%, rgba(250,245,255,.25) 100%); }
    .pw-theme-lavender_veil .pw-h-main { font-family: 'Playfair Display', Georgia, serif; font-size: 3.2rem; color: #faf5ff; }
    .pw-theme-lavender_veil .pw-h-side { font-family: 'Playfair Display', Georgia, serif; font-size: 1.35rem; color: #e9d5ff; }
    .pw-theme-lavender_veil .pw-subline { color: #ddd6fe; }
    .pw-theme-lavender_veil .pw-quote { color: rgba(250,245,255,.9); }

    .pw-theme-cherry_romance .pw-content { justify-content: center; }
    .pw-theme-cherry_romance .pw-headblock { text-align: center; }
    .pw-theme-cherry_romance .pw-headline-row { justify-content: center; }
    .pw-theme-cherry_romance .pw-scrim { background: linear-gradient(135deg, rgba(127,29,29,.78) 0%, rgba(190,18,60,.45) 100%); }
    .pw-theme-cherry_romance .pw-h-main { font-family: 'Libre Baskerville', Georgia, serif; font-size: 2.85rem; color: #fff1f2; }
    .pw-theme-cherry_romance .pw-h-side { font-family: 'Libre Baskerville', Georgia, serif; font-size: 1.3rem; color: #fecdd3; }
    .pw-theme-cherry_romance .pw-subline { color: #fda4af; }
    .pw-theme-cherry_romance .pw-quote { color: rgba(255,228,230,.9); }

    .pw-theme-ivory_script .pw-content { justify-content: flex-start; }
    .pw-theme-ivory_script .pw-scrim { background: linear-gradient(180deg, rgba(255,255,255,.88) 0%, rgba(255,255,255,.25) 40%, rgba(28,25,23,.55) 100%); }
    .pw-theme-ivory_script .pw-h-main { font-family: 'Great Vibes', cursive; font-size: 3.8rem; color: #1c1917; font-weight: 400; }
    .pw-theme-ivory_script .pw-h-side { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.6rem; color: #292524; }
    .pw-theme-ivory_script .pw-subline { color: #44403c; }
    .pw-theme-ivory_script .pw-quote { color: #57534e; }

    .pw-theme-modern_mono .pw-content { justify-content: flex-end; text-align: right; }
    .pw-theme-modern_mono .pw-headblock { text-align: right; }
    .pw-theme-modern_mono .pw-headline-row { justify-content: flex-end; }
    .pw-theme-modern_mono .pw-scrim { background: linear-gradient(0deg, rgba(0,0,0,.88) 0%, rgba(255,255,255,.08) 100%); }
    .pw-theme-modern_mono .pw-h-main { font-family: 'DM Sans', sans-serif; font-size: 2.75rem; color: #fff; font-weight: 900; letter-spacing: -0.04em; }
    .pw-theme-modern_mono .pw-h-side { font-family: 'DM Sans', sans-serif; font-size: 1.1rem; color: #a3a3a3; font-weight: 700; }
    .pw-theme-modern_mono .pw-subline { color: #d4d4d4; }
    .pw-theme-modern_mono .pw-quote { color: rgba(212,212,212,.85); }

    .pw-theme-blush_arch .pw-content { justify-content: flex-start; padding-top: 32px; }
    .pw-theme-blush_arch .pw-scrim { background: linear-gradient(180deg, rgba(253,242,248,.9) 0%, rgba(244,114,182,.2) 45%, rgba(131,24,67,.55) 100%); }
    .pw-theme-blush_arch .pw-h-main { font-family: 'Playfair Display', Georgia, serif; font-size: 3.15rem; color: #831843; }
    .pw-theme-blush_arch .pw-h-side { font-family: 'Playfair Display', Georgia, serif; font-size: 1.35rem; color: #9d174d; }
    .pw-theme-blush_arch .pw-subline { color: #a21caf; }
    .pw-theme-blush_arch .pw-quote { color: #4a044e; }

    .pw-theme-emerald_night .pw-content { justify-content: center; }
    .pw-theme-emerald_night .pw-headblock { text-align: center; }
    .pw-theme-emerald_night .pw-headline-row { justify-content: center; }
    .pw-theme-emerald_night .pw-scrim { background: linear-gradient(160deg, rgba(6,78,59,.82) 0%, rgba(4,47,46,.65) 100%); }
    .pw-theme-emerald_night .pw-h-main { font-family: 'Fraunces', Georgia, serif; font-size: 3rem; color: #ecfdf5; }
    .pw-theme-emerald_night .pw-h-side { font-family: 'Fraunces', Georgia, serif; font-size: 1.3rem; color: #a7f3d0; }
    .pw-theme-emerald_night .pw-subline { color: #6ee7b7; }
    .pw-theme-emerald_night .pw-quote { color: rgba(209,250,229,.9); }

    .pw-theme-sunset_warm .pw-content { justify-content: flex-end; }
    .pw-theme-sunset_warm .pw-scrim { background: linear-gradient(0deg, rgba(154,52,18,.82) 0%, rgba(251,146,60,.35) 55%, rgba(253,224,71,.2) 100%); }
    .pw-theme-sunset_warm .pw-h-main { font-family: 'Playfair Display', Georgia, serif; font-size: 3.35rem; color: #fffbeb; }
    .pw-theme-sunset_warm .pw-h-side { font-family: 'Playfair Display', Georgia, serif; font-size: 1.45rem; color: #ffedd5; }
    .pw-theme-sunset_warm .pw-subline { color: #fed7aa; }
    .pw-theme-sunset_warm .pw-quote { color: rgba(255,251,235,.92); }

    .pw-theme-royal_plum .pw-content { justify-content: flex-start; }
    .pw-theme-royal_plum .pw-scrim { background: linear-gradient(145deg, rgba(59,7,100,.82) 0%, rgba(30,27,75,.55) 100%); }
    .pw-theme-royal_plum .pw-h-main { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 3.4rem; color: #fae8ff; }
    .pw-theme-royal_plum .pw-h-side { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.45rem; color: #e9d5ff; }
    .pw-theme-royal_plum .pw-subline { color: #d8b4fe; }
    .pw-theme-royal_plum .pw-quote { color: rgba(243,232,255,.88); }

    .pw-theme-paper_minimal .pw-content { justify-content: flex-start; }
    .pw-theme-paper_minimal .pw-scrim { background: linear-gradient(180deg, rgba(250,250,249,.94) 0%, rgba(250,250,249,.2) 50%, rgba(28,25,23,.5) 100%); }
    .pw-theme-paper_minimal .pw-h-main { font-family: 'DM Sans', sans-serif; font-size: 2.65rem; color: #0c0a09; font-weight: 800; }
    .pw-theme-paper_minimal .pw-h-side { font-family: 'DM Sans', sans-serif; font-size: 1rem; color: #44403c; font-weight: 700; }
    .pw-theme-paper_minimal .pw-subline { color: #57534e; }
    .pw-theme-paper_minimal .pw-quote { color: #44403c; }

    .pw-theme-celebration_gold .pw-content { justify-content: center; text-align: center; }
    .pw-theme-celebration_gold .pw-headblock { text-align: center; }
    .pw-theme-celebration_gold .pw-headline-row { justify-content: center; }
    .pw-theme-celebration_gold .pw-scrim { background: linear-gradient(180deg, rgba(120,53,15,.55) 0%, rgba(254,243,199,.35) 50%, rgba(69,26,3,.75) 100%); }
    .pw-theme-celebration_gold .pw-h-main { font-family: 'Playfair Display', Georgia, serif; font-size: 2.95rem; color: #fffbeb; }
    .pw-theme-celebration_gold .pw-h-side { font-family: 'Playfair Display', Georgia, serif; font-size: 2.2rem; color: #fde68a; }
    .pw-theme-celebration_gold .pw-quote { color: rgba(255,251,235,.92); margin-top: 18px; }

    .pw-custom-text {
        text-align: center;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.95);
        text-shadow: 0 2px 8px rgba(0,0,0,0.8);
        margin-top: auto;
        padding-top: 14px;
        width: 100%;
        box-sizing: border-box;
    }
</style>

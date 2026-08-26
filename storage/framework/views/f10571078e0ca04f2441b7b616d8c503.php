<?php
    $celebrationInr = (float) config('packs.celebration.amount_inr', 300);
    $guestPayInr = (float) config('packs.guest_pay_single.amount_inr', 400);
    $hostDuoInr = (float) config('packs.ledger_duo.amount_inr', 500);
    $premiumInr = (float) config('packs.premium_bundle.amount_inr', 700);

    $schemas = [];

    // 1. Organization Schema (always present on public pages)
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        '@id' => url('/#organization'),
        'name' => 'Chandla Book',
        'url' => url('/'),
        'logo' => asset('images/chandla-favicon.png'),
        'image' => asset('images/chandla-app-icon.png'),
        'description' => 'A smart digital collection ledger & direct Guest Contributionment platform for Indian weddings and occasions.',
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'contactType' => 'customer support',
            'email' => config('chandlabook.support_email', 'support@chandlabook.com'),
            'availableLanguage' => ['English', 'Hindi']
        ]
    ];

    // 2. Website Schema
    $schemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => url('/#website'),
        'url' => url('/'),
        'name' => 'Chandla Book',
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => url('/client/register?ref={search_term_string}'),
            'query-input' => 'required name=search_term_string'
        ]
    ];

    // 3. Breadcrumb list (if not homepage)
    if (request()->path() !== '/' && request()->path() !== '') {
        $segments = request()->segments();
        $itemListElement = [];
        $currentUrl = url('/');

        $itemListElement[] = [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => $currentUrl
        ];

        $accumulated = '';
        foreach ($segments as $index => $segment) {
            $accumulated .= '/' . $segment;
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 2,
                'name' => ucwords(str_replace('-', ' ', $segment)),
                'item' => url($accumulated)
            ];
        }

        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement
        ];
    }

    // 4. FAQ Schema (homepage or FAQ page)
    if (request()->routeIs('public.home') || request()->routeIs('faq') || request()->routeIs('plans')) {
        $faqPairs = [
            [
                'q' => "What is the Chandla Book Celebration Plan?",
                'a' => "The Celebration Plan is a ₹{$celebrationInr} upgrade that unlocks 10 custom invitation card styles, a story-ready invitation video export, and the pre-wedding countdown milestone card generator."
            ],
            [
                'q' => "How does Direct GPay payment work?",
                'a' => "Direct GPay allows guests to pay gifts of any amount directly to the host's personal UPI ID or QR code. Chandla Book charges zero commission, transferring 100% of the funds to your bank account instantly."
            ],
            [
                'q' => "What is the Premium Host Plan?",
                'a' => "The Premium Host Plan costs ₹{$premiumInr} and bundles the invitation layouts, pre-wedding countdown card generator, and 3 event ledgers with unlimited entries."
            ]
        ];

        $mainEntity = [];
        foreach ($faqPairs as $pair) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $pair['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $pair['a']
                ]
            ];
        }

        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity
        ];
    }

    // 5. Product/Service Schema (for pricing packages)
    if (request()->routeIs('public.home') || request()->routeIs('plans')) {
        $packs = [
            ['name' => 'Celebration Plan', 'price' => $celebrationInr, 'desc' => 'Invitation templates, story-ready video generator, and milestone cards.'],
            ['name' => 'Guest Contribution', 'price' => $guestPayInr, 'desc' => 'Unlocks Direct UPI / GPay payments, PDF download, and unlimited ledger entries on one event.'],
            ['name' => 'Host Plus Plan', 'price' => $hostDuoInr, 'desc' => 'Unlocks two event ledgers with unlimited entries and full PDF download features.'],
            ['name' => 'Premium Host Plan', 'price' => $premiumInr, 'desc' => 'Full bundle including invites, video, milestone cards, and 3 unlimited event ledgers.']
        ];

        foreach ($packs as $p) {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $p['name'],
                'description' => $p['desc'],
                'image' => asset('images/chandla-app-icon.png'),
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $p['price'],
                    'priceCurrency' => 'INR',
                    'availability' => 'https://schema.org/InStock',
                    'url' => url('/plans')
                ]
            ];
        }
    }
?>

<?php $__currentLoopData = $schemas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<script type="application/ld+json">
<?php echo json_encode($s, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT); ?>

</script>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /home/chandlabook/public_html/resources/views/public/partials/jsonld.blade.php ENDPATH**/ ?>
<?php
    $defTitle = 'Chandla Book — Event Chandla Ledger & Guest Payments';
    $defDesc = 'Track cash collections, cover balances, envelope details, and direct guest payments (UPI / GPay) for Indian occasions with Chandla Book. Zero-commission guest gifts directly to your account.';
    $defImage = asset('images/chandla-app-icon.png');

    // Dynamic overrides passed from controller or view stack
    $metaTitle = $seoTitle ?? (isset($seo['title']) ? $seo['title'] : $defTitle);
    $metaDesc = $seoDesc ?? (isset($seo['description']) ? $seo['description'] : $defDesc);
    $metaCanonical = $seoCanonical ?? (isset($seo['canonical']) ? $seo['canonical'] : request()->url());
    $metaRobots = $seoRobots ?? (isset($seo['robots']) ? $seo['robots'] : 'index, follow');
    $metaOgType = $seoOgType ?? (isset($seo['og_type']) ? $seo['og_type'] : 'website');
    $metaImage = $seoImage ?? (isset($seo['image']) ? $seo['image'] : $defImage);
    $metaKeywords = $seoKeywords ?? (isset($seo['keywords']) ? $seo['keywords'] : 'chandla book, guest payments, upi ledger, cash tracker, wedding planner india, marriage collection ledger');
?>

<!-- Basic Meta Tags -->
<title><?php echo e($metaTitle); ?></title>
<meta name="description" content="<?php echo e($metaDesc); ?>">
<meta name="keywords" content="<?php echo e($metaKeywords); ?>">
<meta name="robots" content="<?php echo e($metaRobots); ?>">
<link rel="canonical" href="<?php echo e($metaCanonical); ?>">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="<?php echo e($metaOgType); ?>">
<meta property="og:url" content="<?php echo e($metaCanonical); ?>">
<meta property="og:title" content="<?php echo e($metaTitle); ?>">
<meta property="og:description" content="<?php echo e($metaDesc); ?>">
<meta property="og:image" content="<?php echo e($metaImage); ?>">
<meta property="og:locale" content="en_IN">
<meta property="og:site_name" content="Chandla Book">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="<?php echo e($metaCanonical); ?>">
<meta name="twitter:title" content="<?php echo e($metaTitle); ?>">
<meta name="twitter:description" content="<?php echo e($metaDesc); ?>">
<meta name="twitter:image" content="<?php echo e($metaImage); ?>">
<?php /**PATH /home/chandlabook/public_html/resources/views/public/partials/seo.blade.php ENDPATH**/ ?>
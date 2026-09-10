<?php

/**
 * Brand / support hints used in PDF footer pages and exports.
 */
return [
    'support_email'  => env('CHANDLABOOK_SUPPORT_EMAIL', env('MAIL_FROM_ADDRESS', '')),
    'support_phone'  => env('CHANDLABOOK_SUPPORT_PHONE', '+91 78619 76671'),
    'play_store_url' => env('PLAY_STORE_URL', 'https://play.google.com/store/apps/details?id=com.skylighttech.chandla_book'),
    'app_store_url'  => env('APP_STORE_URL', 'https://apps.apple.com/us/app/chandla-book/id6796605523'),
];

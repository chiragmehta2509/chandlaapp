<?php

/**
 * Brand / support hints used in PDF footer pages and exports.
 */
return [
    'support_email' => env('CHANDLABOOK_SUPPORT_EMAIL', env('MAIL_FROM_ADDRESS', '')),
    'support_phone' => env('CHANDLABOOK_SUPPORT_PHONE', '+91 78619 76671'),
];

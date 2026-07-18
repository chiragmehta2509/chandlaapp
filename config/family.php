<?php

return [
    /**
     * Maximum number of family-viewer sub-accounts a single main user can create.
     */
    'max_per_user' => env('FAMILY_MAX_PER_USER', 3),

    /**
     * Default password assigned when a family member is added with phone only (no email).
     * Shown once to the main user; family member is forced to change it on first login.
     */
    'default_password' => 'Chandla@123',
];

<?php

use App\Support\MatrimonialPlan;

if (! function_exists('isPlanActive')) {
    /**
     * @param  int|null  $userId
     */
    function isPlanActive($userId): bool
    {
        return MatrimonialPlan::isPlanActive($userId);
    }
}

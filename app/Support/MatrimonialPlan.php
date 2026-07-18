<?php

namespace App\Support;

use App\Models\MatrimonialPlan as MatrimonialPlanModel;
use Carbon\Carbon;

class MatrimonialPlan
{
    /** Plan types that count as an active paid access (config keys + legacy rows). */
    private static function activePlanTypeKeys(): array
    {
        $fromConfig = array_keys(config('matrimonial.plans', []));

        return array_values(array_unique(array_merge($fromConfig, ['6m', '12m', '500', '200'])));
    }

    public static function isPlanActive(?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }

        $today = Carbon::today();

        return MatrimonialPlanModel::query()
            ->where('user_id', $userId)
            ->whereIn('plan_type', static::activePlanTypeKeys())
            ->where('expiry_date', '>=', $today)
            ->exists();
    }

    public static function activePlanFor(int $userId): ?MatrimonialPlanModel
    {
        $today = Carbon::today();

        return MatrimonialPlanModel::query()
            ->where('user_id', $userId)
            ->whereIn('plan_type', static::activePlanTypeKeys())
            ->where('expiry_date', '>=', $today)
            ->orderByDesc('expiry_date')
            ->first();
    }
}

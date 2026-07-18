<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Admin Plan Controller
 *
 * Provides visibility and management of subscription plans:
 *   - Overview of all plan tiers with subscriber counts and revenue
 *   - Subscriber list per plan level
 *   - Manual grant / revoke plan access for a user
 */
class PlanController extends Controller
{
    // Maps planLevel() integer → user table column name for that plan
    private const LEVEL_COLUMNS = [
        1 => 'celebration_pack_paid_at',
        2 => null, // guest_pay_single — credit-based, no single timestamp
        3 => 'ledger_duo_pack_paid_at',
        4 => 'family_pack_paid_at',
        5 => 'premium_bundle_paid_at',
        6 => 'professional_pack_paid_at',
        7 => 'enterprise_pack_paid_at',
    ];

    // Maps planLevel() → pack config key (for pricing lookup)
    private const LEVEL_CONFIG_KEY = [
        1 => 'celebration',
        2 => 'guest_pay_single',
        3 => 'ledger_duo',
        4 => 'family',
        5 => 'premium_bundle',
        6 => 'professional',
        7 => 'enterprise',
    ];

    /**
     * GET /admin/plans
     * Overview of all plans with subscriber counts and revenue.
     */
    public function index()
    {
        $levelNames  = config('packs.level_names', []);
        $packsConfig = config('packs', []);
        $plans       = [];

        foreach (range(0, 7) as $level) {
            $configKey = self::LEVEL_CONFIG_KEY[$level] ?? null;
            $column    = self::LEVEL_COLUMNS[$level] ?? null;

            // Count subscribers at exactly this level (highest active tier == $level)
            $subscriberCount = $this->countSubscribersAtLevel($level);

            // Revenue from PaymentTransaction for this pack key
            $revenue = 0.0;
            if ($configKey) {
                $revenue = (float) PaymentTransaction::where('package_key', $configKey)
                    ->where('status', PaymentTransaction::STATUS_SUCCESS)
                    ->sum('amount_inr');
            }

            $plans[] = [
                'level'           => $level,
                'name'            => $levelNames[$level] ?? "Level {$level}",
                'config_key'      => $configKey,
                'column'          => $column,
                'amount_inr'      => $configKey ? (float) ($packsConfig[$configKey]['amount_inr'] ?? 0) : 0,
                'description'     => $configKey ? ($packsConfig[$configKey]['description'] ?? '') : 'Free starter plan',
                'subscriber_count' => $subscriberCount,
                'revenue'         => $revenue,
            ];
        }

        $totalRevenue = (float) PaymentTransaction::where('status', PaymentTransaction::STATUS_SUCCESS)->sum('amount_inr');

        return view('admin.plans.index', compact('plans', 'totalRevenue'));
    }

    /**
     * GET /admin/plans/{level}/subscribers
     * Paginated list of users at a specific plan level.
     */
    public function subscribers(int $level)
    {
        $levelNames = config('packs.level_names', []);
        $planName   = $levelNames[$level] ?? "Level {$level}";

        $users = $this->usersAtLevel($level)->paginate(25);

        return view('admin.plans.subscribers', compact('users', 'level', 'planName'));
    }

    /**
     * POST /admin/plans/grant
     * Manually grant a plan tier to a user (admin action).
     */
    public function grantPlan(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'level'   => 'required|integer|min:1|max:7',
        ]);

        $user  = User::findOrFail($validated['user_id']);
        $level = (int) $validated['level'];

        DB::transaction(function () use ($user, $level) {
            $now = now();
            // Grant all levels up to and including the target (cumulative)
            foreach (range(1, $level) as $l) {
                $col = self::LEVEL_COLUMNS[$l] ?? null;
                if ($col && $user->$col === null) {
                    $user->$col = $now;
                }
                // Level 2 (Guest Contribution) uses credits
                if ($l === 2 && (int) $user->guest_pay_single_event_credits === 0) {
                    $user->guest_pay_single_event_credits = 1;
                }
            }
            $user->save();

            Log::info('Admin granted plan level', [
                'admin_action' => 'grant_plan',
                'user_id'      => $user->id,
                'level'        => $level,
            ]);
        });

        $planName = config('packs.level_names.' . $level, "Level {$level}");
        return back()->with('success', "Granted \"{$planName}\" to {$user->name} successfully.");
    }

    /**
     * POST /admin/plans/revoke
     * Revoke a specific plan tier from a user (nullify timestamp).
     */
    public function revokePlan(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'level'   => 'required|integer|min:1|max:7',
        ]);

        $user  = User::findOrFail($validated['user_id']);
        $level = (int) $validated['level'];

        DB::transaction(function () use ($user, $level) {
            // Revoke this level and all higher levels (cascade down)
            foreach (range($level, 7) as $l) {
                $col = self::LEVEL_COLUMNS[$l] ?? null;
                if ($col) {
                    $user->$col = null;
                }
                // Level 2: zero out credits
                if ($l === 2) {
                    $user->guest_pay_single_event_credits = 0;
                }
            }
            $user->save();

            Log::info('Admin revoked plan level', [
                'admin_action' => 'revoke_plan',
                'user_id'      => $user->id,
                'level'        => $level,
            ]);
        });

        $planName = config('packs.level_names.' . $level, "Level {$level}");
        return back()->with('success', "Revoked \"{$planName}\" and all higher plans from {$user->name}.");
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Count non-admin, non-deleted users whose highest active plan level equals $level.
     */
    private function countSubscribersAtLevel(int $level): int
    {
        return $this->usersAtLevel($level)->count();
    }

    /**
     * Returns a query builder for users whose highest plan level equals $level.
     */
    private function usersAtLevel(int $level)
    {
        $q = User::where('is_admin', false)->where('is_deleted', false);

        switch ($level) {
            case 0:
                // Free users: no pack timestamps, no guest credits
                return $q->whereNull('celebration_pack_paid_at')
                    ->whereNull('ledger_duo_pack_paid_at')
                    ->whereNull('family_pack_paid_at')
                    ->whereNull('premium_bundle_paid_at')
                    ->whereNull('professional_pack_paid_at')
                    ->whereNull('enterprise_pack_paid_at')
                    ->where(function ($sub) {
                        $sub->whereNull('guest_pay_single_event_credits')
                            ->orWhere('guest_pay_single_event_credits', 0);
                    });

            case 1:
                // Celebration Pack: has celebration, no higher packs
                return $q->whereNotNull('celebration_pack_paid_at')
                    ->whereNull('ledger_duo_pack_paid_at')
                    ->whereNull('family_pack_paid_at')
                    ->whereNull('premium_bundle_paid_at')
                    ->whereNull('professional_pack_paid_at')
                    ->whereNull('enterprise_pack_paid_at')
                    ->where(function ($sub) {
                        $sub->whereNull('guest_pay_single_event_credits')
                            ->orWhere('guest_pay_single_event_credits', 0);
                    });

            case 2:
                // Guest Contribution: has credits, no higher packs
                return $q->where('guest_pay_single_event_credits', '>', 0)
                    ->whereNull('ledger_duo_pack_paid_at')
                    ->whereNull('family_pack_paid_at')
                    ->whereNull('premium_bundle_paid_at')
                    ->whereNull('professional_pack_paid_at')
                    ->whereNull('enterprise_pack_paid_at');

            case 3:
                // Host Plus: has ledger_duo, no family or above
                return $q->whereNotNull('ledger_duo_pack_paid_at')
                    ->whereNull('family_pack_paid_at')
                    ->whereNull('premium_bundle_paid_at')
                    ->whereNull('professional_pack_paid_at')
                    ->whereNull('enterprise_pack_paid_at');

            case 4:
                // Family Plan
                return $q->whereNotNull('family_pack_paid_at')
                    ->whereNull('premium_bundle_paid_at')
                    ->whereNull('professional_pack_paid_at')
                    ->whereNull('enterprise_pack_paid_at');

            case 5:
                // Premium Host
                return $q->whereNotNull('premium_bundle_paid_at')
                    ->whereNull('professional_pack_paid_at')
                    ->whereNull('enterprise_pack_paid_at');

            case 6:
                // Professional
                return $q->whereNotNull('professional_pack_paid_at')
                    ->whereNull('enterprise_pack_paid_at');

            case 7:
                // Enterprise
                return $q->whereNotNull('enterprise_pack_paid_at');

            default:
                return $q->whereRaw('1=0'); // empty
        }
    }
}

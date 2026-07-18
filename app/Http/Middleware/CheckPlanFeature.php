<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckPlanFeature middleware
 *
 * Usage in routes:
 *   ->middleware('plan.feature:1')  // requires planLevel() >= 1 (Celebration Pack)
 *   ->middleware('plan.feature:2')  // requires planLevel() >= 2 (Guest Contribution)
 *   ->middleware('plan.feature:3')  // requires planLevel() >= 3 (Host Plus Plan)
 *   ->middleware('plan.feature:4')  // requires planLevel() >= 4 (Family Plan)
 *   ->middleware('plan.feature:5')  // requires planLevel() >= 5 (Premium Host)
 *   ->middleware('plan.feature:6')  // requires planLevel() >= 6 (Professional)
 *   ->middleware('plan.feature:7')  // requires planLevel() >= 7 (Enterprise)
 *
 * For API/AJAX requests (Accept: application/json), returns a JSON response.
 * For browser requests, renders a friendly upgrade prompt page.
 */
class CheckPlanFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int|string  $minLevel  Minimum planLevel() required
     */
    public function handle(Request $request, Closure $next, int|string $minLevel = 1): Response
    {
        $minLevel = (int) $minLevel;

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Not logged in — let auth middleware handle it
        if (! $user) {
            return $next($request);
        }

        // Dev unlock: bypass all plan checks
        if (config('packs.dev_unlock_all', false)) {
            return $next($request);
        }

        $userLevel = $user->planLevel();

        if ($userLevel >= $minLevel) {
            return $next($request);
        }

        // Resolve the required plan name
        $levelNames   = config('packs.level_names', []);
        $requiredPlan = $levelNames[$minLevel] ?? "Plan Level {$minLevel}";
        $upgradeUrl   = route('client.plans');

        // API / AJAX — return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'error'          => "This feature requires the {$requiredPlan} or higher.",
                'required_plan'  => $requiredPlan,
                'required_level' => $minLevel,
                'your_level'     => $userLevel,
                'upgrade_url'    => $upgradeUrl,
            ], 403);
        }

        // Browser — render upgrade prompt view
        return response()->view('errors.plan-required', [
            'requiredLevel' => $minLevel,
            'requiredPlan'  => $requiredPlan,
            'userLevel'     => $userLevel,
            'upgradeUrl'    => $upgradeUrl,
        ], 403);
    }
}

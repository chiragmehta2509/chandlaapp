<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveMatrimonialPlan
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!isPlanActive($request->user()?->id)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'An active paid matrimonial plan is required.'], 403);
            }

            return redirect()
                ->route('client.matrimonial.plans')
                ->with('info', 'Upgrade to a paid plan to use this feature.');
        }

        return $next($request);
    }
}

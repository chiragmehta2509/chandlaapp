<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    /**
     * If the authenticated user has must_change_password=true (e.g., a freshly added
     * family viewer), force them to the change-password screen until they update it.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || !$user->must_change_password) {
            return $next($request);
        }

        $allowed = [
            'client.password.edit',
            'client.password.update',
            'client.logout',
        ];

        if (in_array($request->route()?->getName(), $allowed, true)) {
            return $next($request);
        }

        return redirect()->route('client.password.edit')
            ->with('warning', 'Please set a new password before continuing.');
    }
}

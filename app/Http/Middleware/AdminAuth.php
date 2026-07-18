<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->route('admin.login');
        }

        // Check if user is admin
        if (!Auth::guard('web')->user()->is_admin) {
            return redirect()->route('admin.login')->withErrors(['email' => 'You do not have admin access']);
        }

        return $next($request);
    }
}

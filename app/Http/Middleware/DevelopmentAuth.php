<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DevelopmentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Only auto-login in local/testing environments and when no Authorization header is present
        if (app()->environment('local', 'testing') && !$request->hasHeader('Authorization')) {
            if (!Auth::check()) {
                $user = User::first();
                
                if (!$user) {
                    // Create a default user if none exists
                    $user = User::create([
                        'name' => 'Development User',
                        'email' => 'dev@example.com',
                        'password' => bcrypt('password'),
                        'is_active' => true,
                    ]);
                }
                
                Auth::login($user);
            }
        }

        return $next($request);
    }
}

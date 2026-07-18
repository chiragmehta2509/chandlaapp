<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Redirect to appropriate login based on route prefix
        if ($request->is('admin/*')) {
            return route('admin.login');
        }

        if ($request->is('client/*')) {
            return route('client.login');
        }

        // Default to client login
        return route('client.login');
    }
}


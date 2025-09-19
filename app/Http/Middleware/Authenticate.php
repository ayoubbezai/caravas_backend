<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Instead of redirecting, just return null so it throws 401 JSON
        if (! $request->expectsJson()) {
            return null;
        }

        return null;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts access to authenticated users with an admin profile.
 */
class EnsureIsAdmin
{
    /** Reject the request unless the authenticated user is an admin. */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->admin) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}

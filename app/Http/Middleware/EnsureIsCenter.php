<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts access to authenticated users with a center staff profile.
 */
class EnsureIsCenter
{
    /** Reject the request unless the authenticated user is center staff. */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->staff) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}

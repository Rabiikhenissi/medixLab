<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts access to authenticated users with a doctor profile.
 */
class EnsureIsDoctor
{
    /** Reject the request unless the authenticated user is a doctor. */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->doctor) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}

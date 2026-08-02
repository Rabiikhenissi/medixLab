<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts access to authenticated users with a patient profile.
 */
class EnsureIsPatient
{
    /** Reject the request unless the authenticated user is a patient. */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->patient) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}

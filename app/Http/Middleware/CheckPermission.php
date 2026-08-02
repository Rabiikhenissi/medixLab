<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures the authenticated user's group holds the required permission code.
 */
class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! auth()->check()) {
            return redirect()->route('home');
        }

        $user = auth()->user();

        // Must have the specific permission
        if (! $user->hasPermission($permission)) {
            abort(403, 'Vous n\'avez pas la permission requise.');
        }

        return $next($request);
    }
}

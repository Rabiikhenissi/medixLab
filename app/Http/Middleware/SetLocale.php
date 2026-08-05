<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Apply the user's chosen locale (session + cookie) and support ?lang= switches. */
class SetLocale
{
    /** Locales the application can display. */
    public const SUPPORTED = ['fr', 'en'];

    /**
     * Resolve the active locale from the request, session or cookie, then
     * persist a switch made via the ?lang= query parameter.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', $request->cookie('locale'));

        if ($request->query('lang') !== null) {
            $requested = strtolower((string) $request->query('lang'));

            if (in_array($requested, self::SUPPORTED, true)) {
                $locale = $requested;
                app()->setLocale($locale);
                $request->session()->put('locale', $locale);

                return $next($this->stripLangParameter($request))
                    ->withCookie(cookie()->forever('locale', $locale));
            }
        }

        if (in_array($locale, self::SUPPORTED, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    /** Drop the ?lang= query parameter once the switch has been persisted. */
    private function stripLangParameter(Request $request): Request
    {
        $query = $request->query();
        unset($query['lang']);

        $request->query->replace($query);

        return $request;
    }
}

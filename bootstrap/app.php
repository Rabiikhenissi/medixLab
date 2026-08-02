<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\EnsureIsCenter;
use App\Http\Middleware\EnsureIsDoctor;
use App\Http\Middleware\EnsureIsPatient;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;

/** Application bootstrap: routes, middleware and rate limiters. */
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    // Middleware stack: security headers on web, role + permission aliases
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('home'));
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);
        $middleware->alias([
            'permission' => CheckPermission::class,
            'admin' => EnsureIsAdmin::class,
            'doctor' => EnsureIsDoctor::class,
            'patient' => EnsureIsPatient::class,
            'center' => EnsureIsCenter::class,
        ]);
    })
    // Render errors as json only for api routes
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    // Post-boot setup: force https in production and register rate limiters
    ->booted(function () {
        // Force HTTPS URLs in production (safe for alwaysdata which serves TLS)
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Auth: login — 6 attempts/min
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });

        // Auth: registration — 5 attempts/min
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Auth: password reset — 3 attempts/min
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // General: authenticated state-changing actions — 60/min
        RateLimiter::for('general', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Public location endpoints — 20/min
        RateLimiter::for('location', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    })
    ->create();

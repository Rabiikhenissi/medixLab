<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('home'));
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'admin'      => \App\Http\Middleware\EnsureIsAdmin::class,
            'doctor'     => \App\Http\Middleware\EnsureIsDoctor::class,
            'patient'    => \App\Http\Middleware\EnsureIsPatient::class,
            'center'     => \App\Http\Middleware\EnsureIsCenter::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    ->booted(function () {
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

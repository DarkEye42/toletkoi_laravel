<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust dev-tunnel / reverse-proxy headers (X-Forwarded-Host/Proto)
        // so url()/asset()/@vite generate the public https URL instead of
        // http://localhost:8000 (which browsers block as mixed-content + CORS).
        $middleware->trustProxies(at: '*');

        // Must run after TrustProxies so X-Forwarded-Host/Proto are trusted.
        // Fixes Storage::disk('public')->url() + asset() emitting
        // http://localhost:8000 behind an https dev tunnel (mixed-content).
        $middleware->append(\App\Http\Middleware\ForceTunnelRoot::class);

        // Spatie Permission middleware aliases.
        // NOTE: spatie/laravel-permission v6 moved these into the `Middleware`
        // namespace (singular). The previous `Middlewares` path does not exist
        // and threw a BindingResolutionException on every role-protected route.
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'listing.owner' => \App\Http\Middleware\EnsureListingOwner::class,
        ]);

        // Redirect guests to login
        $middleware->redirectGuestsTo('/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

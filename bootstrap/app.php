<?php

use App\Http\Middleware\CheckNivelEducativo;
use App\Http\Middleware\EnsureActiveAccount;
use App\Http\Middleware\EnsureVerifiedEmailWhenEnabled;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\PreventBackHistory::class);

        // Alias para los middleware de roles y permisos
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'check.nivel' => CheckNivelEducativo::class,
            'school.verified' => EnsureVerifiedEmailWhenEnabled::class,
            'account.active' => EnsureActiveAccount::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Aquí puedes manejar excepciones si es necesario
    })
    ->create();

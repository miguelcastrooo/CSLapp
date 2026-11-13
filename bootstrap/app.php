<?php

use App\Http\Middleware\CheckNivelEducativo;
use App\Http\Middleware\EnsureActiveAccount;
use App\Http\Middleware\EnsureVerifiedEmailWhenEnabled;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Alias para los middleware de roles y permisos
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'check.nivel' => CheckNivelEducativo::class,
            'school.verified' => EnsureVerifiedEmailWhenEnabled::class,
            'account.active' => EnsureActiveAccount::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Aquí puedes manejar excepciones si es necesario
    })
    ->create();

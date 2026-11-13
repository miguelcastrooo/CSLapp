<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerifiedEmailWhenEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('security.require_verified_email')) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user || ! $user->hasVerifiedEmail()) {
            if ($request->expectsJson()) {
                abort(403, 'Debes verificar tu correo institucional para continuar.');
            }

            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}

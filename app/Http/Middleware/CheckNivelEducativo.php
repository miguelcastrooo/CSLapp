<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckNivelEducativo
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Si es Admin o ControlEscolar, permite el acceso
        if ($user->role?->name === 'SuperAdmin' || $user->role?->name === 'ControlEscolar') {
            return $next($request);
        }

        // Si el usuario tiene un nivel educativo asignado, permite el acceso
        if ($user->role?->nivelEducativo) {
            return $next($request);
        }

        // Si el usuario tiene roles de Coordinación, permite el acceso
        $allowedRoles = ['CoordinacionPreescolar', 'CoordinacionPrimaria', 'CoordinacionSecundaria'];
        if (in_array($user->role?->name, $allowedRoles)) {
            return $next($request);
        }

        // Si no tiene nivel educativo ni rol de coordinación, redirigir o mostrar error
        return abort(403, 'Acceso no autorizado');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckNivelEducativo
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();

        // Verifica si el usuario tiene el rol adecuado
        if (!$user->hasRole($role)) {
            return redirect()->route('home')->with('error', 'No tienes acceso a esta página.');
        }

        // Verifica si el usuario tiene un nivel educativo asignado
        $nivelEducativo = $user->nivelEducativo;
        if (!$nivelEducativo) {
            return redirect()->route('home')->with('error', 'No tienes un nivel educativo asignado.');
        }

        // Permite el acceso si se pasa todas las condiciones
        return $next($request);
    }
}

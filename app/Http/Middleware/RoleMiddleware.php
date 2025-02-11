<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Verifica si el usuario está autenticado y si su rol coincide con el requerido
        if (auth()->check() && auth()->user()->role == $role) {
            return $next($request);  // Si el rol coincide, permite el acceso
        }

        // Si no tiene el rol adecuado, redirige a la página principal o cualquier otra ruta que desees
        return redirect()->route('home');  // Aquí puedes cambiar 'home' por la ruta que desees redirigir
    }
}

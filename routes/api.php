<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CalificacionController;
use App\Http\Controllers\Api\CatalogoController;
use Illuminate\Support\Facades\Route;

// Angular y Calificaciones no forman parte del sistema operativo actual.
// El código se conserva para una siguiente fase, pero no publica rutas por defecto.
if (! config('features.angular_api_enabled')) {
    return;
}

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware(['auth:sanctum', 'account.active'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/me', fn (\Illuminate\Http\Request $request) => $request->user()->only(['id', 'name', 'email']));

        Route::get('/niveles', [CatalogoController::class, 'niveles']);
        Route::get('/niveles/{nivel}/grados', [CatalogoController::class, 'grados']);
        Route::get('/niveles/{nivel}/plataformas', [CatalogoController::class, 'plataformas']);
        Route::get('/grados/{grado}/secciones', [CatalogoController::class, 'secciones']);
        Route::get('/alumnos', [CatalogoController::class, 'alumnos']);
        Route::get('/calificaciones', [CalificacionController::class, 'index'])->middleware('abilities:calificaciones:read');
        Route::post('/calificaciones', [CalificacionController::class, 'store'])->middleware('abilities:calificaciones:write');
    });
});

<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AlumnoArchivado;
use App\Models\NivelEducativo;
use App\Models\Plataforma;
use App\Support\NivelAccess;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $nivelIds = NivelAccess::allowedIds($user);
        $niveles = NivelEducativo::withCount('alumnos')
            ->whereIn('id', $nivelIds)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        $esCoordinacion = $user->hasAnyRole([
            'CoordinacionPreescolar',
            'CoordinacionPrimaria',
            'CoordinacionSecundaria',
        ]);

        $alumnosActivos = Alumno::with([
            'contactos',
            'familiares',
            'confirmacionesOpcionales',
            'nivelEducativo.plataformas',
            'alumnoPlataforma',
        ])
            ->withCount('alumnoPlataforma')
            ->where('status', 1)
            ->whereIn('nivel_educativo_id', $nivelIds)
            ->get();

        $expedientesPorRevisar = $alumnosActivos
            ->filter(function (Alumno $alumno) use ($esCoordinacion): bool {
                if ($esCoordinacion) {
                    $plataformasRequeridas = ($alumno->nivelEducativo?->plataformas ?? collect())
                        ->where('activo', true);

                    return $plataformasRequeridas->contains(function ($plataforma) use ($alumno): bool {
                        $acceso = $alumno->alumnoPlataforma->firstWhere('plataforma_id', $plataforma->id);

                        return blank($acceso?->usuario) || blank($acceso?->contraseña);
                    });
                }

                $estado = $alumno->estadoExpediente();

                return $estado['importantes'] !== [] || $estado['opcionales'] !== [];
            })
            ->count();

        return view('dashboard', [
            'niveles' => $niveles,
            'accesoCompleto' => NivelAccess::hasFullAccess($user),
            'esCoordinacion' => $esCoordinacion,
            'totalAlumnos' => $alumnosActivos->count(),
            'totalArchivados' => NivelAccess::hasFullAccess($user)
                ? AlumnoArchivado::where('status', 0)->count()
                : null,
            'totalPlataformas' => NivelAccess::hasFullAccess($user)
                ? Plataforma::where('activo', true)->count()
                : null,
            'expedientesPorRevisar' => $expedientesPorRevisar,
            'avisosSinLeer' => $user->unreadNotifications()->count(),
        ]);
    }
}

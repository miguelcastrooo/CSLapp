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
        $niveles = NivelEducativo::withCount('alumnos')->whereIn('id', $nivelIds)->orderBy('nombre')->get();

        return view('dashboard', [
            'niveles' => $niveles,
            'accesoCompleto' => NivelAccess::hasFullAccess($user),
            'totalAlumnos' => Alumno::where('status', 1)->whereIn('nivel_educativo_id', $nivelIds)->count(),
            'totalArchivados' => NivelAccess::hasFullAccess($user)
                ? AlumnoArchivado::where('status', 0)->count()
                : null,
            'totalPlataformas' => NivelAccess::hasFullAccess($user)
                ? Plataforma::count()
                : null,
        ]);
    }
}

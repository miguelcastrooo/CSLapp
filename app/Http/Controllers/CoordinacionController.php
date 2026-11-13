<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grado;
use App\Models\NivelEducativo;
use App\Support\NivelAccess;

class CoordinacionController extends Controller
{
    public function selectGrado()
    {
        $nivelIds = NivelAccess::allowedIds(auth()->user());
        $grados = Grado::whereIn('nivel_educativo_id', $nivelIds)->get();

        return view('coordinacion.grados.select', compact('grados'));
    }

    public function showGradoAlumnos($gradoId)
    {
        $grado = Grado::findOrFail($gradoId);
        NivelAccess::ensureCanAccess(auth()->user(), $grado->nivel_educativo_id);
        $alumnos = Alumno::where('grado_id', $grado->id)->paginate(10);

        return view('coordinacion.grados.alumnos', compact('grado', 'alumnos'));
    }

    public function show($id)
    {
        $alumno = Alumno::findOrFail($id);
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);

        return view('admin.show', compact('alumno'));
    }

    public function showAlumno($id)
    {
        $alumno = Alumno::findOrFail($id);
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);
        $grado = $alumno->grado;

        return view('coordinacion.show', compact('alumno', 'grado'));
    }

    public function gradosShow($gradoId)
    {
        $grado = Grado::findOrFail($gradoId);
        NivelAccess::ensureCanAccess(auth()->user(), $grado->nivel_educativo_id);

        return view('coordinacion.grados.show', compact('grado'));
    }

    public function mostrarAlumnos($nivelId)
    {
        $nivelEducativo = NivelEducativo::findOrFail($nivelId);
        NivelAccess::ensureCanAccess(auth()->user(), $nivelEducativo->id);
        $alumnos = Alumno::where('nivel_educativo_id', $nivelEducativo->id)
            ->with('alumnoPlataforma')
            ->paginate(10);

        return view('coordinacion.grados.alumnos', compact('nivelEducativo', 'alumnos'));
    }
}

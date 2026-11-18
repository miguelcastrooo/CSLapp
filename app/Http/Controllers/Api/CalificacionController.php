<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Grado;
use App\Support\NivelAccess;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['alumno_id' => ['required', 'integer'], 'periodo' => ['nullable', 'string', 'max:30']]);
        $alumno = Alumno::findOrFail($data['alumno_id']);
        NivelAccess::ensureCanAccess($request->user(), $alumno->nivel_educativo_id);
        return response()->json(Calificacion::with('plataforma:id,nombre')->where('alumno_id', $alumno->id)->when($data['periodo'] ?? null, fn ($query, $periodo) => $query->where('periodo', $periodo))->orderByDesc('updated_at')->get());
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->tokenCan('calificaciones:write'), 403);
        $data = $request->validate(['alumno_id' => ['required', 'integer', 'exists:alumnos,id'], 'plataforma_id' => ['required', 'integer', 'exists:plataformas,id'], 'periodo' => ['required', 'string', 'max:30'], 'calificacion' => ['required', 'numeric', 'between:0,100']]);
        $alumno = Alumno::with('nivelEducativo.plataformas')->findOrFail($data['alumno_id']);
        NivelAccess::ensureCanAccess($request->user(), $alumno->nivel_educativo_id);
        abort_unless($alumno->nivelEducativo->plataformas->contains('id', $data['plataforma_id']), 422, 'La plataforma no pertenece al nivel del alumno.');
        $calificacion = Calificacion::updateOrCreate(['alumno_id' => $alumno->id, 'plataforma_id' => $data['plataforma_id'], 'periodo' => $data['periodo']], ['calificacion' => $data['calificacion'], 'registrada_por' => $request->user()->id]);
        return response()->json($calificacion->load('plataforma:id,nombre'), 201);
    }
}

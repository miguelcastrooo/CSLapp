<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grado;
use App\Models\NivelEducativo;
use App\Models\Plataforma;
use App\Support\NivelAccess;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    public function index(Request $request)
    {
        $nivelIds = NivelAccess::allowedIds(auth()->user());
        $nivelesSeleccionados = array_map('intval', $request->input('niveles', []));
        $gradosSeleccionados = array_map('intval', $request->input('grados', []));
        $seccionesSeleccionadas = $request->input('secciones', []);
        $busqueda = $request->input('busqueda');

        abort_unless(empty(array_diff($nivelesSeleccionados, $nivelIds)), 403, 'No tienes permisos para consultar uno de los niveles seleccionados.');

        $query = Alumno::query()->with('plataformas')->whereIn('nivel_educativo_id', $nivelIds);

        if (! empty($nivelesSeleccionados)) {
            $query->whereIn('nivel_educativo_id', $nivelesSeleccionados);
        }

        if (! empty($gradosSeleccionados)) {
            $query->whereIn('grado_id', $gradosSeleccionados);
        }

        if (! empty($seccionesSeleccionadas)) {
            $query->whereIn('seccion', $seccionesSeleccionadas);
        }

        if (! empty($busqueda)) {
            $query->where(function ($consulta) use ($busqueda) {
                $consulta->where('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('apellidopaterno', 'like', "%{$busqueda}%")
                    ->orWhere('apellidomaterno', 'like', "%{$busqueda}%")
                    ->orWhere('matricula', 'like', "%{$busqueda}%");
            });
        }

        $alumnosFiltrados = $query->get();
        $promedios = Plataforma::all()->map(fn ($plataforma) => [
            'nombre' => $plataforma->nombre,
            'promedio' => $alumnosFiltrados->isEmpty() ? 0 : rand(70, 100),
        ]);
        $niveles = NivelEducativo::whereIn('id', $nivelIds)->get(['id', 'nombre']);

        return view('calificaciones.index', compact('promedios', 'niveles'));
    }

    public function show($id)
    {
        $alumno = Alumno::with('nivelEducativo')->findOrFail($id);
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);
        $nivel = $alumno->nivelEducativo->nombre ?? 'Preescolar';
        $plataformasPorNivel = [
            'Preescolar' => ['Classroom', 'Moodle'],
            'Primaria Baja' => ['Classroom', 'Moodle', 'HMH'],
            'Primaria Alta' => ['Classroom', 'Moodle', 'HMH', 'Mathletics', 'Progrentis'],
            'Secundaria' => ['Classroom', 'Moodle', 'Mathletics'],
        ];
        $plataformas = array_map(fn ($plataforma) => [
            'nombre' => $plataforma,
            'calificacion' => rand(60, 100),
        ], $plataformasPorNivel[$nivel] ?? []);

        return response()->json([
            'alumno' => "{$alumno->nombre} {$alumno->apellidopaterno}",
            'plataformas' => $plataformas,
            'nivel' => $nivel,
        ]);
    }

    public function niveles()
    {
        return NivelAccess::allowedLevels(auth()->user())->only(['id', 'nombre'])->values();
    }

    public function gradosPorNivel($nivelId)
    {
        NivelAccess::ensureCanAccess(auth()->user(), (int) $nivelId);

        return Grado::where('nivel_educativo_id', $nivelId)->get(['id', 'nombre']);
    }

    public function secciones($gradoId)
    {
        $grado = Grado::findOrFail($gradoId);
        NivelAccess::ensureCanAccess(auth()->user(), $grado->nivel_educativo_id);

        return response()->json(
            Alumno::where('grado_id', $grado->id)->select('seccion')->distinct()->orderBy('seccion')->get()
                ->map(fn ($item) => ['id' => $item->seccion, 'nombre' => $item->seccion])
        );
    }

    public function alumnos($nivelId, $gradoId, $seccion)
    {
        NivelAccess::ensureCanAccess(auth()->user(), (int) $nivelId);
        abort_unless(Grado::whereKey($gradoId)->where('nivel_educativo_id', $nivelId)->exists(), 404);

        return response()->json(
            Alumno::where('nivel_educativo_id', $nivelId)->where('grado_id', $gradoId)->where('seccion', $seccion)
                ->select('id', 'nombre', 'apellidopaterno', 'apellidomaterno')->orderBy('nombre')->get()
                ->map(fn ($alumno) => ['id' => $alumno->id, 'nombre' => "{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}"])
        );
    }
}

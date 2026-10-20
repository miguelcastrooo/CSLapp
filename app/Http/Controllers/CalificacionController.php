<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Plataforma;
use App\Models\Grado;
use App\Models\NivelEducativo;

class CalificacionController extends Controller
{
    public function index(Request $request)
{
    // Obtener filtros desde el request
    $nivelesSeleccionados = $request->input('niveles', []);
    $gradosSeleccionados = $request->input('grados', []);
    $seccionesSeleccionadas = $request->input('secciones', []);
    $busqueda = $request->input('busqueda');

    // Consulta base de alumnos
    $query = Alumno::query()->with('plataformas');

    if (!empty($nivelesSeleccionados)) {
        $query->whereIn('nivel_educativo_id', $nivelesSeleccionados);
    }

    if (!empty($gradosSeleccionados)) {
        $query->whereIn('grado_id', $gradosSeleccionados);
    }

    if (!empty($seccionesSeleccionadas)) {
        $query->whereIn('seccion', $seccionesSeleccionadas);
    }

    if (!empty($busqueda)) {
        $query->where(function ($q) use ($busqueda) {
            $q->where('nombre', 'like', "%$busqueda%")
              ->orWhere('apellidopaterno', 'like', "%$busqueda%")
              ->orWhere('apellidomaterno', 'like', "%$busqueda%")
              ->orWhere('matricula', 'like', "%$busqueda%");
        });
    }

    $alumnosFiltrados = $query->get();

    // Simular promedios generales por plataforma para alumnos filtrados
    $plataformas = Plataforma::all();

    $promedios = $plataformas->map(function ($plataforma) use ($alumnosFiltrados) {
        return [
            'nombre' => $plataforma->nombre,
            'promedio' => $alumnosFiltrados->isEmpty() ? 0 : rand(70, 100) // Simulado
        ];
    });

    // Obtener niveles educativos para el filtro
    $niveles = NivelEducativo::all(['id', 'nombre']);

    return view('calificaciones.index', compact('promedios', 'niveles'));
}


    public function show($id)
    {
        $alumno = Alumno::with('nivelEducativo')->findOrFail($id);
        $nivel = $alumno->nivelEducativo->nombre ?? 'preescolar';

        $niveles = [
            'preescolar' => ['Classroom', 'Moodle'],
            'primaria_inferior' => ['Classroom', 'Moodle', 'HMH'],
            'primaria_superior' => ['Classroom', 'Moodle', 'HMH', 'Mathletics', 'Progrentis'],
            'secundaria' => ['Classroom', 'Moodle', 'Mathletics']
        ];

        $plataformas_nivel = $niveles[$nivel] ?? $niveles['preescolar'];

        $plataformas = array_map(function($plataforma) {
            return [
                'nombre' => $plataforma,
                'calificacion' => rand(60, 100),
            ];
        }, $plataformas_nivel);

        return response()->json([
            'alumno' => $alumno->nombre . ' ' . $alumno->apellidopaterno,
            'plataformas' => $plataformas,
            'nivel' => $nivel,
        ]);
    }

    // Obtener niveles educativos
    public function niveles()
    {
        return NivelEducativo::all(['id', 'nombre']);
    }

    // Obtener grados por nivel educativo
    public function gradosPorNivel($nivelId)
    {
        return Grado::where('nivel_educativo_id', $nivelId)->get(['id', 'nombre']);
    }

    // Obtener secciones por grado (desde alumnos)
    public function secciones($gradoId)
    {
        $secciones = Alumno::where('grado_id', $gradoId)
            ->select('seccion')
            ->distinct()
            ->orderBy('seccion')
            ->get()
            ->map(function ($item) {
                return ['id' => $item->seccion, 'nombre' => $item->seccion];
            });

        return response()->json($secciones);
    }

    // Obtener alumnos por nivel, grado y sección
    public function alumnos($nivelId, $gradoId, $seccion)
    {
        $alumnos = Alumno::where('nivel_educativo_id', $nivelId)
            ->where('grado_id', $gradoId)
            ->where('seccion', $seccion)
            ->select('id', 'nombre', 'apellidopaterno', 'apellidomaterno')
            ->orderBy('nombre')
            ->get()
            ->map(function ($alumno) {
                return [
                    'id' => $alumno->id,
                    'nombre' => "{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}"
                ];
            });

        return response()->json($alumnos);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $grado = $request->query('grado');
        $nivel_educativo = $request->query('nivel_educativo');

        $alumnos = Alumno::query();

        // Filtro por nombre o matrícula
        if ($search) {
            $alumnos->where('nombre', 'like', "%{$search}%")
                    ->orWhere('matricula', 'like', "%{$search}%");
        }

        // Filtro por grado
        if ($grado) {
            $alumnos->where('grado', $grado);
        }

        // Filtro por nivel educativo
        if ($nivel_educativo) {
            $alumnos->where('nivel_educativo', $nivel_educativo);
        }

        // Paginación
        $alumnos = $alumnos->paginate(10);

        if ($request->ajax()) {
            $alumnosHTML = view('partials.alumnos_table', compact('alumnos'))->render();
            $paginationHTML = view('partials.pagination', compact('alumnos'))->render();

            return response()->json([
                'alumnosHTML' => $alumnosHTML,
                'paginationHTML' => $paginationHTML
            ]);
        }

        // Recoger todos los grados y niveles para mostrarlos en los filtros
        $grados = Alumno::distinct()->pluck('grado');
        $niveles = Alumno::distinct()->pluck('nivel_educativo');

        return view('capturista.index', compact('alumnos', 'grados', 'niveles'));
    }

    public function search(Request $request)
    {
        $query = $request->input('search');
        $alumnos = Alumno::where('matricula', 'like', "%$query%")
            ->orWhere('nombre', 'like', "%$query%")
            ->orWhere('apellidopaterno', 'like', "%$query%")
            ->orWhere('apellidomaterno', 'like', "%$query%")
            ->get();
    
        return response()->json($alumnos);
    }
    

    public function create()
    {
        $nivelesEducativos = [
            'preescolar' => ['BabiesRoom', 'Primero de Kinder', 'Segundo de Kinder', 'Tercero de Kinder'],
            'primaria_baja' => ['1° Primaria', '2° Primaria', '3° Primaria'],
            'primaria_alta' => ['4° Primaria', '5° Primaria', '6° Primaria'],
            'secundaria' => ['1° Secundaria', '2° Secundaria', '3° Secundaria'],
        ];

        return view('capturista.create', compact('nivelesEducativos'));
    }

    public function store(Request $request)
    {
        // Validación de los datos
        $request->validate([
            'matricula' => 'required|numeric|unique:alumnos|digits:10',
            'nombre' => 'required',
            'apellidopaterno' => 'required',
            'apellidomaterno' => 'required',
            'correo_familia' => 'required|email',
            'contacto1nombre' => 'required',
            'telefono1' => 'required|numeric|digits:10',
            'nivel_educativo' => 'required',
            'grado' => 'required',
            'fecha_inscripcion' => 'required|date',
        ]);

        $primerNombre = explode(' ', $request->nombre)[0];
        $anioActual = date('Y');
        $contraseñaClassroom = 'Csl$' . ucfirst(strtolower($primerNombre)) . $anioActual;

        Alumno::create([
            'matricula' => $request->matricula,
            'nombre' => $request->nombre,
            'apellidopaterno' => $request->apellidopaterno,
            'apellidomaterno' => $request->apellidomaterno,
            'correo_familia' => $request->correo_familia,
            'contacto1nombre' => $request->contacto1nombre,
            'telefono1' => $request->telefono1,
            'usuario_classroom' => null,
            'contraseña_classroom' => $contraseñaClassroom,
            'usuario_moodle' => 'Csl-' . $request->matricula,
            'contraseña_moodle' => $request->matricula,
            'nivel_educativo' => $request->nivel_educativo,
            'grado' => $request->grado,
            'fecha_inscripcion' => $request->fecha_inscripcion,
        ]);

        return redirect()->route('capturista.index')->with('success', 'Alumno registrado correctamente');
    }

    public function edit($id)
    {
        $alumno = Alumno::findOrFail($id);
        return view('capturista.edit', compact('alumno'));
    }

    public function update(Request $request, $id)
    {
        // Validación de los datos
        $request->validate([
            'matricula' => 'required|unique:alumnos,matricula,' . $id,
            'nombre' => 'required',
            'apellidopaterno' => 'required',
            'apellidomaterno' => 'required',
            'correo_familia' => 'required|email',
            'contacto1nombre' => 'required',
            'telefono1' => 'required',
            'nivel_educativo' => 'required',
            'grado' => 'required',
            'fecha_inscripcion' => 'required|date',
        ]);

        $alumno = Alumno::findOrFail($id);
        $alumno->update([
            'matricula' => $request->matricula,
            'nombre' => $request->nombre,
            'apellidopaterno' => $request->apellidopaterno,
            'apellidomaterno' => $request->apellidomaterno,
            'correo_familia' => $request->correo_familia,
            'contacto1nombre' => $request->contacto1nombre,
            'telefono1' => $request->telefono1,
            'contacto2nombre' => $request->contacto2nombre,
            'telefono2' => $request->telefono2,
            'nivel_educativo' => $request->nivel_educativo,
            'grado' => $request->grado,
            'fecha_inscripcion' => $request->fecha_inscripcion,
        ]);

        return redirect()->route('alumnos.index')->with('success', 'Alumno actualizado correctamente');
    }

    public function destroy($id)
    {
        $alumno = Alumno::findOrFail($id);
        $alumno->delete();

        return redirect()->route('alumnos.index')->with('success', 'Alumno eliminado correctamente');
    }
}

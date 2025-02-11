<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\NivelEducativo;
use App\Models\Grado;
use App\Models\AlumnoPlataforma;
use App\Models\NivelPlataforma;
use App\Models\Plataforma;
use Barryvdh\DomPDF\Facade\Pdf;


class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $query = Alumno::query(); 

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nombre', 'like', "%$search%")
                  ->orWhere('matricula', 'like', "%$search%")
                  ->orWhere('correo_familia', 'like', "%$search%");
        }

        $alumnos = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('capturista.index', compact('alumnos'));
    }

    public function search(Request $request)
    {
        // Obtiene el término de búsqueda
        $search = $request->input('search');

        // Realiza la consulta de búsqueda en los campos deseados
        $alumnos = Alumno::where('nombre', 'like', "%$search%")
                        ->orWhere('matricula', 'like', "%$search%")
                        ->orWhere('apellidopaterno', 'like', "%$search%")
                        ->orWhere('apellidomaterno', 'like', "%$search%")
                        ->get();

        // Retorna los resultados en formato JSON
        return response()->json($alumnos);
    }


    public function create()
    {
        $niveles = NivelEducativo::all(); // Asegúrate de importar el modelo adecuado
        $grados = Grado::all(); // Asegúrate de importar el modelo adecuado

        return view('capturista.create',compact('niveles','grados'));
    }

    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $request->validate([
            'matricula' => 'required|numeric|unique:alumnos',
            'nombre' => 'required|string|max:255',
            'apellidopaterno' => 'required|string|max:255',
            'apellidomaterno' => 'required|string|max:255',
            'contacto1nombre' => 'required|string|max:255',
            'telefono1' => 'required|digits:10',
            'correo_familia' => 'required|email|unique:alumnos',
            'contacto2nombre' => 'nullable|string|max:255',
            'telefono2' => 'nullable|digits:10',
            'nivel_educativo_id' => 'required|exists:nivel_educativo,id',
            'grado_id' => 'required|exists:grados,id',
            'fecha_inscripcion' => 'required|date',
        ]);
    
        // Crear el nuevo alumno
        $alumno = Alumno::create($request->all());
    
        if (!$alumno) {
            return redirect()->back()->with('error', 'Error al crear el alumno.');
        }
    
        // Obtener el nivel educativo del alumno
        $nivelEducativo = $alumno->nivelEducativo;
    
        // Obtener plataformas asociadas al nivel educativo
        $plataformas = $nivelEducativo->plataformas ?? [];
    
        if ($plataformas->isEmpty()) {
            return redirect()->route('alumnos.index')->with('error', 'No se encontraron plataformas asociadas al nivel educativo.');
        }
    
        // Generar credenciales automáticamente
        $primerNombre = explode(' ', $alumno->nombre)[0];
        $anioInscripcion = date('y', strtotime($alumno->fecha_inscripcion));
    
        $alumno->contraseña_classroom = 'Csl$' . $primerNombre . $anioInscripcion;
        $alumno->usuario_moodle = 'Csl-' . $alumno->matricula;
        $alumno->contraseña_moodle = $alumno->matricula;
        $alumno->save();
    
        // Asociar plataformas sin duplicar credenciales
        foreach ($plataformas as $plataforma) {
            $alumno->plataformas()->attach($plataforma->id, [
                'nivel_educativo_id' => $alumno->nivel_educativo_id,
            ]);
        }
    
        return redirect()->route('alumnos.index')->with('success', 'Alumno registrado exitosamente.');
    }
    
    

    public function show(Alumno $alumno)
    {
        return view('alumnos.show', compact('alumno'));
    }

    public function edit($id)
    {
        // Encuentra al alumno por su id
        $alumno = Alumno::findOrFail($id);
        
        // Obtén los niveles educativos
        $niveles = NivelEducativo::all();
        
        // También puedes pasar los grados relacionados con el nivel del alumno, si es necesario
        $grados = Grado::where('nivel_educativo_id', $alumno->nivel_educativo_id)->get();
        
        // Retorna la vista y pasa los datos
        return view('capturista.edit', compact('alumno', 'niveles', 'grados'));
    }
    

    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'matricula' => 'required|numeric|unique:alumnos,matricula,' . $alumno->id,
            'nombre' => 'required|string|max:255',
            'apellidopaterno' => 'required|string|max:255',
            'apellidomaterno' => 'required|string|max:255',
            'contacto1nombre' => 'required|string|max:255',
            'telefono1' => 'required|digits:10',
            'correo_familia' => 'required|email|unique:alumnos,correo_familia,' . $alumno->id,
            'contacto2nombre' => 'nullable|string|max:255',
            'telefono2' => 'nullable|digits:10',
            'nivel_educativo_id' => 'required|exists:nivel_educativo,id',
            'grado_id' => 'required|exists:grados,id',
            'fecha_inscripcion' => 'required|date',
        ]);

        $alumno->update($request->all());

        return redirect()->route('alumnos.index')->with('success', 'Alumno actualizado exitosamente.');
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->delete();
        return redirect()->route('alumnos.index')->with('success', 'Alumno eliminado correctamente.');
    }

  
}    
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\NivelEducativo;
use App\Models\Grado;
use App\Models\AlumnoPlataforma;
use App\Models\NivelPlataforma;
use App\Models\Plataforma;
use App\Models\User;
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

    public function select()
    {
        $niveles = NivelEducativo::all();
        
        // Depuración para ver qué datos están llegando
        foreach ($niveles as $nivel) {
            logger("Nivel: " . $nivel->nombre . ", Color: " . ($nivel->color ?? 'No definido'));
        }
    
        return view('capturista.select', compact('niveles'));
    }
    
    public function create($nivel_id)
    {
        // Verificar que se está recibiendo el $nivel_id
        $nivel = NivelEducativo::findOrFail($nivel_id);
        $grados = Grado::where('nivel_educativo_id', $nivel->id)->get();
        
        return view('capturista.create', compact('nivel', 'grados'));
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
    
            // Verificación de que el campo nivel_educativo_id no esté vacío
            if (empty($request->nivel_educativo_id)) {
                return redirect()->back()->with('error', 'El campo nivel educativo es requerido.');
            }
    
            // Llamamos a la función para generar las credenciales
            $credenciales = $this->generarCredenciales(
                $request->nombre,
                $request->apellidopaterno,
                $request->matricula,
                $request->fecha_inscripcion
            );
    
            // Crear el nuevo alumno con los datos del formulario y las credenciales generadas
            $alumno = Alumno::create([
                'matricula' => $request->matricula,
                'nombre' => $request->nombre,
                'apellidopaterno' => $request->apellidopaterno,
                'apellidomaterno' => $request->apellidomaterno,
                'contacto1nombre' => $request->contacto1nombre,
                'telefono1' => $request->telefono1,
                'correo_familia' => $request->correo_familia,
                'contacto2nombre' => $request->contacto2nombre,
                'telefono2' => $request->telefono2,
                'nivel_educativo_id' => $request->nivel_educativo_id,
                'grado_id' => $request->grado_id,
                'fecha_inscripcion' => $request->fecha_inscripcion,
                'usuario_classroom' => $credenciales['usuario_classroom'], // Asignamos el usuario generado
                'contraseña_classroom' => $credenciales['contraseña_classroom'], // Asignamos la contraseña generada
                'usuario_moodle' => $credenciales['usuario_moodle'], // Asignamos el usuario de Moodle
                'contraseña_moodle' => $credenciales['contraseña_moodle'], // Asignamos la contraseña de Moodle
                'correo' => $credenciales['correo'], // Asignamos el correo completo con @gmail.com
            ]);
    
            // Verificar si se ha creado correctamente el alumno
            if (!$alumno) {
                return redirect()->back()->with('error', 'Error al crear el alumno.');
            }
    
            // Obtener el nivel educativo del alumno
            $nivelEducativo = $alumno->nivelEducativo;
    
            // Verificar que el nivel educativo tenga plataformas asociadas
            $plataformas = $nivelEducativo->plataformas ?? [];
            if ($plataformas->isEmpty()) {
                return redirect()->route('alumnos.index')->with('error', 'No se encontraron plataformas asociadas al nivel educativo.');
            }
    
            // Asociar plataformas al alumno
            $alumno->plataformas()->syncWithoutDetaching($plataformas->pluck('id')->toArray());
    
            // Redirigir con mensaje de éxito
            return redirect()->route('alumnos.index')->with('success', 'Alumno registrado exitosamente.');
        }
    
        protected function generarCredenciales($nombre, $apellido, $matricula, $fechaInscripcion)
        {
            // Extraemos el primer nombre y el primer apellido
            $primerNombre = strtolower(explode(' ', $nombre)[0]);
            $primerApellido = strtolower(explode(' ', $apellido)[0]);
    
            // Generar correo de Classroom con el formato: primerNombreprimerApellido + 'classroom@gmail.com'
            $emailClassroom = $primerNombre . $primerApellido . 'classroom@gmail.com';
    
            // Verificar si el correo ya existe en la base de datos
            $contador = 1;
            while (Alumno::where('correo', $emailClassroom)->exists()) {
                // Si existe, agregamos un número al final del correo
                $emailClassroom = $primerNombre . $primerApellido . $contador . 'classroom@gmail.com';
                $contador++;
            }
    
            // Generación de contraseñas para Classroom y Moodle
            $anioInscripcion = date('y', strtotime($fechaInscripcion)); // Año de inscripción
            $contraseñaClassroom = 'Csl$' . $primerNombre . $anioInscripcion; // Contraseña para Classroom
            $usuarioMoodle = 'Csl-' . $matricula; // Usuario para Moodle
            $contraseñaMoodle = $matricula; // Contraseña para Moodle (usando la matrícula)
    
            // Devolvemos las credenciales generadas
            return [
                'usuario_classroom' => $primerNombre . $primerApellido . 'classroom',  // Usuario sin el @gmail.com
                'contraseña_classroom' => $contraseñaClassroom,
                'usuario_moodle' => $usuarioMoodle,
                'contraseña_moodle' => $contraseñaMoodle,
                'correo' => $emailClassroom,  // Correo completo con @gmail.com
            ];
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
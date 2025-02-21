<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\NivelEducativo;
use App\Models\Grado;
use App\Models\AlumnoPlataforma;
use App\Models\NivelPlataforma;
use App\Models\Plataforma;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Permission\Models\Role;


class AlumnoController extends Controller
{

    public function __construct()
    {
    }

    // Método index
    public function index(Request $request)
    {
        $user = auth()->user(); // Obtener el usuario autenticado
        
            $query = Alumno::query(); // Crear una consulta base para obtener alumnos
        
            // Si hay un parámetro de búsqueda, aplicarlo a la consulta
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('nombre', 'like', "%$search%")
                      ->orWhere('matricula', 'like', "%$search%")
                      ->orWhere('correo_familia', 'like', "%$search%");
            }
        
            // Realizar la consulta y obtener los alumnos
            $alumnos = $query->orderBy('created_at', 'desc')->paginate(10);
        
            // Redirigir según el rol
            if ($user->hasRole('SuperAdmin')) {
                return view('admin.index', compact('alumnos')); // Vista para SuperAdmin
            }
        
            if ($user->hasRole('ControlEscolar')) {
                return view('capturista.index', compact('alumnos')); // Vista para ControlEscolar
            }
        
            // Si no tiene el rol adecuado, redirigir o abortar
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        
        public function search(Request $request)
        {
            $search = $request->input('search');
            
            // Realiza la consulta de búsqueda
            $alumno = Alumno::where('matricula', $search) 
                            ->orWhere('nombre', 'like', "%$search%")
                            ->orWhere('apellidopaterno', 'like', "%$search%")
                            ->orWhere('apellidomaterno', 'like', "%$search%")
                            ->first(); // Tomar solo el primer alumno que coincida
            
            // Si se encuentra el alumno, devolver los datos con las relaciones
            if ($alumno) {
                return response()->json([
                    'matricula' => $alumno->matricula,
                    'nombre' => $alumno->nombre,
                    'apellidopaterno'=> $alumno->apellidopaterno,
                    'apellidomaterno'=> $alumno->apellidomaterno,
                    'contacto1nombre' => $alumno->contacto1nombre,
                    'telefono1' => $alumno->telefono1,
                    'correo_familia' => $alumno->correo_familia,
                    'contacto2nombre' => $alumno->contacto2nombre,
                    'telefono2' => $alumno->telefono2,
                    'fecha_inscripcion' => $alumno->fecha_inscripcion,
                    // Otros datos que desees incluir
                    'nivel_educativo_nombre' => $alumno->nivelEducativo ? $alumno->nivelEducativo->nombre : '',
                    'grado_nombre' => $alumno->grado ? $alumno->grado->nombre : ''
                ]);
            } else {
                // Si no se encuentra el alumno, devolver null
                return response()->json(null);
            }
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
            'correo_familia' => 'required|email',
            'contacto2nombre' => 'nullable|string|max:255',
            'telefono2' => 'nullable|digits:10',
            'nivel_educativo_id' => 'required|exists:nivel_educativo,id',
            'grado_id' => 'required|exists:grados,id',
            'fecha_inscripcion' => 'required|date',
        ]);

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
            'usuario_classroom' => $credenciales['usuario_classroom'],
            'contraseña_classroom' => $credenciales['contraseña_classroom'],
            'usuario_moodle' => $credenciales['usuario_moodle'],
            'contraseña_moodle' => $credenciales['contraseña_moodle'],
            'correo' => $credenciales['correo'],
        ]);

        // Verificar si se ha creado correctamente el alumno
        if (!$alumno) {
            return redirect()->back()->with('error', 'Error al crear el alumno.');
        }

        // Obtener el nivel educativo del alumno
        $nivelEducativo = $alumno->nivelEducativo;

        // Obtener las plataformas asociadas al nivel educativo
        $plataformas = $nivelEducativo->plataformas;

        // Asociar las plataformas al alumno
        $alumno->plataformas()->sync($plataformas->pluck('id')->toArray());

             // Verificar el rol del usuario y redirigir en consecuencia
    if (auth()->user()->hasRole('SuperAdmin')) {
        // Si es un administrador, redirige al index de admin
        return redirect()->route('admin.search')->with('success', 'Alumno registrado correctamente.');
    } elseif (auth()->user()->hasRole('ControlEscolar')) {
        // Si es de control escolar, redirige a la vista correspondiente
        return redirect()->route('capturista.index')->with('success', 'Alumno registrado correctamente.');
    }

        // Redirigir con mensaje de éxito
        return redirect()->route('capturista.index')->with('success', 'Alumno registrado exitosamente.');
    }

    protected function generarCredenciales($nombre, $apellido, $matricula, $fechaInscripcion)
    {
        // Obtener el primer nombre y apellido
        $primerNombre = strtolower(explode(' ', $nombre)[0]);
        $primerApellido = strtolower(explode(' ', $apellido)[0]);
    
        // Generar el correo con el dominio @colegiosanluis.com.mx
        $emailClassroom = $primerNombre . $primerApellido . '@colegiosanluis.com.mx';
    
        // Comprobamos si el correo ya existe y agregamos un número si es necesario
        $contador = 1;
        while (Alumno::where('correo', $emailClassroom)->exists()) {
            $emailClassroom = $primerNombre . $primerApellido . $contador . '@colegiosanluis.com.mx';
            $contador++;
        }
    
        // Obtener el año de inscripción
        $anioInscripcion = date('y', strtotime($fechaInscripcion));
    
        // Generar las contraseñas
        $contraseñaClassroom = 'Csl$' . $primerNombre . $anioInscripcion;
        $usuarioMoodle = 'Csl-' . $matricula;
        $contraseñaMoodle = $matricula;
    
        // Devolvemos todas las credenciales generadas
        return [
            'usuario_classroom' => $emailClassroom, // Usamos el correo completo
            'contraseña_classroom' => $contraseñaClassroom,
            'usuario_moodle' => $usuarioMoodle,
            'contraseña_moodle' => $contraseñaMoodle,
            'correo' => $emailClassroom, // También guardamos el correo completo
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

        return redirect()->route('capturista.index')->with('success', 'Alumno actualizado exitosamente.');
    }

    public function destroy(Alumno $alumno)
    {
        $alumno->delete();
        return redirect()->route('capturista.index')->with('success', 'Alumno eliminado correctamente.');
    }

    public function showByMatricula(Request $request)
{
    $matricula = $request->input('matricula');  // Obtener la matrícula del formulario

    // Buscar el alumno por la matrícula
    $alumno = Alumno::where('matricula', $matricula)->first();

    // Si no se encuentra el alumno
    if (!$alumno) {
        return redirect()->back()->with('error', 'Alumno no encontrado.');
    }

    // Retornar la vista con los datos del alumno
    return view('capturista.show', compact('alumno'));
}

}    
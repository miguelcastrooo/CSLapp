<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\NivelEducativo;
use App\Models\Admin;
use App\Models\Grado;
use App\Models\AlumnoPlataforma;
use App\Models\NivelPlataforma;
use App\Models\Plataforma;
use App\Models\Egresado;
use Barryvdh\DomPDF\Facade\Pdf;


class AdminController extends Controller
{
        // Función para mostrar los alumnos para la vista de admin (capturista)
        public function index(Request $request)
        {
            $query = Alumno::query(); // Crea la consulta para los alumnos
        
            // Verifica si hay un término de búsqueda en la solicitud
            if ($request->has('search')) {
                $search = $request->input('search');
        
                // Aplica el filtro de búsqueda en los campos relevantes
                $query->where('nombre', 'like', "%$search%")
                      ->orWhere('matricula', 'like', "%$search%")
                      ->orWhere('correo_familia', 'like', "%$search%");
            }
        
            // Realiza la consulta con paginación
            $alumnos = $query->paginate(10); // Ajusta la cantidad de alumnos por página
        
            // Retorna la vista con los alumnos filtrados
            return view('admin.index', compact('alumnos'));
        }
        
        public function search(Request $request)
        {
            $search = $request->input('search');
        
            $alumnos = Alumno::where('nombre', 'like', "%$search%")
                             ->orWhere('matricula', 'like', "%$search%")
                             ->orWhere('apellidopaterno', 'like', "%$search%")
                             ->orWhere('apellidomaterno', 'like', "%$search%")
                             ->get();
        
            // Retorna los resultados en formato JSON, asegurándote de incluir todos los campos que se están usando en la vista
            return response()->json($alumnos);
        }
  
    // Función para crear un nuevo alumno (admin)
    public function create()
    {
        return view('admin.admincreate');
    }

    // Función para almacenar un nuevo alumno
    public function store(Request $request)
    {
        $request->validate([
            'matricula' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellidopaterno' => 'required|string|max:255',
            'apellidomaterno' => 'required|string|max:255',
            'contacto1nombre' => 'required|string|max:255',
            'telefono1' => 'required|string|max:255',
            'correo_familia' => 'required|email|max:255',
            'grado_id' => 'required|integer',
            'fecha_inscripcion' => 'required|date',
            // Asegúrate de validar todos los campos según sea necesario
        ]);

        Alumno::create($request->all());
        return redirect()->route('admin.storeAdmin');
    }

    // Función para editar un alumno (admin)
    public function edit($id)
    {
        $alumno = Alumno::findOrFail($id);
        $niveles = NivelEducativo::all(); // Aquí recuperas todos los niveles educativos
        $grados = Grado::all(); // También puedes cargar los grados de la base de datos si es necesario
    
        return view('admin.adminedit', compact('alumno', 'niveles', 'grados'));
    }

    // Función para actualizar la información de un alumno (admin)
    public function update(Request $request, $id)
    {
        $request->validate([
            'matricula' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellidopaterno' => 'required|string|max:255',
            'apellidomaterno' => 'required|string|max:255',
            'contacto1nombre' => 'required|string|max:255',
            'telefono1' => 'required|string|max:255',
            'correo_familia' => 'required|email|max:255',
            'grado_id' => 'required|integer',
            'fecha_inscripcion' => 'required|date',
            'seccion' => 'nullable|string|max:255',
            'usuario_classroom' => 'nullable|string|max:255',
            'contraseña_classroom' => 'nullable|string|max:255',
            'usuario_moodle' => 'nullable|string|max:255',
            'contraseña_moodle' => 'nullable|string|max:255',
            'usuario_mathletics' => 'nullable|string|max:255',
            'contraseña_mathletics' => 'nullable|string|max:255',
            'usuario_hmh' => 'nullable|string|max:255',
            'contraseña_hmh' => 'nullable|string|max:255',
            'usuario_progrentis' => 'nullable|string|max:255',
            'contraseña_progrentis' => 'nullable|string|max:255',
            'nivel_educativo_id' => 'nullable|integer',
        ]);

        $alumno = Alumno::findOrFail($id);
        $alumno->update($request->all());
        return redirect('/admin')->with('success', 'Alumno actualizado correctamente');
    }

    // Función para generar PDF
    public function generarPdf($nivel, $id)
    {
        // Obtener al alumno con las relaciones necesarias
        $alumno = Alumno::with(['nivelEducativo', 'grado', 'plataformas'])->findOrFail($id);
        $alumno->load('plataformas');

        // Validar que el nivel proporcionado coincide con el nivel del alumno
        if ($alumno->nivelEducativo->nombre !== $nivel) {
            abort(404, 'El nivel educativo del alumno no coincide con el nivel proporcionado.');
        }

        $nivelEducativo = $alumno->nivelEducativo->nombre;

        // Obtener plataformas según el nivel educativo
        $plataformas = $this->obtenerPlataformasPorNivel($nivelEducativo);

        // Generar el PDF
        $pdf = Pdf::loadView('admin.pdf', compact('alumno', 'plataformas'));
        
        return $pdf->stream("credenciales_alumno_{$alumno->matricula}.pdf");
    }

    // Función privada para obtener las plataformas según el nivel educativo
    private function obtenerPlataformasPorNivel($nivelEducativo)
    {
        $nivelEducativo = ucfirst(strtolower($nivelEducativo));

        // Plataformas asociadas con los niveles educativos
        $plataformasPorNivel = [
            'Preescolar' => ['Classroom', 'Moodle'],
            'Primaria Baja' => ['Classroom', 'Moodle', 'HMH'],
            'Primaria Alta' => ['Classroom', 'Moodle', 'HMH', 'Mathletics', 'Progrentis'],
            'Secundaria' => ['Classroom', 'Moodle', 'Mathletics'],
        ];

        // Verificar si el nivel tiene plataformas definidas
        if (isset($plataformasPorNivel[$nivelEducativo])) {
            return Plataforma::whereIn('nombre', $plataformasPorNivel[$nivelEducativo])->get();
        }

        return collect(); // Si no hay plataformas, devolver una colección vacía
    }

    public function select()
    {
        // Recuperar todos los niveles educativos
        $niveles = NivelEducativo::all();
        
        // Depuración para verificar los datos
        foreach ($niveles as $nivel) {
            logger("Nivel: " . $nivel->nombre . ", Color: " . ($nivel->color ?? 'No definido'));
        }

        // Retornar la vista de selección de niveles para el admin
        return view('admin.select', compact('niveles'));
    }

        // Método para listar todos los niveles educativos
    // Listar todos los niveles educativos
    public function nivelesIndex()
    {
        // Trae todos los niveles
        $niveles = NivelEducativo::all();
        
        // Pasamos los niveles a la vista
        return view('admin.niveles', compact('niveles'));
    }
    
    // Mostrar los alumnos de un nivel educativo específico (usando ID en lugar de nombre)
    public function nivelesShow($nivelId)
    {
        // Obtener el nivel educativo según el ID
        $nivelEducativo = NivelEducativo::findOrFail($nivelId);
    
        // Obtener los alumnos relacionados con este nivel
        $alumnos = Alumno::where('nivel_educativo_id', $nivelEducativo->id)->paginate(10); // Ajusta el número de elementos por página
    
        // Pasar los datos a la vista
        return view('admin.niveles', compact('nivelEducativo', 'alumnos'));
    }
    

    //Baja alumno
    public function darBaja($id)
    {
        $alumno = Alumno::findOrFail($id);

        // Mostrar el modal para pedir el motivo
        return view('admin.dar_baja', compact('alumno'));
    }

    public function confirmarBaja($id, Request $request)
    {
        // Recuperamos el alumno con el ID
        $alumno = Alumno::findOrFail($id);
    
        // Verificamos si la matrícula ya está en la tabla de egresados
        $egresadoExistente = Egresado::where('matricula', $alumno->matricula)->first();
    
        if ($egresadoExistente) {
            return redirect()->route('admin.index')->with('error', 'Este alumno ya está registrado como egresado.');
        }
    
        // Insertamos el alumno en la tabla de egresados
        $egresado = new Egresado();
        $egresado->matricula = $alumno->matricula;
        $egresado->nombre = $alumno->nombre;
        $egresado->apellidopaterno = $alumno->apellidopaterno;
        $egresado->apellidomaterno = $alumno->apellidomaterno;
        $egresado->correo = $alumno->correo;
        $egresado->contacto1nombre = $alumno->contacto1nombre;
        $egresado->telefono1 = $alumno->telefono1;
        $egresado->correo_familia = $alumno->correo_familia;
        $egresado->contacto2nombre = $alumno->contacto2nombre;
        $egresado->telefono2 = $alumno->telefono2;
        $egresado->usuario_classroom = $alumno->usuario_classroom;
        $egresado->contraseña_classroom = $alumno->contraseña_classroom;
        $egresado->usuario_moodle = $alumno->usuario_moodle;
        $egresado->contraseña_moodle = $alumno->contraseña_moodle;
        $egresado->usuario_mathletics = $alumno->usuario_mathletics;
        $egresado->contraseña_mathletics = $alumno->contraseña_mathletics;
        $egresado->usuario_hmh = $alumno->usuario_hmh;
        $egresado->contraseña_hmh = $alumno->contraseña_hmh;
        $egresado->usuario_progrentis = $alumno->usuario_progrentis;
        $egresado->contraseña_progrentis = $alumno->contraseña_progrentis;
        $egresado->nivel_educativo_id = $alumno->nivel_educativo_id;
        $egresado->grado_id = $alumno->grado_id;
        $egresado->plataforma_id = $alumno->plataforma_id;
        $egresado->seccion = $alumno->seccion;
        $egresado->fecha_inscripcion = $alumno->fecha_inscripcion;
        $egresado->motivo_baja = $request->motivo_baja;
        $egresado->save();
    
        // Actualizamos el estado del alumno
        $alumno->status = 0; // Baja
        $alumno->save();
    
        return redirect()->route('admin.index')->with('success', 'El alumno ha sido dado de baja y movido a egresados.');
    }
    
}

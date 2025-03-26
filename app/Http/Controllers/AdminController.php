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
use App\Models\Grupo;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Permission\Models\Role;
use App\Mail\EnviarPdfConMensaje;
use Illuminate\Support\Facades\Mail;



class AdminController extends Controller
{
    public function index(Request $request)
    {
    // Obtener los niveles educativos
    $niveles = NivelEducativo::all();

    // Filtrar alumnos según el nivel, grado y sección
    $alumnos = Alumno::query();

    if ($request->has('nivel_id') && $request->nivel_id != '') {
        $alumnos->where('nivel_educativo_id', $request->nivel_id);
    }

    if ($request->has('grado_id') && $request->grado_id != '') {
        $alumnos->where('grado_id', $request->grado_id);
    }

    if ($request->has('seccion') && $request->seccion != '') {
        $alumnos->where('seccion', $request->seccion);
    }

    // Obtener los alumnos filtrados
    $alumnos = $alumnos->get();

    // Obtener grados y secciones dinámicamente dependiendo del nivel
    $grados = Grado::all();
    $secciones = ['A', 'B'];

    return view('admin.index', compact('alumnos', 'niveles', 'grados', 'secciones'));
    }

    public function search(Request $request)
    {
        $search = $request->get('search');

        // Realizar la búsqueda
        $alumnos = Alumno::where('matricula', 'LIKE', "%{$search}%")
                        ->orWhere('nombre', 'LIKE', "%{$search}%")
                        ->orWhere('apellidopaterno', 'LIKE', "%{$search}%")
                        ->orWhere('apellidomaterno', 'LIKE', "%{$search}%")
                        ->get(); // Obtener todos los resultados sin paginación

        // Si la petición es Ajax (por ejemplo, cuando usas JavaScript para actualizar el contenido dinámicamente)
        if ($request->ajax()) {
            return response()->json($alumnos);  // Retornar los resultados en formato JSON si la petición es Ajax
        }

        // Si no es una petición Ajax, devolver los resultados a la vista 'admin.search'
        return view('admin.search', compact('alumnos'));
    }

   // Función para editar un alumno (admin)
   public function edit($id)
    {
        $alumno = Alumno::with('alumnoPlataforma')->findOrFail($id);
        $niveles = NivelEducativo::all();
        $grados = Grado::all();
        $contactos = $alumno->contactos;

        return view('admin.adminedit', compact('alumno', 'niveles', 'grados', 'contactos'));
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
    
        // Validar que el nivel proporcionado coincide con el nivel del alumno
        if ($alumno->nivelEducativo->nombre !== $nivel) {
            abort(404, 'El nivel educativo del alumno no coincide con el nivel proporcionado.');
        }
    
        // Si no hay plataformas, devolver colección vacía en lugar de null
        $plataformasAlumno = $alumno->plataformas ?? collect();
    
        // Generar el PDF
        $pdf = Pdf::loadView('admin.pdf', compact('alumno', 'plataformasAlumno'));
        
        return $pdf->stream("credenciales_alumno_{$alumno->matricula}.pdf");
    }

 public function enviarCorreoConPdf($id)
{
    // Obtener al alumno con sus familiares
    $alumno = Alumno::with('familiares')->findOrFail($id);

    // Filtrar al familiar por tipo (Padre o Madre)
    $padre = $alumno->familiares->firstWhere('tipo_familiar', 'Padre');
    $madre = $alumno->familiares->firstWhere('tipo_familiar', 'Madre');

    // Generar el PDF
    $pdf = Pdf::loadView('admin.pdf', compact('alumno'));
    
    
    // Establecer el asunto del correo
    $asunto = 'Datos de Acceso a Plataformas Digitales';

    // Verificar si el padre tiene correo
    if ($padre && !empty($padre->correo)) {
        // Crear el mensaje para el padre
        $mensaje = "Estimado Sr/Sra. {$padre->nombre} {$padre->apellido_paterno} {$padre->apellido_materno},\n\n";
        $mensaje .= "Adjunto el PDF con los datos de acceso de su hijo(a) {$alumno->nombre} {$alumno->apellido_paterno} {$alumno->apellido_materno}.\n\n";
        $mensaje .= "Atentamente,\n\nEl equipo de la escuela.";

        // Enviar el correo al padre, pasando el $padre como $familiar
        Mail::to($padre->correo)->send(new EnviarPdfConMensaje($mensaje, $alumno, $pdf, $asunto, $padre));
    }

    // Verificar si la madre tiene correo
    if ($madre && !empty($madre->correo)) {
        // Crear el mensaje para la madre
        $mensaje = "Estimada Sra. {$madre->nombre} {$madre->apellido_paterno} {$madre->apellido_materno},\n\n";
        $mensaje .= "Adjunto el PDF con los datos de acceso de su hijo(a) {$alumno->nombre} {$alumno->apellido_paterno} {$alumno->apellido_materno}.\n\n";
        $mensaje .= "Atentamente,\n\nEl equipo de la escuela.";

        // Enviar el correo a la madre, pasando el $madre como $familiar
        Mail::to($madre->correo)->send(new EnviarPdfConMensaje($mensaje, $alumno, $pdf, $asunto, $madre));
    }

    // Redirigir con mensaje de éxito
    return redirect()->back()->with('success', 'El correo se ha enviado exitosamente a los familiares.');
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

    public function generarPdfPorId($id)
    {
        // Obtener al alumno con las relaciones necesarias
        $alumno = Alumno::with(['nivelEducativo', 'grado', 'plataformas'])->findOrFail($id);

        // Si no hay plataformas, devolver colección vacía en lugar de null
        $plataformasAlumno = $alumno->plataformas ?? collect();

        // Generar el PDF
        $pdf = Pdf::loadView('admin.pdf', compact('alumno', 'plataformasAlumno'));
        
        // Descargar el PDF generado
        return $pdf->stream("credenciales_alumno_{$alumno->matricula}.pdf");
    }

    public function generarPdfPlataformas($nivel)
    {
        // Obtener las plataformas asociadas al nivel educativo
        $plataformas = $this->obtenerPlataformasPorNivel($nivel);
    
        // Validar que el nivel tiene plataformas
        if ($plataformas->isEmpty()) {
            abort(404, "No hay plataformas registradas para el nivel: $nivel");
        }
    
        // Generar el PDF con la vista 'admin.plataformas-pdf'
        $pdf = Pdf::loadView('admin.plataformas-pdf', compact('nivel', 'plataformas'));
    
        // Retornar el PDF generado
        return $pdf->stream("plataformas_nivel_{$nivel}.pdf");
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
    
    public function nivelesShow($nivelId)
    {
        // Obtener el nivel educativo según el ID
        $nivelEducativo = NivelEducativo::findOrFail($nivelId);
    
        // Obtener los grados que pertenecen a este nivel educativo, ordenados de más reciente a más antiguo
        $grados = Grado::where('nivel_educativo_id', $nivelEducativo->id)
                       ->orderBy('created_at', 'desc')  // Ordena por fecha de creación (de más reciente a más antiguo)
                       ->get();
    
        // Obtener los alumnos relacionados con este nivel e incluir las plataformas asociadas, ordenados de más reciente a más antiguo
        $alumnos = Alumno::with('alumnoPlataforma')  // Cargar las plataformas
                         ->where('nivel_educativo_id', $nivelEducativo->id)
                         ->orderBy('created_at', 'desc')  // Ordena por fecha de creación (de más reciente a más antiguo)
                         ->paginate(10); // Ajusta el número de elementos por página
    
        // Pasar los datos a la vista
        return view('admin.niveles', compact('nivelEducativo', 'alumnos', 'grados'));
    }
    


    public function selectAdmin()
    {

        // Obtener los niveles educativos desde la base de datos
        $niveles = NivelEducativo::all();  // Esto obtiene todos los niveles educativos
        
        // Devolver la vista con los niveles
        return view('admin.selectadmin', compact('niveles'));
    }
    

    public function showNivelAlumnos(Request $request, $nivelId)
    {
        // Buscar el nivel educativo específico
        $nivel = NivelEducativo::findOrFail($nivelId);
    
        // Obtener los grados disponibles para el nivel educativo
        $grados = Grado::where('nivel_educativo_id', $nivelId)->get();
    
        // Inicializar la consulta para los alumnos
        $alumnos = Alumno::where('nivel_educativo_id', $nivelId);
    
        // Aplicar filtros de grado y sección si se pasan desde la vista
        if ($request->has('grado') && $request->grado != '') {
            $alumnos = $alumnos->where('grado_id', $request->grado);
        }
    
        if ($request->has('seccion') && $request->seccion != '') {
            $alumnos = $alumnos->where('seccion', 'like', '%' . $request->seccion . '%');
        }
    
        // Obtener los alumnos filtrados y paginados
        $alumnos = $alumnos->paginate(10);
    
        // Obtener las secciones del grado seleccionado (si hay un grado seleccionado)
        $secciones = null;
        if ($request->has('grado') && $request->grado != '') {
            $secciones = Seccion::where('grado_id', $request->grado)->get();  // Asumiendo que tienes una relación en la tabla 'seccions'
        }
    
        // Devolver la vista con el nivel, grados, secciones y alumnos filtrados
        return view('admin.index', compact('nivel', 'alumnos', 'grados', 'secciones'));
    }
    
    public function moverGrupos(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'grado_id' => 'required|exists:grado,id',
            'nivel_educativo_id' => 'required|exists:nivel_educativo,id',
            'nuevo_grado_id' => 'required|exists:grado,id',
            'nuevo_nivel_id' => 'required|exists:nivel_educativo,id',
        ]);
    
        // Verificar que el nuevo grado pertenece al nuevo nivel
        $nuevoGrado = Grado::find($request->nuevo_grado_id);
        if (!$nuevoGrado || $nuevoGrado->nivel_educativo_id != $request->nuevo_nivel_id) {
            return redirect()->back()->with('error', 'El grado seleccionado no pertenece al nivel educativo seleccionado.');
        }
    
        // Obtener a los alumnos del grado y nivel actual
        $alumnos = Alumno::where('grado_id', $request->grado_id)
                        ->where('nivel_educativo_id', $request->nivel_educativo_id)
                        ->get();
    
        // Verificar si hay alumnos para mover
        if ($alumnos->isEmpty()) {
            return redirect()->back()->with('error', 'No hay alumnos en ese grado y nivel.');
        }
    
        // Actualizar el grado y nivel de los alumnos seleccionados
        $alumnos->each(function ($alumno) use ($request) {
            $alumno->update([
                'grado_id' => $request->nuevo_grado_id,
                'nivel_educativo_id' => $request->nuevo_nivel_id,
            ]);
        });
    
        // Redirigir con un mensaje de éxito
        return redirect()->route('admin.index')->with('success', 'Grupo movido correctamente.');
    }
    
    public function mostrarFormularioMoverGrupos(Request $request)
    {
        // Obtener todos los grados y niveles educativos
        $grados = Grado::all();
        $niveles = NivelEducativo::all();
    
        // Filtros de grado y nivel
        $grado_id = $request->grado_id;
        $nivel_educativo_id = $request->nivel_educativo_id;
    
        // Obtener los alumnos filtrados por grado y nivel educativo, si se han proporcionado
        $alumnos = Alumno::when($grado_id, function ($query) use ($grado_id) {
                            return $query->where('grado_id', $grado_id);
                        })
                        ->when($nivel_educativo_id, function ($query) use ($nivel_educativo_id) {
                            return $query->where('nivel_educativo_id', $nivel_educativo_id);
                        })
                        ->get();
    
        return view('admin.movergrupos', compact('grados', 'niveles', 'alumnos', 'grado_id', 'nivel_educativo_id'));
    }
    
    public function obtenerGradosYNiveles(Request $request)
{
    // Validar los datos entrantes (opcional, pero recomendable)
    $request->validate([
        'niveles' => 'array',
        'grados' => 'array',
    ]);

    // Obtener niveles filtrados
    $niveles = NivelEducativo::whereIn('id', $request->niveles ?: [])->get();

    // Obtener grados filtrados
    $grados = Grado::whereIn('id', $request->grados ?: [])->get();

    // Obtener alumnos que pertenecen a esos niveles y grados
    $alumnos = Alumno::when($request->niveles, function ($query) use ($request) {
                        return $query->whereIn('nivel_educativo_id', $request->niveles);
                    })
                    ->when($request->grados, function ($query) use ($request) {
                        return $query->whereIn('grado_id', $request->grados);
                    })
                    ->get();

    return response()->json([
        'niveles' => $niveles,
        'grados' => $grados,
        'alumnos' => $alumnos
    ]);
}
    
}

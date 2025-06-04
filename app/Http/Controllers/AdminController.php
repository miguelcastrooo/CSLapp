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
use App\Models\User;

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

    public function generarPdfTodos($nivel)
{
    // Obtener todos los alumnos del nivel educativo proporcionado
    $alumnos = Alumno::with(['nivelEducativo', 'grado', 'plataformas'])
        ->whereHas('nivelEducativo', function ($query) use ($nivel) {
            $query->where('nombre', $nivel);
        })
        ->get();
    
    // Verificar si hay alumnos en el nivel
    if ($alumnos->isEmpty()) {
        return back()->with('error', 'No hay alumnos en este nivel educativo.');
    }

    // Generar los PDFs para cada alumno
    foreach ($alumnos as $alumno) {
        // Si no hay plataformas, devolver colección vacía en lugar de null
        $plataformasAlumno = $alumno->plataformas ?? collect();

        // Generar el PDF para el alumno
        $pdf = Pdf::loadView('admin.pdf', compact('alumno', 'plataformasAlumno'));

        // Guardar el PDF en el servidor (puedes ajustarlo según tus necesidades)
    }

    // Redirigir con mensaje de éxito
    return back()->with('success', 'PDFs generados para todos los alumnos.');
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

public function enviarCorreoATodos($nivel)
{
    // Obtener todos los alumnos del nivel educativo proporcionado
    $alumnos = Alumno::with(['familiares'])->whereHas('nivelEducativo', function ($query) use ($nivel) {
        $query->where('nombre', $nivel);
    })->get();

    // Verificar si hay alumnos
    if ($alumnos->isEmpty()) {
        return back()->with('error', 'No hay alumnos en este nivel educativo.');
    }

    // Iterar sobre cada alumno y enviar el correo a los familiares
    foreach ($alumnos as $alumno) {
        // Filtrar al familiar por tipo (Padre o Madre)
        $padre = $alumno->familiares->firstWhere('tipo_familiar', 'Padre');
        $madre = $alumno->familiares->firstWhere('tipo_familiar', 'Madre');

        // Generar el PDF para el alumno
        $pdf = Pdf::loadView('admin.pdf', compact('alumno'));

        // Establecer el asunto del correo
        $asunto = 'Datos de Acceso a Plataformas Digitales';

        // Verificar si el padre tiene correo
        if ($padre && !empty($padre->correo)) {
            // Crear el mensaje para el padre
            $mensaje = "Estimado Sr/Sra. {$padre->nombre} {$padre->apellido_paterno} {$padre->apellido_materno},\n\n";
            $mensaje .= "Adjunto el PDF con los datos de acceso de su hijo(a) {$alumno->nombre} {$alumno->apellido_paterno} {$alumno->apellido_materno}.\n\n";
            $mensaje .= "Atentamente,\n\nEl equipo de la escuela.";

            // Enviar el correo al padre
            Mail::to($padre->correo)->send(new EnviarPdfConMensaje($mensaje, $alumno, $pdf, $asunto, $padre));
        }

        // Verificar si la madre tiene correo
        if ($madre && !empty($madre->correo)) {
            // Crear el mensaje para la madre
            $mensaje = "Estimada Sra. {$madre->nombre} {$madre->apellido_paterno} {$madre->apellido_materno},\n\n";
            $mensaje .= "Adjunto el PDF con los datos de acceso de su hijo(a) {$alumno->nombre} {$alumno->apellido_paterno} {$alumno->apellido_materno}.\n\n";
            $mensaje .= "Atentamente,\n\nEl equipo de la escuela.";

            // Enviar el correo a la madre
            Mail::to($madre->correo)->send(new EnviarPdfConMensaje($mensaje, $alumno, $pdf, $asunto, $madre));
        }
    }

    // Redirigir con mensaje de éxito
    return redirect()->back()->with('success', 'Los correos se han enviado exitosamente a los familiares de todos los alumnos.');
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

public function nivelesShow(Request $request, $nivelId)
{
    $nivelEducativo = NivelEducativo::findOrFail($nivelId);

    $grados = Grado::where('nivel_educativo_id', $nivelEducativo->id)
                   ->orderBy('created_at', 'desc')
                   ->get();

    // Si la petición es AJAX, devolvemos sólo JSON con los alumnos filtrados
    if ($request->ajax()) {
        $query = Alumno::with(['grado', 'alumnoPlataforma.plataforma'])
                       ->where('nivel_educativo_id', $nivelEducativo->id);

        if ($request->has('grados') && is_array($request->grados)) {
            $query->whereIn('grado_id', $request->grados);
        }

        if ($request->has('secciones') && is_array($request->secciones)) {
            $query->whereIn('seccion', $request->secciones);
        }

        if ($request->filled('buscar')) {
            $search = strtolower($request->buscar);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(matricula) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(nombre) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(apellidopaterno) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(apellidomaterno) LIKE ?', ["%{$search}%"]);
            });
        }

        // Filtro por plataforma
        if ($request->filled('plataforma')) {
            $plataforma = strtolower($request->plataforma);
            $query->whereHas('alumnoPlataforma.plataforma', function ($q) use ($plataforma) {
                $q->whereRaw('LOWER(nombre) = ?', [$plataforma]);
            });
        }

        $alumnos = $query->orderBy('created_at', 'desc')->get();

        return response()->json($alumnos);
    }

    // Vista normal (no AJAX)
    $alumnos = Alumno::with(['grado', 'alumnoPlataforma.plataforma'])
                     ->where('nivel_educativo_id', $nivelEducativo->id)
                     ->orderBy('created_at', 'desc')
                     ->get();

    $seccionesUnicas = $alumnos->pluck('seccion')->unique()->filter()->values();

    return view('admin.niveles', compact('nivelEducativo', 'alumnos', 'grados', 'seccionesUnicas'));
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

        // Inicializar la consulta para los alumnos y ordenarla por la fecha de creación (más reciente primero)
        $alumnos = Alumno::where('nivel_educativo_id', $nivelId)->orderBy('created_at', 'desc');

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
            $secciones = Seccion::where('grado_id', $request->grado)->get();  // Asumiendo que tienes una relación en la tabla 'secciones'
        }

        // Devolver la vista con el nivel, grados, secciones y alumnos filtrados
        return view('admin.index', compact('nivel', 'alumnos', 'grados', 'secciones'));
    }


public function promover(Request $request)
{
    try {
        $alumnoIds = $request->input('alumnos_ids');

        if (empty($alumnoIds)) {
            return response()->json(['success' => false, 'message' => 'Debe seleccionar al menos un alumno.'], 400);
        }

        $alumnos = Alumno::whereIn('id', $alumnoIds)->get();

        if ($alumnos->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No hay alumnos válidos para promover.'], 404);
        }

        foreach ($alumnos as $alumno) {
            $siguienteGrado = $this->getNextGrado($alumno);

            if ($siguienteGrado) {
                $alumno->grado_id = $siguienteGrado->id;
                $alumno->nivel_educativo_id = $siguienteGrado->nivel_educativo_id;
                $alumno->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Los alumnos fueron promovidos correctamente.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al promover alumnos: ' . $e->getMessage()
        ], 500);
    }
    return redirect()->back()->with('success', 'Los alumnos fueron promovidos correctamente.');

}

private function getNextGrado(Alumno $alumno)
{
    // Obtener el grado actual
    $gradoActual = Grado::find($alumno->grado_id);

    if (!$gradoActual) return null;

    // Buscar el siguiente grado con ID mayor
    $siguienteGrado = Grado::where('id', '>', $gradoActual->id)
                           ->orderBy('id', 'asc')
                           ->first();
    return $siguienteGrado;
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
                    ->orderBy('created_at', 'desc')  // Ordenar desde el más reciente
                    ->get();  // Paginación de 10 alumnos por página

    // Pasar los datos a la vista
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

public function assignRoles()
    {
        if (!auth()->user()->hasRole('SuperAdmin')) {
            abort(403);  // Acceso denegado
        }
    
        $users = User::all();
        $roles = Role::all();
        return view('admin.assign-roles', compact('users', 'roles'));
    }

    // app/Http/Controllers/AdminController.php

public function removeRole($userId, $roleId)
{
    $user = User::find($userId);
    $role = Role::find($roleId);

    if (!$user || !$role) {
        return redirect()->route('admin.assignRoles')->with('error', 'Usuario o rol no encontrado.');
    }

    // Eliminar el rol del usuario
    $user->removeRole($role);

    return redirect()->route('admin.assignRoles')->with('success', 'Rol eliminado correctamente.');
}
    
public function saveAssignedRoles(Request $request)
{
    $user = User::find($request->user_id);
    $user->syncRoles($request->roles);  // Asignar los roles seleccionados
    return redirect()->route('admin.assignRoles')->with('success', 'Roles asignados correctamente');
}
    
}

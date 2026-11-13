<?php

namespace App\Http\Controllers;

use App\Mail\EnviarPdfConMensaje;
use App\Models\Admin;
use App\Models\Alumno;
use App\Models\Grado;
use App\Models\HistorialAlumno;
use App\Models\HistorialSeguridad;
use App\Models\NivelEducativo;
use App\Models\Plataforma;
use App\Models\Seccion;
use App\Models\User;
use App\Support\NivelAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function configuracion()
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);

        $credencialesPorNivelYPlataforma = DB::table('alumno_plataforma')
            ->join('alumnos', 'alumnos.id', '=', 'alumno_plataforma.alumno_id')
            ->select('alumnos.nivel_educativo_id', 'alumno_plataforma.plataforma_id', DB::raw('COUNT(*) as total'))
            ->groupBy('alumnos.nivel_educativo_id', 'alumno_plataforma.plataforma_id')
            ->get()
            ->mapWithKeys(fn ($registro) => [
                $registro->nivel_educativo_id . '-' . $registro->plataforma_id => $registro->total,
            ]);

        return view('admin.configuracion', [
            'niveles' => NivelEducativo::with('plataformas')->orderBy('nombre')->get(),
            'grados' => Grado::with('nivelEducativo')->orderBy('nivel_educativo_id')->orderBy('nombre')->get(),
            'plataformas' => Plataforma::orderBy('nombre')->get(),
            'secciones' => Seccion::with('grado.nivelEducativo')->orderBy('nombre')->get(),
            'credencialesPorNivelYPlataforma' => $credencialesPorNivelYPlataforma,
        ]);
    }

    public function guardarNivel(Request $request) { abort_unless(auth()->user()->hasRole('SuperAdmin'), 403); NivelEducativo::create($request->validate(['nombre' => 'required|string|max:100|unique:nivel_educativo,nombre'])); return back()->with('success', 'Nivel educativo creado.'); }
    public function guardarGrado(Request $request)
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);

        $data = $request->validate([
            'nivel_educativo_id' => 'required|exists:nivel_educativo,id',
            'nombre' => 'required|string|max:100',
            'orden_promocion' => 'nullable|integer|min:1|max:999|unique:grados,orden_promocion',
        ]);

        $data['orden_promocion'] ??= ((int) Grado::max('orden_promocion')) + 1;
        Grado::create($data);

        return back()->with('success', "Grado creado. Su orden de promoción es {$data['orden_promocion']}.");
    }
    public function guardarSeccion(Request $request) { abort_unless(auth()->user()->hasRole('SuperAdmin'), 403); Seccion::firstOrCreate($request->validate(['grado_id' => 'required|exists:grados,id', 'nombre' => 'required|string|max:50'])); return back()->with('success', 'Sección guardada.'); }
    public function guardarPlataforma(Request $request) { abort_unless(auth()->user()->hasRole('SuperAdmin'), 403); Plataforma::create($request->validate(['nombre' => 'required|string|max:100|unique:plataformas,nombre'])); return back()->with('success', 'Plataforma creada.'); }

    public function cambiarEstadoConfiguracion(string $tipo, int $id)
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);

        $modelos = [
            'nivel' => NivelEducativo::class,
            'grado' => Grado::class,
            'seccion' => Seccion::class,
            'plataforma' => Plataforma::class,
        ];
        abort_unless(isset($modelos[$tipo]), 404);

        $registro = $modelos[$tipo]::findOrFail($id);
        $nombre = $registro->nombre;

        if ($registro->activo) {
            $enUso = match ($tipo) {
                'nivel' => Alumno::where('nivel_educativo_id', $registro->id)->count(),
                'grado' => Alumno::where('grado_id', $registro->id)->count(),
                'seccion' => Alumno::where('grado_id', $registro->grado_id)->where('seccion', $registro->nombre)->count(),
                'plataforma' => DB::table('alumno_plataforma')->where('plataforma_id', $registro->id)->count(),
            };

            if ($enUso > 0) {
                return back()->with('error', "No se puede desactivar {$nombre}: ya tiene {$enUso} registro(s) relacionado(s). Conserva los datos y reubica o desvincula primero la información necesaria.");
            }
        }

        $registro->update(['activo' => ! $registro->activo]);
        $accion = $registro->activo ? 'activado' : 'desactivado';
        HistorialSeguridad::registrar(auth()->user(), "configuracion_{$accion}", "Se {$accion} {$tipo}: {$nombre}.", ['tipo' => $tipo, 'id' => $registro->id]);

        return back()->with('success', ucfirst($tipo) . " {$accion} correctamente.");
    }

    public function respaldo()
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);

        return view('admin.respaldo');
    }

    public function asignarPlataformasNivel(Request $request, NivelEducativo $nivel)
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);

        $data = $request->validate([
            'plataformas' => 'nullable|array',
            'plataformas.*' => 'exists:plataformas,id',
            'confirmar_retiro' => 'nullable|boolean',
        ]);

        $plataformasActuales = $nivel->plataformas()->pluck('plataformas.id')->all();
        $plataformasSeleccionadas = $data['plataformas'] ?? [];
        $plataformasRetiradas = array_values(array_diff($plataformasActuales, $plataformasSeleccionadas));

        $registrosAfectados = DB::table('alumno_plataforma')
            ->join('alumnos', 'alumnos.id', '=', 'alumno_plataforma.alumno_id')
            ->join('plataformas', 'plataformas.id', '=', 'alumno_plataforma.plataforma_id')
            ->where('alumnos.nivel_educativo_id', $nivel->id)
            ->whereIn('alumno_plataforma.plataforma_id', $plataformasRetiradas)
            ->select('plataformas.nombre', DB::raw('COUNT(*) as total'))
            ->groupBy('plataformas.nombre')
            ->get();

        if ($registrosAfectados->isNotEmpty() && ! $request->boolean('confirmar_retiro')) {
            $detalle = $registrosAfectados
                ->map(fn ($registro) => "{$registro->nombre}: {$registro->total} alumno(s)")
                ->implode(', ');

            return back()
                ->withInput()
                ->with('error', "No se realizó el cambio. Estás retirando una plataforma con información registrada ({$detalle}). Los datos no se borrarán, pero confirma la acción para ocultarla de este nivel.");
        }

        $nivel->plataformas()->sync($plataformasSeleccionadas);

        if ($plataformasRetiradas !== []) {
            HistorialSeguridad::registrar(auth()->user(), 'plataformas_desvinculadas', "Se retiraron plataformas del nivel {$nivel->nombre}.", [
                'nivel_educativo_id' => $nivel->id,
                'plataformas_retiradas' => $plataformasRetiradas,
                'registros_conservados' => $registrosAfectados->sum('total'),
            ]);
        }

        return back()->with('success', 'Plataformas del nivel actualizadas. La información de alumnos ya registrada se conserva.');
    }
    public function create()
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);

        $roles = Role::orderBy('name')->get();

        return view('admin.create', compact('roles'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->syncRoles([$validated['role']]);
        HistorialSeguridad::registrar(auth()->user(), 'usuario_creado', "Se creó la cuenta de {$user->name}.", [
            'usuario_id' => $user->id,
            'rol' => $validated['role'],
        ]);

        if (config('security.require_verified_email')) {
            event(new Registered($user));
        }

        return redirect()->route('admin.assignRoles')->with('success', 'Usuario creado y rol asignado correctamente.');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $niveles = NivelAccess::allowedLevels($user);
        $nivelIds = $niveles->pluck('id')->all();

        // Filtrar alumnos según el nivel, grado y sección
        $alumnos = Alumno::query()->whereIn('nivel_educativo_id', $nivelIds);

        if ($request->has('nivel_id') && $request->nivel_id != '') {
            NivelAccess::ensureCanAccess($user, (int) $request->nivel_id);
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
        $grados = Grado::whereIn('nivel_educativo_id', $nivelIds)->get();
        $secciones = Alumno::whereIn('nivel_educativo_id', $nivelIds)
            ->whereNotNull('seccion')->distinct()->orderBy('seccion')->pluck('seccion')->all();

        return view('admin.index', compact('alumnos', 'niveles', 'grados', 'secciones'));
    }

    public function search(Request $request)
    {
        $search = $request->get('search');
        $nivelIds = NivelAccess::allowedIds(auth()->user());

        // Realizar la búsqueda
        $alumnos = Alumno::whereIn('nivel_educativo_id', $nivelIds)
            ->where(function ($query) use ($search) {
                $query->where('matricula', 'LIKE', "%{$search}%")
                    ->orWhere('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('apellidopaterno', 'LIKE', "%{$search}%")
                    ->orWhere('apellidomaterno', 'LIKE', "%{$search}%");
            })
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
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);
        $niveles = NivelAccess::allowedLevels(auth()->user());
        $grados = Grado::whereIn('nivel_educativo_id', $niveles->pluck('id'))->get();
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
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);
        if ($request->filled('nivel_educativo_id')) {
            NivelAccess::ensureCanAccess(auth()->user(), (int) $request->nivel_educativo_id);
        }
        $alumno->update($request->all());

        return redirect('/admin')->with('success', 'Alumno actualizado correctamente');
    }

    // Función para generar PDF
    public function generarPdf($nivel, $id)
    {
        // Obtener al alumno con las relaciones necesarias
        $alumno = Alumno::with(['nivelEducativo', 'grado', 'plataformas'])->findOrFail($id);
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);

        // Validar que el nivel proporcionado coincide con el nivel del alumno
        if ($alumno->nivelEducativo->nombre !== $nivel) {
            abort(404, 'El nivel educativo del alumno no coincide con el nivel proporcionado.');
        }

        // Si no hay plataformas, devolver colección vacía en lugar de null
        $plataformasAlumno = $alumno->plataformas ?? collect();

        // Generar el PDF
        $pdf = Pdf::loadView('admin.pdf', compact('alumno', 'plataformasAlumno'));
        HistorialSeguridad::registrar(auth()->user(), 'pdf_credenciales_consultado', 'Se consultó el PDF de credenciales de un alumno.', [
            'alumno_id' => $alumno->id,
            'nivel_educativo_id' => $alumno->nivel_educativo_id,
        ]);

        return $pdf->stream("credenciales_alumno_{$alumno->matricula}.pdf");
    }

    public function generarPdfTodos($nivel)
    {
        $nivelEducativo = NivelEducativo::where('nombre', $nivel)->firstOrFail();
        NivelAccess::ensureCanAccess(auth()->user(), $nivelEducativo->id);

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
        HistorialSeguridad::registrar(auth()->user(), 'pdfs_nivel_consultados', 'Se generaron PDFs de credenciales para un nivel.', [
            'nivel_educativo_id' => $nivelEducativo->id,
            'cantidad_alumnos' => $alumnos->count(),
        ]);

        return back()->with('success', 'PDFs generados para todos los alumnos.');
    }

    public function enviarCorreoConPdf(Request $request, $id)
    {
        $request->validate(['destinatario' => 'required|in:padre,madre,ambos']);
        $destinatario = $request->input('destinatario');

        // Obtener al alumno con sus familiares
        $alumno = Alumno::with('familiares')->findOrFail($id);
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);

        // Filtrar al familiar por tipo (Padre o Madre)
        $padre = $alumno->familiares->firstWhere('tipo_familiar', 'Padre');
        $madre = $alumno->familiares->firstWhere('tipo_familiar', 'Madre');

        // Generar el PDF
        $pdf = Pdf::loadView('admin.pdf', compact('alumno'));

        // Establecer el asunto del correo
        $asunto = 'Datos de Acceso a Plataformas Digitales';

        // Verificar si el padre tiene correo
        if (in_array($destinatario, ['padre', 'ambos'], true) && $padre && ! empty($padre->correo)) {
            // Crear el mensaje para el padre
            $mensaje = "Estimado Sr/Sra. {$padre->nombre} {$padre->apellido_paterno} {$padre->apellido_materno},\n\n";
            $mensaje .= "Adjunto el PDF con los datos de acceso de su hijo(a) {$alumno->nombre} {$alumno->apellido_paterno} {$alumno->apellido_materno}.\n\n";
            $mensaje .= "Atentamente,\n\nEl equipo de la escuela.";

            // Enviar el correo al padre, pasando el $padre como $familiar
            Mail::to($padre->correo)->send(new EnviarPdfConMensaje($mensaje, $alumno, $pdf, $asunto, $padre));
        }

        // Verificar si la madre tiene correo
        if (in_array($destinatario, ['madre', 'ambos'], true) && $madre && ! empty($madre->correo)) {
            // Crear el mensaje para la madre
            $mensaje = "Estimada Sra. {$madre->nombre} {$madre->apellido_paterno} {$madre->apellido_materno},\n\n";
            $mensaje .= "Adjunto el PDF con los datos de acceso de su hijo(a) {$alumno->nombre} {$alumno->apellido_paterno} {$alumno->apellido_materno}.\n\n";
            $mensaje .= "Atentamente,\n\nEl equipo de la escuela.";

            // Enviar el correo a la madre, pasando el $madre como $familiar
            Mail::to($madre->correo)->send(new EnviarPdfConMensaje($mensaje, $alumno, $pdf, $asunto, $madre));
        }

        HistorialSeguridad::registrar(auth()->user(), 'credenciales_enviadas', 'Se enviaron credenciales de un alumno por correo.', [
            'alumno_id' => $alumno->id,
            'nivel_educativo_id' => $alumno->nivel_educativo_id,
            'destinatario' => $destinatario,
        ]);

        return redirect()->back()->with('success', 'El correo se ha enviado exitosamente a los familiares.');
    }

    public function enviarCorreoATodos($nivel)
    {
        $nivelEducativo = NivelEducativo::where('nombre', $nivel)->firstOrFail();
        NivelAccess::ensureCanAccess(auth()->user(), $nivelEducativo->id);

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
            if ($padre && ! empty($padre->correo)) {
                // Crear el mensaje para el padre
                $mensaje = "Estimado Sr/Sra. {$padre->nombre} {$padre->apellido_paterno} {$padre->apellido_materno},\n\n";
                $mensaje .= "Adjunto el PDF con los datos de acceso de su hijo(a) {$alumno->nombre} {$alumno->apellido_paterno} {$alumno->apellido_materno}.\n\n";
                $mensaje .= "Atentamente,\n\nEl equipo de la escuela.";

                // Enviar el correo al padre
                Mail::to($padre->correo)->send(new EnviarPdfConMensaje($mensaje, $alumno, $pdf, $asunto, $padre));
            }

            // Verificar si la madre tiene correo
            if ($madre && ! empty($madre->correo)) {
                // Crear el mensaje para la madre
                $mensaje = "Estimada Sra. {$madre->nombre} {$madre->apellido_paterno} {$madre->apellido_materno},\n\n";
                $mensaje .= "Adjunto el PDF con los datos de acceso de su hijo(a) {$alumno->nombre} {$alumno->apellido_paterno} {$alumno->apellido_materno}.\n\n";
                $mensaje .= "Atentamente,\n\nEl equipo de la escuela.";

                // Enviar el correo a la madre
                Mail::to($madre->correo)->send(new EnviarPdfConMensaje($mensaje, $alumno, $pdf, $asunto, $madre));
            }
        }

        HistorialSeguridad::registrar(auth()->user(), 'credenciales_nivel_enviadas', 'Se enviaron credenciales por correo a un nivel completo.', [
            'nivel_educativo_id' => $nivelEducativo->id,
            'cantidad_alumnos' => $alumnos->count(),
        ]);

        return redirect()->back()->with('success', 'Los correos se han enviado exitosamente a los familiares de todos los alumnos.');
    }

    // Función privada para obtener las plataformas según el nivel educativo
    private function obtenerPlataformasPorNivel($nivelEducativo)
    {
        return NivelEducativo::where('nombre', $nivelEducativo)->first()?->plataformas ?? collect();
    }

    public function generarPdfPorId($id)
    {
        // Obtener al alumno con las relaciones necesarias
        $alumno = Alumno::with(['nivelEducativo', 'grado', 'plataformas'])->findOrFail($id);
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);

        // Si no hay plataformas, devolver colección vacía en lugar de null
        $plataformasAlumno = $alumno->plataformas ?? collect();

        // Generar el PDF
        $pdf = Pdf::loadView('admin.pdf', compact('alumno', 'plataformasAlumno'));
        HistorialSeguridad::registrar(auth()->user(), 'pdf_credenciales_consultado', 'Se consultó el PDF de credenciales de un alumno.', [
            'alumno_id' => $alumno->id,
            'nivel_educativo_id' => $alumno->nivel_educativo_id,
        ]);

        // Descargar el PDF generado
        return $pdf->stream("credenciales_alumno_{$alumno->matricula}.pdf");
    }

    public function generarPdfPlataformas($nivel)
    {
        $nivelEducativo = NivelEducativo::where('nombre', $nivel)->firstOrFail();
        NivelAccess::ensureCanAccess(auth()->user(), $nivelEducativo->id);

        // Obtener las plataformas asociadas al nivel educativo
        $plataformas = $this->obtenerPlataformasPorNivel($nivel);

        // Validar que el nivel tiene plataformas
        if ($plataformas->isEmpty()) {
            abort(404, "No hay plataformas registradas para el nivel: $nivel");
        }

        // Generar el PDF con la vista 'admin.plataformas-pdf'
        $pdf = Pdf::loadView('admin.plataformas-pdf', compact('nivel', 'plataformas'));
        HistorialSeguridad::registrar(auth()->user(), 'pdf_plataformas_consultado', 'Se consultó el PDF de plataformas de un nivel.', [
            'nivel_educativo_id' => $nivelEducativo->id,
        ]);

        // Retornar el PDF generado
        return $pdf->stream("plataformas_nivel_{$nivel}.pdf");
    }

    public function select()
    {
        $niveles = NivelAccess::allowedLevels(auth()->user());

        return view('admin.select', compact('niveles'));
    }

    public function nivelesShow(Request $request, $nivelId)
    {
        $nivelEducativo = NivelEducativo::findOrFail($nivelId);
        NivelAccess::ensureCanAccess(auth()->user(), $nivelEducativo->id);

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
        $niveles = NivelAccess::allowedLevels(auth()->user());

        // Devolver la vista con los niveles
        return view('admin.selectadmin', compact('niveles'));
    }

    public function showNivelAlumnos(Request $request, $nivelId)
    {
        // Buscar el nivel educativo específico
        $nivel = NivelEducativo::findOrFail($nivelId);
        NivelAccess::ensureCanAccess(auth()->user(), $nivel->id);

        // Obtener los grados disponibles para el nivel educativo
        $grados = Grado::where('nivel_educativo_id', $nivelId)->get();

        // Inicializar la consulta para los alumnos y ordenarla por la fecha de creación (más reciente primero)
        $alumnos = Alumno::where('nivel_educativo_id', $nivelId)->orderBy('created_at', 'desc');

        // Aplicar filtros de grado y sección si se pasan desde la vista
        if ($request->has('grado') && $request->grado != '') {
            $alumnos = $alumnos->where('grado_id', $request->grado);
        }

        if ($request->has('seccion') && $request->seccion != '') {
            $alumnos = $alumnos->where('seccion', 'like', '%'.$request->seccion.'%');
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
        $alumnoIds = array_values(array_unique(array_map('intval', (array) $request->input('alumnos_ids', []))));
        if ($alumnoIds === []) {
            return response()->json(['success' => false, 'message' => 'Selecciona un grupo completo para promover.'], 422);
        }

        $alumnos = Alumno::with('grado')->whereIn('id', $alumnoIds)->get();

        abort_unless($alumnos->count() === count($alumnoIds), 403, 'No tienes permisos para promover uno de los alumnos seleccionados.');
        foreach ($alumnos as $alumno) {
            NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);
        }

        $grupo = $alumnos->map(fn (Alumno $alumno) => implode('|', [$alumno->nivel_educativo_id, $alumno->grado_id, $alumno->seccion]))->unique();
        if ($grupo->count() !== 1) {
            return response()->json(['success' => false, 'message' => 'Solo puedes promover un grupo a la vez: mismo nivel, grado y sección.'], 422);
        }

        $alumnoReferencia = $alumnos->first();
        $totalDelGrupo = Alumno::where('nivel_educativo_id', $alumnoReferencia->nivel_educativo_id)
            ->where('grado_id', $alumnoReferencia->grado_id)
            ->where('seccion', $alumnoReferencia->seccion)
            ->count();
        if ($alumnos->count() !== $totalDelGrupo) {
            return response()->json(['success' => false, 'message' => "Debes seleccionar a todo el grupo ({$totalDelGrupo} alumnos) antes de promoverlo."], 422);
        }

        $siguienteGrado = $this->getNextGrado($alumnoReferencia);
        if (! $siguienteGrado) {
            $this->egresarGrupo($alumnos);

            HistorialSeguridad::registrar(auth()->user(), 'grupo_egresado', 'Se registró el egreso de un grupo que concluyó el último grado.', [
                'cantidad_alumnos' => $alumnos->count(),
                'grado_origen' => $alumnoReferencia->grado?->nombre,
                'seccion' => $alumnoReferencia->seccion,
            ]);

            return response()->json(['success' => true, 'message' => 'El grupo concluyó el último grado y fue registrado en Egresados.']);
        }
        NivelAccess::ensureCanAccess(auth()->user(), $siguienteGrado->nivel_educativo_id);

        DB::transaction(function () use ($alumnos, $siguienteGrado) {
            foreach ($alumnos as $alumno) {
                $gradoAnterior = $alumno->grado?->nombre ?? 'Sin grado';
                $alumno->update([
                    'grado_id' => $siguienteGrado->id,
                    'nivel_educativo_id' => $siguienteGrado->nivel_educativo_id,
                ]);
                HistorialAlumno::create([
                    'alumno_id' => $alumno->id,
                    'alumno_nombre' => "{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}",
                    'user_id' => auth()->id(),
                    'accion' => 'promocion',
                    'cambios' => ['grado' => ['antes' => $gradoAnterior, 'despues' => $siguienteGrado->nombre]],
                    'fecha' => now(),
                ]);
            }
        });

        HistorialSeguridad::registrar(auth()->user(), 'grupo_promovido', 'Se promovió un grupo completo al siguiente grado.', [
            'cantidad_alumnos' => $alumnos->count(),
            'grado_origen' => $alumnoReferencia->grado?->nombre,
            'grado_destino' => $siguienteGrado->nombre,
            'seccion' => $alumnoReferencia->seccion,
        ]);

        return response()->json(['success' => true, 'message' => "El grupo fue promovido a {$siguienteGrado->nombre} correctamente."]);
    }

    private function getNextGrado(Alumno $alumno)
    {
        // Obtener el grado actual
        $gradoActual = Grado::find($alumno->grado_id);

        if (! $gradoActual) {
            return null;
        }

        if ($gradoActual->orden_promocion === null) {
            return null;
        }

        // The sequence is managed explicitly, never inferred from a database ID.
        return Grado::where('orden_promocion', $gradoActual->orden_promocion + 1)
            ->first();
    }

    /** Move a completed group into the historical graduates archive. */
    private function egresarGrupo($alumnos): void
    {
        DB::transaction(function () use ($alumnos) {
            foreach ($alumnos as $alumno) {
                $registro = DB::table('alumnos')->where('id', $alumno->id)->first();
                if (! $registro) {
                    continue;
                }

                $datosArchivo = (array) $registro;
                $datosArchivo['status'] = 0;
                $datosArchivo['tipo_archivo'] = 'egreso';
                $datosArchivo['fecha_archivo'] = now();
                $datosArchivo['fecha_egreso'] = now();
                DB::table('alumnos_archivados')->insert($datosArchivo);

                $this->copiarRelacionArchivo('alumno_plataforma', 'alumno_plataforma_archivados', $alumno->id);
                $this->copiarRelacionArchivo('contactos', 'contactos_archivados', $alumno->id);
                $this->copiarRelacionArchivo('familiares', 'familiares_archivados', $alumno->id);
                $this->copiarRelacionArchivo('hermanos', 'hermanos_archivados', $alumno->id);

                DB::table('alumno_plataforma')->where('alumno_id', $alumno->id)->delete();
                DB::table('contactos')->where('alumno_id', $alumno->id)->delete();
                DB::table('familiares')->where('alumno_id', $alumno->id)->delete();
                DB::table('hermanos')->where('alumno_id', $alumno->id)->delete();

                HistorialAlumno::create([
                    'alumno_id' => $alumno->id,
                    'alumno_nombre' => "{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}",
                    'user_id' => auth()->id(),
                    'accion' => 'egreso',
                    'motivo' => 'Conclusión del último grado escolar.',
                    'fecha' => now(),
                ]);

                DB::table('alumnos')->where('id', $alumno->id)->delete();
            }
        });
    }

    private function copiarRelacionArchivo(string $origen, string $destino, int $alumnoId): void
    {
        $registros = DB::table($origen)->where('alumno_id', $alumnoId)->get()
            ->map(fn ($registro) => (array) $registro)->all();

        if ($registros !== []) {
            DB::table($destino)->upsert($registros, ['id']);
        }
    }

    public function mostrarFormularioMoverGrupos(Request $request)
    {
        $nivelIds = NivelAccess::allowedIds(auth()->user());
        $grados = Grado::whereIn('nivel_educativo_id', $nivelIds)->get();
        $secciones = Seccion::whereIn('grado_id', $grados->pluck('id'))->where('activo', true)->orderBy('nombre')->get();
        $niveles = NivelEducativo::whereIn('id', $nivelIds)->get();

        // Filtros de grado y nivel
        $grado_id = $request->grado_id;
        $nivel_educativo_id = $request->nivel_educativo_id;

        if ($nivel_educativo_id) {
            NivelAccess::ensureCanAccess(auth()->user(), (int) $nivel_educativo_id);
        }

        // Obtener los alumnos filtrados por grado y nivel educativo, si se han proporcionado
        $alumnos = Alumno::whereIn('nivel_educativo_id', $nivelIds)->when($grado_id, function ($query) use ($grado_id) {
            return $query->where('grado_id', $grado_id);
        })
            ->when($nivel_educativo_id, function ($query) use ($nivel_educativo_id) {
                return $query->where('nivel_educativo_id', $nivel_educativo_id);
            })
            ->orderBy('created_at', 'desc')  // Ordenar desde el más reciente
            ->get();  // Paginación de 10 alumnos por página

        // Pasar los datos a la vista
        return view('admin.movergrupos', compact('grados', 'secciones', 'niveles', 'alumnos', 'grado_id', 'nivel_educativo_id'));
    }

    /**
     * Show a read-only checklist before performing end-of-school-year actions.
     */
    public function cierreEscolar()
    {
        abort_unless(auth()->user()->hasAnyRole(['SuperAdmin', 'ControlEscolar']), 403);

        $grupos = Alumno::query()
            ->selectRaw('nivel_educativo_id, grado_id, seccion, COUNT(*) as total_alumnos')
            ->groupBy('nivel_educativo_id', 'grado_id', 'seccion')
            ->with(['nivelEducativo', 'grado'])
            ->orderBy('nivel_educativo_id')
            ->orderBy('grado_id')
            ->orderBy('seccion')
            ->get()
            ->map(function ($grupo) {
                $grupo->grado_destino = $grupo->grado?->orden_promocion === null
                    ? null
                    : Grado::where('orden_promocion', $grupo->grado->orden_promocion + 1)->first();

                return $grupo;
            });

        return view('admin.cierre-escolar', compact('grupos'));
    }

    public function obtenerGradosYNiveles(Request $request)
    {
        // Validar los datos entrantes (opcional, pero recomendable)
        $request->validate([
            'niveles' => 'array',
            'grados' => 'array',
        ]);

        $nivelIdsPermitidos = NivelAccess::allowedIds(auth()->user());
        $nivelesSolicitados = array_map('intval', $request->input('niveles', []));
        $nivelIds = $nivelesSolicitados === [] ? $nivelIdsPermitidos : array_values(array_intersect($nivelesSolicitados, $nivelIdsPermitidos));

        abort_unless(
            count($nivelIds) === count($nivelesSolicitados) || $nivelesSolicitados === [],
            403,
            'No tienes permisos para consultar uno de los niveles seleccionados.'
        );

        // Obtener niveles filtrados
        $niveles = NivelEducativo::whereIn('id', $nivelIds)->get();

        // Obtener grados filtrados
        $grados = Grado::whereIn('nivel_educativo_id', $nivelIds)
            ->when($request->filled('grados'), fn ($query) => $query->whereIn('id', $request->grados))
            ->get();

        // Obtener alumnos que pertenecen a esos niveles y grados
        $alumnos = Alumno::whereIn('nivel_educativo_id', $nivelIds)
            ->when($request->grados, function ($query) use ($request) {
                return $query->whereIn('grado_id', $request->grados);
            })
            ->get();

        return response()->json([
            'niveles' => $niveles,
            'grados' => $grados,
            'alumnos' => $alumnos,
        ]);
    }

    public function assignRoles()
    {
        if (! auth()->user()->hasRole('SuperAdmin')) {
            abort(403);  // Acceso denegado
        }

        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::all();

        return view('admin.assign-roles', compact('users', 'roles'));
    }

    // app/Http/Controllers/AdminController.php

    public function removeRole($userId, $roleId)
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);
        $user = User::find($userId);
        $role = Role::find($roleId);

        if (! $user || ! $role) {
            return redirect()->route('admin.assignRoles')->with('error', 'Usuario o rol no encontrado.');
        }

        abort_if($user->is(auth()->user()), 403, 'No puedes retirar tus propios roles.');

        $user->removeRole($role);
        HistorialSeguridad::registrar(auth()->user(), 'rol_retirado', "Se retiró el rol {$role->name} a {$user->name}.", [
            'usuario_id' => $user->id,
            'rol' => $role->name,
        ]);

        return redirect()->route('admin.assignRoles')->with('success', 'Rol eliminado correctamente.');
    }

    public function saveAssignedRoles(Request $request)
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);
        $user = User::findOrFail($request->user_id);
        abort_if($user->is(auth()->user()), 403, 'No puedes modificar tus propios roles.');
        $user->syncRoles($request->roles);  // Asignar los roles seleccionados
        HistorialSeguridad::registrar(auth()->user(), 'roles_actualizados', "Se actualizaron los roles de {$user->name}.", [
            'usuario_id' => $user->id,
            'roles' => $request->roles ?? [],
        ]);

        return redirect()->route('admin.assignRoles')->with('success', 'Roles asignados correctamente');
    }

    /**
     * Disable a staff account without removing its student and security history.
     */
    public function cambiarEstadoUsuario(User $user)
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);
        abort_if($user->is(auth()->user()), 403, 'No puedes desactivar tu propia cuenta.');

        $user->update(['activo' => ! $user->activo]);
        $evento = $user->activo ? 'usuario_activado' : 'usuario_desactivado';
        $descripcion = $user->activo
            ? "Se reactivó la cuenta de {$user->name}."
            : "Se desactivó la cuenta de {$user->name}.";

        HistorialSeguridad::registrar(auth()->user(), $evento, $descripcion, [
            'usuario_id' => $user->id,
            'correo' => $user->email,
        ]);

        return redirect()->route('admin.assignRoles')->with('success', "La cuenta de {$user->name} fue ".($user->activo ? 'reactivada' : 'desactivada').'.');
    }

    public function historial()
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);

        $historial = HistorialAlumno::with('usuario')
            ->orderByDesc('fecha')
            ->paginate(25);

        return view('admin.historial', compact('historial'));
    }

    public function historialSeguridad()
    {
        abort_unless(auth()->user()->hasRole('SuperAdmin'), 403);

        $eventos = HistorialSeguridad::with('usuario')
            ->orderByDesc('fecha')
            ->paginate(30);

        return view('admin.historial-seguridad', compact('eventos'));
    }
}

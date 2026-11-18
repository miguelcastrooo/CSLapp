<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AlumnoArchivado;
use App\Models\AlumnoPlataforma;
use App\Models\BitacoraContactoFamiliar;
use App\Models\Contacto;
use App\Models\Familiar;
use App\Models\Grado;
use App\Models\Hermano;
use App\Models\HistorialAlumno;
use App\Models\NivelEducativo;
use App\Models\Plataforma;
use App\Models\Seccion;
use App\Support\NivelAccess;
use App\Services\NotificacionesEscolares;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumnoController extends Controller
{
    public function __construct(private readonly NotificacionesEscolares $notificaciones)
    {
    }

    private function registrarHistorial(int $alumnoId, string $alumnoNombre, string $accion, ?string $motivo = null, ?array $cambios = null): void
    {
        HistorialAlumno::create([
            'alumno_id' => $alumnoId,
            'alumno_nombre' => $alumnoNombre,
            'user_id' => auth()->id(),
            'accion' => $accion,
            'motivo' => $motivo,
            'cambios' => $cambios,
            'fecha' => now(),
        ]);
    }

    private function resumenRelaciones(Alumno $alumno): array
    {
        $alumno->loadMissing(['contactos', 'familiares', 'hermanos', 'alumnoPlataforma.plataforma']);

        return [
            'contactos' => $alumno->contactos->map(fn ($contacto) => [
                'id' => $contacto->id,
                'nombre' => $contacto->nombre,
                'telefono' => $contacto->telefono,
                'correo' => $contacto->correo,
                'tipo_contacto' => $contacto->tipo_contacto,
            ])->sortBy('id')->values()->all(),
            'familiares' => $alumno->familiares->map(fn ($familiar) => [
                'id' => $familiar->id,
                'tipo_familiar' => $familiar->tipo_familiar,
                'nombre' => $familiar->nombre,
                'apellido_paterno' => $familiar->apellido_paterno,
                'apellido_materno' => $familiar->apellido_materno,
                'celular' => $familiar->celular,
                'correo' => $familiar->correo,
            ])->sortBy('id')->values()->all(),
            'hermanos' => $alumno->hermanos->map(fn ($hermano) => [
                'id' => $hermano->id,
                'nombre' => $hermano->nombre,
                'apellido_paterno' => $hermano->apellido_paterno,
                'apellido_materno' => $hermano->apellido_materno,
                'edad' => $hermano->edad,
            ])->sortBy('id')->values()->all(),
            'plataformas' => $alumno->alumnoPlataforma->map(fn ($plataforma) => [
                'id' => $plataforma->id,
                'plataforma' => $plataforma->plataforma->nombre ?? 'Sin nombre',
                'usuario' => $plataforma->usuario,
            ])->sortBy('id')->values()->all(),
        ];
    }

    private function camposOpcionalesConfirmables(): array
    {
        return [
            'contacto_alterno' => 'Contacto alterno',
            'tercer_contacto' => 'Tercer contacto de emergencia',
            'telefono_fijo_familiar' => 'Teléfono fijo de familiar',
            'alergias_padecimientos' => 'Alergias o padecimientos',
            'plataforma_asignada' => 'Plataforma asignada',
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $niveles = NivelAccess::allowedLevels($user);
        $nivelIds = $niveles->pluck('id')->all();
        $query = Alumno::query()->with('nivelEducativo')->whereIn('nivel_educativo_id', $nivelIds);

        // Si hay un parámetro de búsqueda, aplicarlo a la consulta
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                    ->orWhere('matricula', 'like', "%$search%")
                    ->orWhere('correo_familia', 'like', "%$search%");
            });
        }

        // Aplicar filtro por nivel educativo si se seleccionó uno
        if ($request->has('nivel')) {
            $nivelId = (int) $request->input('nivel');
            NivelAccess::ensureCanAccess($user, $nivelId);
            $query->where('nivel_educativo_id', $nivelId);
        }

        // Obtener los alumnos paginados
        $alumnos = $query->orderBy('created_at', 'desc')->paginate(10);

        $grados = Grado::with('nivelEducativo')->whereIn('nivel_educativo_id', $nivelIds)->where('activo', true)->orderBy('nombre')->get();
        $secciones = Seccion::whereIn('grado_id', $grados->pluck('id'))->where('activo', true)->orderBy('nombre')->get();

        return view('capturista.index', compact('alumnos', 'niveles', 'grados', 'secciones'));
    }

    public function search(Request $request)
    {
        $user = auth()->user();
        $raw = $request->get('search', '');
        $nivel = $request->get('nivel');
        $grado = $request->get('grado');
        $seccion = $request->get('seccion');
        $nivelIds = NivelAccess::allowedIds($user);

        if ($nivel) {
            NivelAccess::ensureCanAccess($user, (int) $nivel);
        }

        // 1. Normaliza: quita espacios extra y separa por palabras
        $tokens = collect(preg_split('/\s+/', trim($raw)))
            ->filter();                   // elimina strings vacíos

        $alumnos = Alumno::with(['nivelEducativo', 'grado', 'contactos', 'familiares'])
            ->withCount(['contactos', 'familiares', 'alumnoPlataforma'])
            ->whereIn('nivel_educativo_id', $nivelIds)

            // 2. Filtro por nivel (si viene)
            ->when($nivel, fn ($q) => $q->where('nivel_educativo_id', $nivel))
            ->when($grado, fn ($q) => $q->where('grado_id', $grado))
            ->when($seccion, fn ($q) => $q->where('seccion', $seccion))

            // 3. Filtro por cada token
            ->when($tokens->isNotEmpty(), function ($q) use ($tokens) {
                $q->where(function ($outer) use ($tokens) {
                    foreach ($tokens as $word) {
                        $outer->where(function ($inner) use ($word) {
                            $like = "%{$word}%";
                            $inner->where('matricula', 'like', $like)
                                ->orWhere('nombre', 'like', $like)
                                ->orWhere('apellidopaterno', 'like', $like)
                                ->orWhere('apellidomaterno', 'like', $like);
                        });
                    }
                });
            })

            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $alumnos->getCollection()->transform(function (Alumno $alumno) {
            $alumno->setAttribute('estado_expediente', $alumno->estadoExpediente());

            return $alumno;
        });

        return response()->json($alumnos);
    }

    public function selectSearch()
    {
        $niveles = NivelAccess::allowedLevels(auth()->user());
        $grados = Grado::with('nivelEducativo')->whereIn('nivel_educativo_id', $niveles->pluck('id'))->where('activo', true)->orderBy('nombre')->get();
        $secciones = Seccion::whereIn('grado_id', $grados->pluck('id'))->where('activo', true)->orderBy('nombre')->get();

        return view('capturista.selectsearch', compact('niveles', 'grados', 'secciones'));

    }

    public function select()
    {
        abort_unless(NivelAccess::hasFullAccess(auth()->user()), 403);
        $niveles = NivelAccess::allowedLevels(auth()->user());

        return view('capturista.select', compact('niveles'));
    }

    public function create($nivel_id)
    {
        abort_unless(NivelAccess::hasFullAccess(auth()->user()), 403);
        // Obtener el nivel educativo con su ID
        $nivel = NivelEducativo::findOrFail($nivel_id);
        NivelAccess::ensureCanAccess(auth()->user(), $nivel->id);

        $familiares = Familiar::all();
        $grados = Grado::where('nivel_educativo_id', $nivel->id)->where('activo', true)->get();
        $secciones = Seccion::whereIn('grado_id', $grados->pluck('id'))->where('activo', true)->orderBy('nombre')->get();

        // Pasar las variables a la vista, incluyendo el id del nivel educativo
        return view('capturista.create', compact('nivel', 'grados', 'secciones', 'nivel_id', 'familiares'));
    }

    public function store(Request $request)
    {
        abort_unless(NivelAccess::hasFullAccess(auth()->user()), 403);
        NivelAccess::ensureCanAccess(auth()->user(), (int) $request->nivel_educativo_id);
        // Validación de los datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidopaterno' => 'required|string|max:255',
            'apellidomaterno' => 'required|string|max:255',
            'contacto1nombre' => 'required|string|max:255',
            'telefono1' => 'required|string|max:15',
            'contacto1tipo' => 'required|string|max:255',
            'correo1' => 'required|email',
            'contacto2nombre' => 'nullable|required_with:telefono2,contacto2tipo,correo2|string|max:255',
            'telefono2' => 'nullable|required_with:contacto2nombre,contacto2tipo,correo2|string|max:15',
            'contacto2tipo' => 'nullable|required_with:contacto2nombre,telefono2,correo2|string|max:255',
            'correo2' => 'nullable|required_with:contacto2nombre,telefono2,contacto2tipo|email',
            'contacto3nombre' => 'nullable|required_with:telefono3,contacto3tipo,correo3|string|max:255',
            'telefono3' => 'nullable|required_with:contacto3nombre,contacto3tipo,correo3|string|max:15',
            'contacto3tipo' => 'nullable|required_with:contacto3nombre,telefono3,correo3|string|max:255',
            'correo3' => 'nullable|required_with:contacto3nombre,telefono3,contacto3tipo|email',
            'nivel_educativo_id' => 'required|exists:nivel_educativo,id',
            'grado_id' => 'required|exists:grados,id',
            'seccion' => 'nullable|string|max:50',
            'fecha_inscripcion' => 'required|date',
            'fecha_inicio' => 'required|date',
            'lugar_nacimiento' => 'required',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'edad_anios' => 'required|integer|max:50',
            'edad_meses' => 'required|integer|max:50',
            'sexo' => 'required|in:Masculino,Femenino,Sin Definir',
            'domicilio' => 'required',
            'cp' => 'required|numeric|digits_between:5,12',
            'cerrada' => 'nullable',
            'colonia' => 'nullable',
            'no_domicilio' => 'nullable|integer|max:100000',
            'ciudad' => 'required',
            'estado' => 'required',
            'enfermedades_alergias' => 'nullable|string',
            'pediatra_nombre' => 'nullable',
            'pediatra_telefono' => 'nullable|digits:10|numeric',
            'hermano1nombre' => 'nullable|string|max:255',
            'hermano1edad' => 'nullable|integer|min:0|max:50',
            'hermano2nombre' => 'nullable|string|max:255',
            'hermano2edad' => 'nullable|integer|min:0|max:50',
            'hermano3nombre' => 'nullable|string|max:255',
            'hermano3edad' => 'nullable|integer|min:0|max:50',
            'hermano4nombre' => 'nullable|string|max:255',
            'hermano4edad' => 'nullable|integer|min:0|max:50',
            'hermano5nombre' => 'nullable|string|max:255',
            'hermano5edad' => 'nullable|integer|min:0|max:50',
            'hermanos' => 'nullable|array',
            'hermanos.*.nombre' => 'nullable|string|max:255',
            'hermanos.*.apellido_paterno' => 'nullable|string|max:255',
            'hermanos.*.apellido_materno' => 'nullable|string|max:255',
            'hermanos.*.edad' => 'nullable|integer|min:0|max:50',
            'familiares' => 'nullable|array',
            'familiares.*.nombre' => 'nullable|string|max:255',
            'familiares.*.apellido_paterno' => 'nullable|string|max:255',
            'familiares.*.apellido_materno' => 'nullable|string|max:255',
            'familiares.*.correo' => 'nullable|email|max:255',
            'familiares.*.celular' => 'nullable|string|max:15',
            'familiares.*.telefono_fijo' => 'nullable|string|max:15',
        ]);

        // No se obliga a registrar padre, madre y tutor a la vez. Basta con
        // capturar a una persona responsable real; sólo se guardan las tarjetas
        // que tengan nombre.
        $tiposFamiliares = collect(['Padre', 'Madre', 'Tutor']);
        $familiaresRegistrados = $tiposFamiliares
            ->mapWithKeys(fn (string $tipo) => [$tipo => $request->input("familiares.{$tipo}", [])])
            ->filter(fn (array $datos) => filled($datos['nombre'] ?? null));

        if ($familiaresRegistrados->isEmpty()) {
            return back()->withInput()->withErrors([
                'familiares' => 'Registra al menos un familiar responsable: padre, madre o tutor.',
            ]);
        }

        foreach ($familiaresRegistrados as $tipo => $datos) {
            foreach (['apellido_paterno' => 'apellido paterno', 'apellido_materno' => 'apellido materno'] as $campo => $etiqueta) {
                if (blank($datos[$campo] ?? null)) {
                    return back()->withInput()->withErrors([
                        "familiares.{$tipo}.{$campo}" => "Completa el {$etiqueta} de {$tipo}.",
                    ]);
                }
            }
        }

        $correosContacto = collect([$request->correo1, $request->correo2, $request->correo3])
            ->filter()
            ->map(fn ($correo) => strtolower(trim($correo)));

        if ($correosContacto->count() !== $correosContacto->unique()->count()) {
            return back()
                ->withInput()
                ->withErrors(['correo1' => 'No repitas el mismo correo en los contactos del alumno.']);
        }

        abort_unless(
            Grado::whereKey($request->grado_id)
                ->where('nivel_educativo_id', $request->nivel_educativo_id)
                ->exists(),
            422,
            'El grado seleccionado no corresponde al nivel educativo.'
        );

        $this->validarSeccionDelGrado($request->seccion, (int) $request->grado_id);

        // Asignación de fecha_inicio: si no se recibe, se asigna la fecha actual
        $fecha_inicio = $request->fecha_inicio ?? now()->toDateString();

        // Obtener los últimos dos dígitos del año actual (Ejemplo: "25" para 2025)
        $año = now()->format('y');

        // Buscar el último alumno registrado en el año actual con la matrícula más alta
        $ultimoAlumno = Alumno::where('matricula', 'like', "$año%")
            ->orderBy('matricula', 'desc')
            ->first();

        if ($ultimoAlumno && preg_match("/^$año(\d{4})$/", $ultimoAlumno->matricula, $matches)) {
            $ultimoNumero = (int) $matches[1];
        } else {
            $ultimoNumero = 1723;
        }

        // Incrementar en 1 y formatear con 4 dígitos
        $nuevoNumero = str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);

        // Construir la matrícula final (Ejemplo: "251724" para el siguiente alumno)
        $matricula = $año.$nuevoNumero;

        // Crear credenciales solo para Classroom y Moodle
        $credenciales = $this->generarCredenciales(
            $request->nombre,
            $request->apellidopaterno,
            $matricula,
            $request->fecha_inscripcion
        );

        // Crear el alumno
        $alumno = Alumno::create([
            // Datos del alumno
            'matricula' => $matricula,
            'nombre' => $request->nombre,
            'apellidopaterno' => $request->apellidopaterno,
            'apellidomaterno' => $request->apellidomaterno,
            'nivel_educativo_id' => $request->nivel_educativo_id,
            'grado_id' => $request->grado_id,
            'seccion' => $request->seccion,
            'fecha_inscripcion' => $request->fecha_inscripcion,
            'fecha_inicio' => $fecha_inicio,
            'lugar_nacimiento' => $request->lugar_nacimiento,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'edad_anios' => $request->edad_anios,
            'edad_meses' => $request->edad_meses,
            'sexo' => $request->sexo,
            'domicilio' => $request->domicilio,
            'cp' => $request->cp,
            'cerrada' => $request->cerrada,
            'colonia' => $request->colonia,
            'no_domicilio' => $request->no_domicilio,
            'ciudad' => $request->ciudad,
            'estado' => $request->estado,
            'enfermedades_alergias' => $request->enfermedades_alergias,
            'pediatra_nombre' => $request->pediatra_nombre,
            'pediatra_telefono' => $request->pediatra_telefono,
        ]);

        if (! $alumno) {
            return redirect()->back()->with('error', 'Error al crear el alumno.');
        }

        if ($familiaresRegistrados->isNotEmpty()) {
            foreach ($familiaresRegistrados as $tipo => $datos) {
                Familiar::create([
                    'alumno_id' => $alumno->id,
                    'tipo_familiar' => $tipo,
                    'nombre' => $datos['nombre'],
                    'apellido_paterno' => $datos['apellido_paterno'] ?? null,
                    'apellido_materno' => $datos['apellido_materno'] ?? null,
                    'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                    'estado_civil' => $datos['estado_civil'] ?? null,
                    'domicilio' => $datos['domicilio'] ?? null,
                    'no_domicilio' => $datos['no_domicilio'] ?? null,
                    'cp' => $datos['cp'] ?? null,
                    'colonia' => $datos['colonia'] ?? null,
                    'ciudad' => $datos['ciudad'] ?? null,
                    'estado' => $datos['estado'] ?? null,
                    'telefono_fijo' => $datos['telefono_fijo'] ?? null,
                    'celular' => $datos['celular'] ?? null,
                    'correo' => $datos['correo'] ?? null,
                    'profesion' => $datos['profesion'] ?? null,
                    'ocupacion' => $datos['ocupacion'] ?? null,
                    'empresa_nombre' => $datos['empresa_nombre'] ?? null,
                    'empresa_telefono' => $datos['empresa_telefono'] ?? null,
                    'empresa_domicilio' => $datos['empresa_domicilio'] ?? null,
                    'empresa_ciudad' => $datos['empresa_ciudad'] ?? null,
                ]);
            }
        }

        // Relacionar los contactos
        $this->relacionarContactos($request, $alumno);

        // Guardar las credenciales en la tabla alumno_plataforma
        $this->guardarCredencialesPlataforma($alumno, $credenciales);

        // Relacionar los hermanos
        $this->relacionarHermanos($request, $alumno);

        $this->registrarHistorial($alumno->id, "{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}", 'alta');

        $this->notificaciones->notificarAlumno($alumno, 'alta');

        return redirect()->route('alumnos.edit', $alumno)
            ->with('success', "Alumno registrado correctamente. Matrícula asignada: {$alumno->matricula}. Desde este expediente puedes revisar los datos o descargar sus credenciales.");
    }

    protected function generarCredenciales(string $nombre, string $apellido, string $matricula, string $fechaInscripcion): array
    {
        // Se usan datos simples y sin acentos para evitar correos inválidos.
        $primerNombre = Str::lower(Str::ascii(explode(' ', trim($nombre))[0]));
        $primerApellido = Str::lower(Str::ascii(explode(' ', trim($apellido))[0]));

        // Generar el correo con el dominio @colegiosanluis.com.mx
        $emailClassroom = $primerNombre.$primerApellido.'@colegiosanluis.com.mx';

        // Comprobamos si el correo ya existe en la tabla alumno_plataforma
        $contador = 1;
        while (AlumnoPlataforma::where('usuario', $emailClassroom)->exists()) {
            $emailClassroom = $primerNombre.$primerApellido.$contador.'@colegiosanluis.com.mx';
            $contador++;
        }

        // Obtener el año de inscripción
        $anioInscripcion = date('y', strtotime($fechaInscripcion));

        // Generar las contraseñas
        $contraseñaClassroom = 'Csl$'.$primerNombre.$anioInscripcion;
        $usuarioMoodle = 'Csl-'.$matricula;
        $contraseñaMoodle = $matricula;

        // Devolvemos las credenciales generadas
        return [
            'usuario_classroom' => $emailClassroom,
            'contraseña_classroom' => $contraseñaClassroom,
            'usuario_moodle' => $usuarioMoodle,
            'contraseña_moodle' => $contraseñaMoodle,
        ];
    }

    /**
     * Guarda sólo credenciales automáticas de plataformas activas y asignadas
     * al nivel del alumno. Nunca depende de IDs internos como 1 o 2.
     */
    protected function guardarCredencialesPlataforma(Alumno $alumno, array $credenciales): void
    {
        $plataformas = Plataforma::query()
            ->where('activo', true)
            ->whereHas('niveles', fn ($query) => $query->whereKey($alumno->nivel_educativo_id))
            ->get();

        $automaticas = [
            'classroom' => ['usuario' => $credenciales['usuario_classroom'], 'contraseña' => $credenciales['contraseña_classroom']],
            'moodle' => ['usuario' => $credenciales['usuario_moodle'], 'contraseña' => $credenciales['contraseña_moodle']],
        ];

        foreach ($automaticas as $nombre => $datos) {
            $plataforma = $plataformas->first(fn (Plataforma $plataforma) => Str::contains(Str::lower($plataforma->nombre), $nombre));

            if ($plataforma) {
                $alumno->alumnoPlataforma()->updateOrCreate(['plataforma_id' => $plataforma->id], $datos);
            }
        }
    }

    public function relacionarContactos(Request $request, Alumno $alumno)
    {
        // Crear los datos de los contactos
        $contactos = [];

        // Definir los posibles contactos
        for ($i = 1; $i <= 3; $i++) {
            $nombreKey = "contacto{$i}nombre";
            $telefonoKey = "telefono{$i}";
            $correoKey = "correo{$i}";
            $tipoKey = "contacto{$i}tipo";

            if ($request->$nombreKey && $request->$correoKey) {
                $contactos[] = [
                    'nombre' => $request->$nombreKey,
                    'telefono' => $request->$telefonoKey,
                    'correo' => $request->$correoKey,
                    'tipo_contacto' => $request->$tipoKey,
                    'alumno_id' => $alumno->id,
                ];
            }
        }

        // Guardar los contactos en la base de datos
        if (! empty($contactos)) {
            Contacto::insert($contactos);
        }
    }

    private function relacionarHermanos(Request $request, Alumno $alumno)
    {

        foreach ($request->input('hermanos', []) as $hermano) {
            if (! empty($hermano['nombre'])) {
                Hermano::create([
                    'alumno_id' => $alumno->id,
                    'nombre' => $hermano['nombre'],
                    'apellido_paterno' => $hermano['apellido_paterno'] ?? $alumno->apellidopaterno,
                    'apellido_materno' => $hermano['apellido_materno'] ?? $alumno->apellidomaterno,
                    'edad' => $hermano['edad'] ?? null,
                ]);
            }
        }

        $apellido_paterno = $alumno->apellidopaterno;
        $apellido_materno = $alumno->apellidomaterno;

        // Recorrer del 1 al 5 (según los campos del formulario)
        for ($i = 1; $i <= 5; $i++) {
            $nombre = $request->input("hermano{$i}nombre");
            $edad = $request->input("hermano{$i}edad");

            // Si no hay nombre, no lo guardamos
            if (! $nombre) {
                continue;
            }

            // Crear o registrar el hermano
            Hermano::create([
                'alumno_id' => $alumno->id,
                'nombre' => $nombre,
                'apellido_paterno' => $apellido_paterno,
                'apellido_materno' => $apellido_materno,
                'edad' => $edad ?? null,
            ]);
        }
    }

    public function edit($id)
    {
        $alumno = Alumno::with(['confirmacionesOpcionales', 'bitacorasContacto.usuario'])->findOrFail($id);
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);
        $contactos = Contacto::where('alumno_id', $alumno->id)->get();
        $grados = Grado::where('nivel_educativo_id', $alumno->nivel_educativo_id)->where('activo', true)->get();
        $secciones = $this->seccionesDisponiblesParaGrado($alumno->grado_id);
        $niveles = NivelAccess::allowedLevels(auth()->user());
        $hermanos = $alumno->hermanos; // Obtener los hermanos relacionados con el alumno
        $familiares = Familiar::where('alumno_id', $id)
            ->orderByRaw("CASE
                WHEN tipo_familiar = 'Padre' THEN 1
                WHEN tipo_familiar = 'Madre' THEN 2
                WHEN tipo_familiar = 'Tutor' THEN 3
                ELSE 4
            END")
            ->get();

        return view('capturista.edit', compact('alumno', 'contactos', 'grados', 'secciones', 'niveles', 'hermanos', 'familiares') + [
            'estadoExpediente' => $alumno->estadoExpediente(),
            'camposOpcionalesConfirmables' => $this->camposOpcionalesConfirmables(),
            'puedeEditarExpediente' => ! auth()->user()->hasAnyRole(['CoordinacionPreescolar', 'CoordinacionPrimaria', 'CoordinacionSecundaria'])
                && auth()->user()->hasAnyRole(['SuperAdmin', 'ControlEscolar']),
        ]);
    }

    public function registrarContactoFamiliar(Request $request, Alumno $alumno)
    {
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);

        $data = $request->validate([
            'tipo' => 'required|in:llamada,correo,reunion,mensaje,otro',
            'resumen' => 'required|string|max:1500',
            'fecha_contacto' => 'required|date',
        ]);

        $registro = $alumno->bitacorasContacto()->create([
            ...$data,
            'user_id' => auth()->id(),
        ]);

        $this->registrarHistorial(
            $alumno->id,
            trim("{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}"),
            'contacto_familia',
            "Se registró {$registro->tipo} con la familia."
        );

        return back()->with('success', 'El contacto con la familia quedó registrado en el expediente.');
    }

    /**
     * Muestra el historial de comunicación en una pantalla separada para que el
     * expediente conserve una lectura corta y enfocada en los datos del alumno.
     */
    public function bitacoraContacto(Alumno $alumno)
    {
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);

        $alumno->load('bitacorasContacto.usuario');

        $puedeRegistrarBitacora = ! auth()->user()->hasAnyRole([
            'CoordinacionPreescolar',
            'CoordinacionPrimaria',
            'CoordinacionSecundaria',
        ]) && auth()->user()->hasAnyRole(['SuperAdmin', 'ControlEscolar']);

        return view('capturista.bitacora-contacto', compact('alumno', 'puedeRegistrarBitacora'));
    }

    public function confirmarCampoOpcional(Alumno $alumno, string $campo)
    {
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);
        abort_unless(array_key_exists($campo, $this->camposOpcionalesConfirmables()), 404);

        $alumno->confirmacionesOpcionales()->updateOrCreate(
            ['campo' => $campo],
            ['user_id' => auth()->id(), 'confirmado_en' => now()]
        );

        $this->registrarHistorial(
            $alumno->id,
            trim("{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}"),
            'dato_opcional_confirmado',
            "Se confirmó que no aplica o no se cuenta con: {$this->camposOpcionalesConfirmables()[$campo]}."
        );

        return back()->with('success', 'El dato opcional quedó confirmado como no disponible o no aplicable.');
    }

    public function reabrirCampoOpcional(Alumno $alumno, string $campo)
    {
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);
        abort_unless(array_key_exists($campo, $this->camposOpcionalesConfirmables()), 404);

        $alumno->confirmacionesOpcionales()->where('campo', $campo)->delete();

        return back()->with('success', 'El dato opcional volvió a quedar pendiente para completarse.');
    }

    public function getGrados($nivel_id)
    {
        NivelAccess::ensureCanAccess(auth()->user(), (int) $nivel_id);
        // Obtener los grados correspondientes al nivel educativo seleccionado
        $grados = Grado::where('nivel_educativo_id', $nivel_id)->where('activo', true)->get();

        // Devolver los grados como JSON
        return response()->json($grados);
    }

    public function getSecciones($grado_id)
    {
        $grado = Grado::findOrFail($grado_id);
        NivelAccess::ensureCanAccess(auth()->user(), $grado->nivel_educativo_id);

        return response()->json($this->seccionesDisponiblesParaGrado($grado->id));
    }

    /**
     * Combina las secciones administradas con las que ya existen en expedientes
     * antiguos. Así la transición al nuevo catálogo no deja selectores vacíos.
     */
    private function seccionesDisponiblesParaGrado(int $gradoId)
    {
        $catalogo = Seccion::where('grado_id', $gradoId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $historicas = Alumno::where('grado_id', $gradoId)
            ->whereNotNull('seccion')
            ->where('seccion', '!=', '')
            ->distinct()
            ->orderBy('seccion')
            ->pluck('seccion')
            ->map(fn (string $nombre) => ['id' => null, 'nombre' => $nombre]);

        return $catalogo
            ->map(fn (Seccion $seccion) => ['id' => $seccion->id, 'nombre' => $seccion->nombre])
            ->concat($historicas)
            ->unique('nombre')
            ->sortBy('nombre')
            ->values();
    }

    private function validarSeccionDelGrado(?string $seccion, int $gradoId): void
    {
        if (blank($seccion)) {
            return;
        }

        $haySeccionesConfiguradas = Seccion::where('grado_id', $gradoId)->exists();

        abort_unless(
            ! $haySeccionesConfiguradas || Seccion::where('grado_id', $gradoId)->where('nombre', $seccion)->exists(),
            422,
            'La sección seleccionada no corresponde al grado.'
        );
    }

    public function update(Request $request, $id)
    {
        // Obtener el alumno a actualizar
        $alumno = Alumno::find($id);

        if (! $alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado.');
        }

        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);

        $soloAccesosPlataforma = auth()->user()->hasAnyRole([
            'CoordinacionPreescolar',
            'CoordinacionPrimaria',
            'CoordinacionSecundaria',
        ]);

        // Coordinación consulta el expediente, pero puede mantener actualizados los
        // accesos de las plataformas de su propio nivel. Ningún otro dato recibido
        // desde el formulario se procesa para ese rol.
        if ($soloAccesosPlataforma) {
            $request->validate([
                'plataformas' => 'nullable|array',
                'plataformas.*.usuario' => 'nullable|string|max:255',
                'plataformas.*.contraseña' => 'nullable|string|max:255',
            ]);

            $actualizadas = $this->actualizarAccesosPlataformas($request, $alumno);

            if ($actualizadas > 0) {
                $this->registrarHistorial(
                    $alumno->id,
                    "{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}",
                    'accesos_plataformas_actualizados',
                    "Se actualizaron los accesos de {$actualizadas} plataforma(s), sin registrar credenciales."
                );
            }

            return back()->with('success', 'Los accesos de las plataformas se actualizaron correctamente.');
        }

        if ($request->filled('nivel_educativo_id')) {
            NivelAccess::ensureCanAccess(auth()->user(), (int) $request->nivel_educativo_id);
        }

        // Validación de los campos
        $request->validate($this->getValidationRules($id));
        $this->validarTelefonosFamiliaresModificados($request, $alumno);

        $nivelDestinoId = (int) ($request->nivel_educativo_id ?? $alumno->nivel_educativo_id);
        $gradoDestinoId = (int) ($request->grado_id ?? $alumno->grado_id);

        abort_unless(
            Grado::whereKey($gradoDestinoId)
                ->where('nivel_educativo_id', $nivelDestinoId)
                ->exists(),
            422,
            'El grado seleccionado no corresponde al nivel educativo.'
        );

        $seccionDestino = $request->has('seccion')
            ? $request->seccion
            : ($gradoDestinoId === $alumno->grado_id ? $alumno->seccion : null);

        $this->validarSeccionDelGrado($seccionDestino, $gradoDestinoId);

        // Asignación de la fecha de inicio
        $fecha_inicio = $request->fecha_inicio ?? now()->toDateString();

        $valoresAnteriores = $alumno->getOriginal();
        $relacionesAnteriores = $this->resumenRelaciones($alumno);

        // Asignar los valores recibidos de la solicitud
        $alumno->update([
            // La matrícula se asigna al alta o importación y no se modifica desde el expediente.
            'matricula' => $alumno->matricula,
            'nombre' => $request->nombre ?? $alumno->nombre,
            'apellidopaterno' => $request->apellidopaterno ?? $alumno->apellidopaterno,
            'apellidomaterno' => $request->apellidomaterno ?? $alumno->apellidomaterno,
            'usuario_hmh' => $request->usuario_hmh ?? $alumno->usuario_hmh,
            'contraseña_hmh' => $request->contraseña_hmh ?? $alumno->contraseña_hmh,
            'usuario_mathletics' => $request->usuario_mathletics ?? $alumno->usuario_mathletics,
            'contraseña_mathletics' => $request->contraseña_mathletics ?? $alumno->contraseña_mathletics,
            'usuario_progrentis' => $request->usuario_progrentis ?? $alumno->usuario_progrentis,
            'contraseña_progrentis' => $request->contraseña_progrentis ?? $alumno->contraseña_progrentis,
            'nivel_educativo_id' => $request->nivel_educativo_id ?? $alumno->nivel_educativo_id,
            'grado_id' => $request->grado_id ?? $alumno->grado_id,
            'fecha_inscripcion' => $request->fecha_inscripcion ?? $alumno->fecha_inscripcion,
            'fecha_inicio' => $fecha_inicio,
            'seccion' => $seccionDestino,
            'lugar_nacimiento' => $request->lugar_nacimiento ?? $alumno->lugar_nacimiento,
            'fecha_nacimiento' => $request->fecha_nacimiento ?? $alumno->fecha_nacimiento,
            'edad_anios' => $request->edad_anios ?? $alumno->edad_anios,
            'edad_meses' => $request->edad_meses ?? $alumno->edad_meses,
            'sexo' => $request->sexo ?? $alumno->sexo,
            'domicilio' => $request->domicilio ?? $alumno->domicilio,
            'cp' => $request->cp ?? $alumno->cp,
            'cerrada' => $request->cerrada ?? $alumno->cerrada,
            'colonia' => $request->colonia ?? $alumno->colonia,
            'ciudad' => $request->ciudad ?? $alumno->ciudad,
            'estado' => $request->estado ?? $alumno->estado,
            'enfermedades_alergias' => $request->enfermedades_alergias ?? $alumno->enfermedades_alergias,
            'pediatra_nombre' => $request->pediatra_nombre ?? $alumno->pediatra_nombre,
            'pediatra_telefono' => $request->pediatra_telefono ?? $alumno->pediatra_telefono,
            'no_domicilio' => $request->no_domicilio ?? $alumno->no_domicilio,
        ]);

        $cambios = collect($alumno->getChanges())
            ->except([
                'updated_at',
                'contraseña_classroom',
                'contraseña_moodle',
                'contraseña_hmh',
                'contraseña_mathletics',
                'contraseña_progrentis',
            ])
            ->map(fn ($despues, $campo) => ['antes' => $valoresAnteriores[$campo] ?? null, 'despues' => $despues])
            ->all();

        // Actualizar plataformas
        $this->actualizarPlataformas($request, $alumno);

        // Actualizar familiares
        $this->actualizarFamiliares($request, $alumno);

        // Actualizar hermanos
        $this->actualizarHermanos($request, $alumno);

        // Actualizar contactos
        $this->actualizarContactos($request, $alumno);

        $alumno->unsetRelation('contactos');
        $alumno->unsetRelation('familiares');
        $alumno->unsetRelation('hermanos');
        $alumno->unsetRelation('alumnoPlataforma');
        $relacionesActuales = $this->resumenRelaciones($alumno);

        foreach ($relacionesActuales as $relacion => $datosActuales) {
            if ($relacionesAnteriores[$relacion] !== $datosActuales) {
                $cambios[$relacion] = [
                    'antes' => $relacionesAnteriores[$relacion],
                    'despues' => $datosActuales,
                ];
            }
        }

        if ($cambios !== []) {
            $this->registrarHistorial($alumno->id, "{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}", 'actualizacion', null, $cambios);
        }

        // Redirigir según el rol del usuario
        if (auth()->user()->hasRole('SuperAdmin')) {
            return redirect()->route('alumnos.edit', ['alumno' => $alumno->id])->with('success', 'Alumno actualizado correctamente.');
        }

        return back()->with('success', 'Alumno actualizado correctamente.');
    }

    private function getValidationRules($id)
    {
        return [
            'matricula' => 'nullable|string|max:100|unique:alumnos,matricula,'.$id,
            'nombre' => 'nullable|string|max:255',
            'apellidopaterno' => 'nullable|string|max:255',
            'apellidomaterno' => 'nullable|string|max:255',
            'usuario_classroom' => 'nullable|string|max:255',
            'contraseña_classroom' => 'nullable|string|max:255',
            'usuario_moodle' => 'nullable|string|max:255',
            'contraseña_moodle' => 'nullable|string|max:255',
            'usuario_hmh' => 'nullable|string|max:255',
            'contraseña_hmh' => 'nullable|string|max:255',
            'usuario_mathletics' => 'nullable|string|max:255',
            'contraseña_mathletics' => 'nullable|string|max:255',
            'usuario_progrentis' => 'nullable|string|max:255',
            'contraseña_progrentis' => 'nullable|string|max:255',
            'nivel_educativo_id' => 'nullable|exists:nivel_educativo,id',
            'grado_id' => 'nullable|exists:grados,id',
            'fecha_inscripcion' => 'nullable|date',
            'fecha_inicio' => 'nullable|date',
            'seccion' => 'nullable|string|max:50',
            'lugar_nacimiento' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'edad_anios' => 'nullable|numeric',
            'edad_meses' => 'nullable|numeric',
            'sexo' => 'required|in:Masculino,Femenino,Sin Definir',
            'domicilio' => 'nullable|string|max:255',
            'cp' => 'nullable|string|max:5',
            'cerrada' => 'nullable|string|max:255',
            'colonia' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'enfermedades_alergias' => 'nullable|string|max:255',
            'pediatra_nombre' => 'nullable|string|max:255',
            'pediatra_telefono' => 'nullable|digits:10',
            'no_domicilio' => 'nullable|string|max:255',
            'familiares' => 'nullable|array',
            'familiares.*.nombre' => 'nullable|string|max:255',
            'familiares.*.apellido_paterno' => 'nullable|string|max:255',
            'familiares.*.apellido_materno' => 'nullable|string|max:255',
            'familiares.*.fecha_nacimiento' => 'nullable|date',
            'familiares.*.estado_civil' => 'nullable|string|max:255',
            'familiares.*.domicilio' => 'nullable|string|max:255',
            'familiares.*.no_domicilio' => 'nullable|string|max:255',
            'familiares.*.cp' => 'nullable|string|max:5',
            'familiares.*.colonia' => 'nullable|string|max:255',
            'familiares.*.ciudad' => 'nullable|string|max:255',
            'familiares.*.estado' => 'nullable|string|max:255',
            // Los expedientes históricos pueden conservar teléfonos antiguos. Los datos
            // nuevos o modificados se validan después con un mensaje orientado al usuario.
            'familiares.*.telefono_fijo' => 'sometimes|nullable|string|max:15',
            'familiares.*.celular' => 'sometimes|nullable|string|max:15',
            'familiares.*.correo' => 'nullable|email',
            'familiares.*.profesion' => 'nullable|string|max:255',
            'familiares.*.ocupacion' => 'nullable|string|max:255',
            'familiares.*.empresa_nombre' => 'nullable|string|max:255',
            'familiares.*.empresa_telefono' => 'nullable|digits:10',
            'familiares.*.empresa_domicilio' => 'nullable|string|max:255',
            'familiares.*.empresa_ciudad' => 'nullable|string|max:255',
            'familiares.*.tipo_familiar' => 'string|in:Padre,Madre,Tutor|max:255',
            'hermanos' => 'nullable|array',
            'hermanos.*.nombre' => 'nullable|string|max:255',
            'hermanos.*.apellido_paterno' => 'nullable|string|max:255',
            'hermanos.*.apellido_materno' => 'nullable|string|max:255',
            'hermanos.*.edad' => 'nullable|numeric',
            'contactos.*.nombre' => 'nullable|string|max:255',
            'contactos.*.telefono' => 'nullable|string|max:15',
            'contactos.*.tipo_contacto' => 'nullable|string|max:255',
            'contactos.*.correo' => 'nullable|email',
        ];
    }

    /**
     * Valida únicamente teléfonos de familiares que se modificaron en esta edición.
     * Esto evita bloquear una consulta/guardado por datos heredados del expediente.
     */
    private function validarTelefonosFamiliaresModificados(Request $request, Alumno $alumno): void
    {
        $familiaresActuales = $alumno->familiares->keyBy('id');
        $errores = [];

        foreach ($request->input('familiares', []) as $indice => $familiar) {
            $registroAnterior = $familiaresActuales->get((int) ($familiar['id'] ?? 0));

            foreach (['celular' => 'teléfono celular', 'telefono_fijo' => 'teléfono fijo'] as $campo => $etiqueta) {
                $valor = trim((string) ($familiar[$campo] ?? ''));
                $valorAnterior = (string) ($registroAnterior?->{$campo} ?? '');

                if ($valor === '' || $valor === $valorAnterior) {
                    continue;
                }

                if (! preg_match('/^\\d{10}$/', $valor)) {
                    $errores["familiares.{$indice}.{$campo}"] = "El {$etiqueta} debe tener 10 dígitos.";
                }
            }
        }

        if ($errores !== []) {
            throw \Illuminate\Validation\ValidationException::withMessages($errores);
        }
    }

    private function actualizarPlataformas(Request $request, Alumno $alumno)
    {
        $plataformas = $request->input('plataformas', []);
        $plataformasPermitidas = Plataforma::query()
            ->where('activo', true)
            ->whereHas('niveles', fn ($query) => $query->whereKey($alumno->nivel_educativo_id))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($plataformas as $plataformaId => $datos) {
            if (in_array((int) $plataformaId, $plataformasPermitidas, true) && ! empty($datos['usuario']) && ! empty($datos['contraseña'])) {
                $alumno->alumnoPlataforma()->updateOrCreate(
                    ['plataforma_id' => $plataformaId],
                    ['usuario' => $datos['usuario'], 'contraseña' => $datos['contraseña']]
                );
            }
        }
    }

    /** Actualiza sólo accesos de plataforma; se usa desde Coordinación. */
    private function actualizarAccesosPlataformas(Request $request, Alumno $alumno): int
    {
        $plataformasPermitidas = Plataforma::query()
            ->where('activo', true)
            ->whereHas('niveles', fn ($query) => $query->whereKey($alumno->nivel_educativo_id))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $actualizadas = 0;

        foreach ($request->input('plataformas', []) as $plataformaId => $datos) {
            if (! in_array((int) $plataformaId, $plataformasPermitidas, true) || blank($datos['usuario'] ?? null) || blank($datos['contraseña'] ?? null)) {
                continue;
            }

            $registro = $alumno->alumnoPlataforma()->where('plataforma_id', $plataformaId)->first();
            if ($registro && (! hash_equals((string) $registro->usuario, (string) $datos['usuario']) || ! hash_equals((string) $registro->contraseña, (string) $datos['contraseña']))) {
                $registro->update(['usuario' => $datos['usuario'], 'contraseña' => $datos['contraseña']]);
                $actualizadas++;
            }
        }

        return $actualizadas;
    }

    private function actualizarFamiliares(Request $request, Alumno $alumno)
    {
        $familiaresEnviados = collect($request->input('familiares', []));

        foreach ($familiaresEnviados as $familiar) {
            // Verificar si el familiar tiene un id
            if (isset($familiar['id']) && $familiar['id'] > 0) {
                // Buscar si el familiar ya existe para el alumno
                $registroExistente = $alumno->familiares()->where('id', $familiar['id'])->first();
                if ($registroExistente) {
                    // Si existe, actualizarlo
                    $registroExistente->update([
                        'nombre' => $familiar['nombre'],
                        'apellido_paterno' => $familiar['apellido_paterno'],
                        'apellido_materno' => $familiar['apellido_materno'],
                        'fecha_nacimiento' => $familiar['fecha_nacimiento'],
                        'estado_civil' => $familiar['estado_civil'],
                        'domicilio' => $familiar['domicilio'],
                        'no_domicilio' => $familiar['no_domicilio'],
                        'cp' => $familiar['cp'],
                        'colonia' => $familiar['colonia'],
                        'ciudad' => $familiar['ciudad'],
                        'estado' => $familiar['estado'],
                        'telefono_fijo' => $familiar['telefono_fijo'],
                        'celular' => $familiar['celular'],
                        'correo' => $familiar['correo'],
                        'profesion' => $familiar['profesion'],
                        'ocupacion' => $familiar['ocupacion'],
                        'empresa_nombre' => $familiar['empresa_nombre'],
                        'empresa_telefono' => $familiar['empresa_telefono'],
                        'empresa_domicilio' => $familiar['empresa_domicilio'],
                        'empresa_ciudad' => $familiar['empresa_ciudad'],
                        'tipo_familiar' => $familiar['tipo_familiar'],
                    ]);
                }
            }
        }
    }

    private function actualizarHermanos(Request $request, Alumno $alumno)
    {
        $hermanosEnviados = collect($request->input('hermanos', []));
        $idsConservados = $hermanosEnviados->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();

        $alumno->hermanos()->whereNotIn('id', $idsConservados)->delete();

        foreach ($hermanosEnviados as $hermano) {
            if (isset($hermano['id']) && $hermano['id'] > 0) {
                // Actualizar hermano existente
                $registroExistente = $alumno->hermanos()->where('id', $hermano['id'])->first();
                if ($registroExistente) {
                    $registroExistente->update([
                        'nombre' => $hermano['nombre'] ?? null,
                        'apellido_paterno' => $hermano['apellido_paterno'] ?? null,
                        'apellido_materno' => $hermano['apellido_materno'] ?? null,
                        'edad' => $hermano['edad'] ?? null,
                    ]);
                }
            } else {
                // Crear nuevo hermano (no tiene ID)
                if (! empty($hermano['nombre']) || ! empty($hermano['apellido_paterno']) || ! empty($hermano['apellido_materno']) || ! empty($hermano['edad'])) {
                    $alumno->hermanos()->create([
                        'nombre' => $hermano['nombre'] ?? null,
                        'apellido_paterno' => $hermano['apellido_paterno'] ?? null,
                        'apellido_materno' => $hermano['apellido_materno'] ?? null,
                        'edad' => $hermano['edad'] ?? null,
                    ]);
                }
            }
        }
    }

    private function actualizarContactos(Request $request, Alumno $alumno)
    {
        // Recoger los datos de contactos del formulario
        $contactosEnviados = collect($request->input('contactos', []));

        foreach ($contactosEnviados as $contacto) {
            // Verificar si el contacto tiene un id (si es un contacto existente)
            if (isset($contacto['id']) && $contacto['id'] > 0) {
                // Buscar si el contacto ya existe para el alumno
                $registroExistente = $alumno->contactos()->where('id', $contacto['id'])->first();
                if ($registroExistente) {
                    // Si existe, actualizarlo
                    $registroExistente->update([
                        'nombre' => $contacto['nombre'],
                        'telefono' => $contacto['telefono'],
                        'correo' => $contacto['correo'],
                        'tipo_contacto' => $contacto['tipo_contacto'],
                    ]);
                }
            } else {
                // Si el contacto no tiene un id, significa que es un contacto nuevo
                $alumno->contactos()->create([
                    'nombre' => $contacto['nombre'],
                    'telefono' => $contacto['telefono'],
                    'correo' => $contacto['correo'],
                    'tipo_contacto' => $contacto['tipo_contacto'],
                ]);
            }
        }
    }

    public function indexBaja()
    {
        abort_unless(NivelAccess::hasFullAccess(auth()->user()), 403);
        $nivelIds = NivelAccess::allowedIds(auth()->user());
        $alumnos = Alumno::with('grado')
            ->whereIn('nivel_educativo_id', $nivelIds)
            ->where('status', 1)  // Solo los alumnos con status 1
            ->orderBy('created_at', 'desc') // Ordena por la fecha de creación en orden descendente
            ->get(); // Pagina con 10 registros por página

        $niveles = NivelAccess::allowedLevels(auth()->user());
        $grados = Grado::whereIn('nivel_educativo_id', $nivelIds)->get();

        return view('capturista.baja', compact('alumnos', 'niveles', 'grados'));
    }

    public function darBaja(Request $request)
    {
        abort_unless(NivelAccess::hasFullAccess(auth()->user()), 403);
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'motivo_baja' => 'required|string|max:2000',
        ]);

        $id = (int) $request->input('alumno_id');
        $motivo = trim($request->input('motivo_baja'));
        $alumno = Alumno::findOrFail($id);
        NivelAccess::ensureCanAccess(auth()->user(), $alumno->nivel_educativo_id);

        DB::transaction(function () use ($id, $motivo) {
            $alumno = Alumno::findOrFail($id);

            // Guardar en el historial de bajas
            DB::table('bajas_alumnos')->insert([
                'alumno_id' => $alumno->id,
                'matricula' => $alumno->matricula,
                'nombre_completo' => "{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}",
                'nivel_educativo_id' => $alumno->nivel_educativo_id,
                'grado_id' => $alumno->grado_id,
                'seccion' => $alumno->seccion,
                'motivo' => $motivo,
                'fecha_baja' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->registrarHistorial($alumno->id, "{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}", 'baja', $motivo);

            // Mover datos a la tabla de archivados
            DB::table('alumnos_archivados')->insert([
                'id' => $alumno->id,
                'matricula' => $alumno->matricula,
                'nombre' => $alumno->nombre,
                'apellidopaterno' => $alumno->apellidopaterno,
                'apellidomaterno' => $alumno->apellidomaterno,
                'nivel_educativo_id' => $alumno->nivel_educativo_id,
                'grado_id' => $alumno->grado_id,
                'seccion' => $alumno->seccion,
                'fecha_inscripcion' => $alumno->fecha_inscripcion,
                'status' => 0,
                'fecha_inicio' => $alumno->fecha_inicio,
                'lugar_nacimiento' => $alumno->lugar_nacimiento,
                'fecha_nacimiento' => $alumno->fecha_nacimiento,
                'edad_anios' => $alumno->edad_anios,
                'edad_meses' => $alumno->edad_meses,
                'sexo' => $alumno->sexo,
                'domicilio' => $alumno->domicilio,
                'cp' => $alumno->cp,
                'cerrada' => $alumno->cerrada,
                'colonia' => $alumno->colonia,
                'ciudad' => $alumno->ciudad,
                'estado' => $alumno->estado,
                'enfermedades_alergias' => $alumno->enfermedades_alergias,
                'pediatra_nombre' => $alumno->pediatra_nombre,
                'pediatra_telefono' => $alumno->pediatra_telefono,
                'no_domicilio' => $alumno->no_domicilio,
                'fecha_archivo' => now(),
                'created_at' => $alumno->created_at,
                'updated_at' => $alumno->updated_at,
            ]);

            // Mover relaciones a tablas de archivo

            // Insertar en la tabla alumno_plataforma_archivados
            DB::table('alumno_plataforma_archivados')->insert(
                json_decode(json_encode(DB::table('alumno_plataforma')->where('alumno_id', $id)->get([
                    'id',
                    'alumno_id',
                    'plataforma_id',
                    'usuario',
                    'contraseña',
                    'created_at',
                    'updated_at',
                ])), true)
            );

            // Insertar en la tabla contactos_archivados
            DB::table('contactos_archivados')->insert(
                json_decode(json_encode(DB::table('contactos')->where('alumno_id', $id)->get([
                    'id',
                    'nombre',
                    'telefono',
                    'tipo_contacto',
                    'correo',
                    'created_at',
                    'updated_at',
                    'alumno_id',
                ])), true)
            );

            // Insertar en la tabla familiares_archivados
            DB::table('familiares_archivados')->insert(
                json_decode(json_encode(DB::table('familiares')->where('alumno_id', $id)->get([
                    'id',
                    'alumno_id',
                    'nombre',
                    'apellido_paterno',
                    'apellido_materno',
                    'fecha_nacimiento',
                    'estado_civil',
                    'domicilio',
                    'no_domicilio',
                    'cp',
                    'colonia',
                    'ciudad',
                    'estado',
                    'telefono_fijo',
                    'celular',
                    'correo',
                    'profesion',
                    'ocupacion',
                    'empresa_nombre',
                    'empresa_telefono',
                    'empresa_domicilio',
                    'empresa_ciudad',
                    'tipo_familiar',
                    'created_at',
                    'updated_at',
                ])), true)
            );

            // Insertar en la tabla hermanos_archivados
            DB::table('hermanos_archivados')->insert(
                json_decode(json_encode(DB::table('hermanos')->where('alumno_id', $id)->get([
                    'id',
                    'alumno_id',
                    'nombre',
                    'apellido_paterno',
                    'apellido_materno',
                    'edad',
                    'created_at',
                    'updated_at',
                ])), true)
            );

            // Eliminar datos originales
            DB::table('alumno_plataforma')->where('alumno_id', $id)->delete();
            DB::table('contactos')->where('alumno_id', $id)->delete();
            DB::table('familiares')->where('alumno_id', $id)->delete();
            DB::table('hermanos')->where('alumno_id', $id)->delete();
            $alumno->delete();
        });

        $this->notificaciones->notificarAlumno($alumno, 'baja', $motivo);

        return redirect()->route('index.baja')->with('success', 'Alumno dado de baja correctamente.');
    }

    public function indexArchivados()
    {
        abort_unless(NivelAccess::hasFullAccess(auth()->user()), 403);
        $nivelIds = NivelAccess::allowedIds(auth()->user());
        // Obtener todos los alumnos archivados y cargar las relaciones 'nivelEducativo' y 'grado'
        $alumnosArchivados = AlumnoArchivado::with(['nivelEducativo', 'grado'])
            ->whereIn('nivel_educativo_id', $nivelIds)
            ->where('status', 0)  // Solo los alumnos con status 1
            ->where('tipo_archivo', 'baja')
            ->orderBy('created_at', 'desc')
            ->get(); // Paginación de 10 registros por página

        // Obtener todos los niveles educativos y grados
        $niveles = NivelAccess::allowedLevels(auth()->user());
        $grados = Grado::whereIn('nivel_educativo_id', $nivelIds)->get();
        $alumnoIds = $alumnosArchivados->pluck('id');
        $historialBajas = HistorialAlumno::with('usuario')
            ->whereIn('alumno_id', $alumnoIds)
            ->where('accion', 'baja')
            ->orderByDesc('fecha')
            ->get()
            ->unique('alumno_id')
            ->keyBy('alumno_id');
        $bajasAnteriores = DB::table('bajas_alumnos')
            ->whereIn('alumno_id', $alumnoIds)
            ->orderByDesc('fecha_baja')
            ->get()
            ->unique('alumno_id')
            ->keyBy('alumno_id');

        // Pasar las variables a la vista
        return view('capturista.archivados', compact('alumnosArchivados', 'niveles', 'grados', 'historialBajas', 'bajasAnteriores'));
    }

    public function indexEgresados()
    {
        abort_unless(NivelAccess::hasFullAccess(auth()->user()), 403);

        $egresados = AlumnoArchivado::with(['nivelEducativo', 'grado'])
            ->where('tipo_archivo', 'egreso')
            ->orderByDesc('fecha_egreso')
            ->get();

        return view('capturista.egresados', compact('egresados'));
    }

    public function reactivar(Request $request, $id)
    {
        abort_unless(NivelAccess::hasFullAccess(auth()->user()), 403);
        $request->validate(['motivo_reactivacion' => 'required|string|max:2000']);
        $motivo = trim($request->input('motivo_reactivacion'));
        $alumnoArchivado = DB::table('alumnos_archivados')->where('id', $id)->first();
        abort_unless($alumnoArchivado, 404);
        NivelAccess::ensureCanAccess(auth()->user(), $alumnoArchivado->nivel_educativo_id);

        DB::transaction(function () use ($id, $motivo) {
            // Obtener el alumno archivado
            $alumnoArchivado = DB::table('alumnos_archivados')->where('id', $id)->first();

            // Reactivar el alumno: moverlo a la tabla 'alumnos'
            DB::table('alumnos')->insert([
                'id' => $alumnoArchivado->id,
                'matricula' => $alumnoArchivado->matricula,
                'nombre' => $alumnoArchivado->nombre,
                'apellidopaterno' => $alumnoArchivado->apellidopaterno,
                'apellidomaterno' => $alumnoArchivado->apellidomaterno,
                'nivel_educativo_id' => $alumnoArchivado->nivel_educativo_id,
                'grado_id' => $alumnoArchivado->grado_id,
                'seccion' => $alumnoArchivado->seccion,
                'fecha_inscripcion' => $alumnoArchivado->fecha_inscripcion,
                'status' => 1, // Reactivar el alumno
                'fecha_inicio' => $alumnoArchivado->fecha_inicio,
                'lugar_nacimiento' => $alumnoArchivado->lugar_nacimiento,
                'fecha_nacimiento' => $alumnoArchivado->fecha_nacimiento,
                'edad_anios' => $alumnoArchivado->edad_anios,
                'edad_meses' => $alumnoArchivado->edad_meses,
                'sexo' => $alumnoArchivado->sexo,
                'domicilio' => $alumnoArchivado->domicilio,
                'cp' => $alumnoArchivado->cp,
                'cerrada' => $alumnoArchivado->cerrada,
                'colonia' => $alumnoArchivado->colonia,
                'ciudad' => $alumnoArchivado->ciudad,
                'estado' => $alumnoArchivado->estado,
                'enfermedades_alergias' => $alumnoArchivado->enfermedades_alergias,
                'pediatra_nombre' => $alumnoArchivado->pediatra_nombre,
                'pediatra_telefono' => $alumnoArchivado->pediatra_telefono,
                'no_domicilio' => $alumnoArchivado->no_domicilio,
                'created_at' => $alumnoArchivado->created_at,
                'updated_at' => $alumnoArchivado->updated_at,
            ]);

            DB::table('reactivaciones_alumnos')->insert([
                'alumno_id' => $alumnoArchivado->id,
                'motivo' => $motivo,
                'fecha_reactivacion' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->registrarHistorial($alumnoArchivado->id, "{$alumnoArchivado->nombre} {$alumnoArchivado->apellidopaterno} {$alumnoArchivado->apellidomaterno}", 'reactivacion', $motivo);

            // Mover los datos de las tablas relacionadas (plataforma, contactos, familiares, hermanos)

            // Recuperar y mover los datos de la tabla 'alumno_plataforma_archivados' a 'alumno_plataforma'
            $alumnoPlataformas = DB::table('alumno_plataforma_archivados')->where('alumno_id', $id)->get();
            foreach ($alumnoPlataformas as $plataforma) {
                DB::table('alumno_plataforma')->insert([
                    'alumno_id' => $plataforma->alumno_id,
                    'plataforma_id' => $plataforma->plataforma_id,
                    'usuario' => $plataforma->usuario,
                    'contraseña' => $plataforma->contraseña,
                    'created_at' => $plataforma->created_at,
                    'updated_at' => $plataforma->updated_at,
                ]);
            }

            // Mover los contactos archivados
            $contactos = DB::table('contactos_archivados')->where('alumno_id', $id)->get();
            foreach ($contactos as $contacto) {
                DB::table('contactos')->insert([
                    'alumno_id' => $contacto->alumno_id,
                    'nombre' => $contacto->nombre ?? 'Desconocido',
                    'telefono' => $contacto->telefono,
                    'tipo_contacto' => $contacto->tipo_contacto,
                    'correo' => $contacto->correo,
                    'created_at' => $contacto->created_at,
                    'updated_at' => $contacto->updated_at,
                ]);
            }

            // Mover los familiares archivados
            $familiares = DB::table('familiares_archivados')->where('alumno_id', $id)->get();
            foreach ($familiares as $familiar) {
                DB::table('familiares')->insert([
                    'alumno_id' => $familiar->alumno_id,
                    'nombre' => $familiar->nombre,
                    'apellido_paterno' => $familiar->apellido_paterno,
                    'apellido_materno' => $familiar->apellido_materno,
                    'fecha_nacimiento' => $familiar->fecha_nacimiento,
                    'estado_civil' => $familiar->estado_civil,
                    'domicilio' => $familiar->domicilio,
                    'no_domicilio' => $familiar->no_domicilio,
                    'cp' => $familiar->cp,
                    'colonia' => $familiar->colonia,
                    'ciudad' => $familiar->ciudad,
                    'estado' => $familiar->estado,
                    'telefono_fijo' => $familiar->telefono_fijo,
                    'celular' => $familiar->celular,
                    'correo' => $familiar->correo,
                    'profesion' => $familiar->profesion,
                    'ocupacion' => $familiar->ocupacion,
                    'empresa_nombre' => $familiar->empresa_nombre,
                    'empresa_telefono' => $familiar->empresa_telefono,
                    'empresa_domicilio' => $familiar->empresa_domicilio,
                    'empresa_ciudad' => $familiar->empresa_ciudad,
                    'tipo_familiar' => $familiar->tipo_familiar,
                    'created_at' => $familiar->created_at,
                    'updated_at' => $familiar->updated_at,
                ]);
            }

            // Mover los hermanos archivados
            $hermanos = DB::table('hermanos_archivados')->where('alumno_id', $id)->get();
            foreach ($hermanos as $hermano) {
                DB::table('hermanos')->insert([
                    'alumno_id' => $hermano->alumno_id,
                    'nombre' => $hermano->nombre,
                    'apellido_paterno' => $hermano->apellido_paterno,
                    'apellido_materno' => $hermano->apellido_materno,
                    'edad' => $hermano->edad,
                    'created_at' => $hermano->created_at,
                    'updated_at' => $hermano->updated_at,
                ]);
            }

            // Eliminar el alumno de la tabla 'alumnos_archivados'
            DB::table('alumnos_archivados')->where('id', $id)->delete();
        });

        $alumnoReactivado = Alumno::findOrFail($id);
        $this->notificaciones->notificarAlumno($alumnoReactivado, 'reactivacion', $motivo);

        return redirect()->route('alumnos.archivados')->with('success', 'Alumno reactivado correctamente.');
    }
}

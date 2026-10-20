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
use App\Models\Contacto;
use App\Models\Hermano;
use App\Mail\AlumnoRegistered;
use App\Mail\AlumnoUpdate;
use Illuminate\Support\Facades\Mail;
use App\Models\Familiar;
use Illuminate\Support\Facades\DB;
use App\Models\AlumnoArchivado;
use App\Mail\PruebaMail;



class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user(); // Obtener el usuario autenticado
        $query = Alumno::query()->with('nivelEducativo'); // Incluir la relación con NivelEducativo
    
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
            $nivelId = $request->input('nivel');
            $query->where('nivel_educativo_id', $nivelId);
        }
    
        // Obtener los alumnos paginados
        $alumnos = $query->orderBy('created_at', 'desc')->paginate(10);
    
        // Obtener todos los niveles educativos para los botones de filtro
        $niveles = NivelEducativo::all();
    
        // Redirigir según el rol
        if ($user->hasRole('SuperAdmin')) {
            return view('capturista.index', compact('alumnos', 'niveles'));
        }
    
        if ($user->hasRole('ControlEscolar')) {
            return view('capturista.index', compact('alumnos', 'niveles'));
        }
    
        // Si no tiene el rol adecuado, redirigir o abortar
        abort(403, 'No tienes permiso para acceder a esta página.');
    }

    public function search(Request $request)
{
    $raw     = $request->get('search', '');
    $nivel   = $request->get('nivel');

    // 1. Normaliza: quita espacios extra y separa por palabras
    $tokens = collect(preg_split('/\s+/', trim($raw)))
                ->filter();                   // elimina strings vacíos

    $alumnos = Alumno::with(['nivelEducativo', 'grado'])

        // 2. Filtro por nivel (si viene)
        ->when($nivel, fn ($q) => $q->where('nivel_educativo_id', $nivel))

        // 3. Filtro por cada token
        ->when($tokens->isNotEmpty(), function ($q) use ($tokens) {
            $q->where(function ($outer) use ($tokens) {
                foreach ($tokens as $word) {
                    $outer->where(function ($inner) use ($word) {
                        $like = "%{$word}%";
                        $inner->where('matricula',       'like', $like)
                              ->orWhere('nombre',        'like', $like)
                              ->orWhere('apellidopaterno','like', $like)
                              ->orWhere('apellidomaterno','like', $like);
                    });
                }
            });
        })

        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return response()->json($alumnos);
}

           
    public function selectSearch()
    {
        $niveles = NivelEducativo::all();
        return view('capturista.selectsearch', compact('niveles'));
        
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
        // Obtener el nivel educativo con su ID
        $nivel = NivelEducativo::findOrFail($nivel_id);

        $familiares = Familiar::all();
        $grados = Grado::where('nivel_educativo_id', $nivel->id)->get();
        
        // Pasar las variables a la vista, incluyendo el id del nivel educativo
        return view('capturista.create', compact('nivel', 'grados', 'nivel_id','familiares'));
    }
    
    public function store(Request $request)
    {
    // Validación de los datos del formulario
    $request->validate([
    'nombre' => 'required',
    'apellidopaterno' => 'required',
    'apellidomaterno' => 'required',
    'contacto1nombre' => 'required',
    'telefono1' => 'required',
    'contacto1tipo' => 'required',
    'correo1' => 'required|email',
    'contacto2nombre' => 'nullable',
    'telefono2' => 'nullable',
    'contacto2tipo' => 'nullable',
    'correo2' => 'nullable|email',
    'contacto3nombre' => 'nullable',
    'telefono3' => 'nullable',
    'contacto3tipo' => 'nullable',
    'correo3' => 'nullable|email',
    'nivel_educativo_id' => 'required|exists:nivel_educativo,id',
    'grado_id' => 'required|exists:grados,id',
    'fecha_inscripcion' => 'required|date',
    'fecha_inicio' => 'required|date',
    'lugar_nacimiento' => 'required',
    'fecha_nacimiento' => 'required|date',
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
    ]);

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

// Generar la nueva matrícula
$nuevaMatricula = $año . str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);


    // Incrementar en 1 y formatear con 4 dígitos
    $nuevoNumero = str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);

    // Construir la matrícula final (Ejemplo: "251724" para el siguiente alumno)
    $matricula = $año . $nuevoNumero;

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
        'no_domicilio'=> $request->no_domicilio,
        'ciudad' => $request->ciudad,
        'estado' => $request->estado,
        'enfermedades_alergias' => $request->enfermedades_alergias,
        'pediatra_nombre' => $request->pediatra_nombre,
        'pediatra_telefono' => $request->pediatra_telefono,
    ]);

    if (!$alumno) {
        return redirect()->back()->with('error', 'Error al crear el alumno.');
    }

    if ($request->has('familiares')) {
        // Definir los tipos requeridos
        $tiposRequeridos = ['Padre', 'Madre', 'Tutor'];

        foreach ($tiposRequeridos as $tipo) {
            // Obtener datos si existen, sino asignar array vacío
            $datos = $request->familiares[$tipo] ?? [];

            Familiar::create([
                'alumno_id' => $alumno->id,
                'tipo_familiar' => $tipo, // Aquí se usa el nombre correcto del campo en la base de datos
                'nombre' => $datos['nombre'] ?? 'No Especificado',
                'apellido_paterno'=> $datos['apellido_paterno']?? null,
                'apellido_materno'=> $datos['apellido_materno']?? null,
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

    // Recuperar los hermanos
    $hermanos = Hermano::where('alumno_id', $alumno->id)->get();

    $contactos = Contacto::where('alumno_id', $alumno->id)->get();

    // Lógica para agregar los destinatarios del correo
    $destinatarios = [
        'coordinador_tecnologia@colegiosanluis.com',
        'coordinadora_academica@colegiosanluis.com',
    ];

    // Agregar al coordinador del nivel educativo
    switch ($request->nivel_educativo_id) {
        case 1:
            $destinatarios[] = 'coordinador.preescolar@colegiosanluis.com';
            break;
        case 2:
            $destinatarios[] = 'coordinador.primaria@colegiosanluis.com';
            break;
        case 3:
            $destinatarios[] = 'coordinador.secundaria@colegiosanluis.com';
            break;
    }

    // Enviar el correo de notificación a los destinatarios
    Mail::to($destinatarios)->send(new AlumnoRegistered($alumno, $contactos,$hermanos));

    return redirect()->route('capturista.selectsearch')->with('success', 'Alumno registrado correctamente.');
    }

    
    protected function generarCredenciales($nombre, $apellido, $matricula, $fechaInscripcion)
    {
        // Obtener el primer nombre y apellido
        $primerNombre = strtolower(explode(' ', $nombre)[0]);
        $primerApellido = strtolower(explode(' ', $apellido)[0]);
    
        // Generar el correo con el dominio @colegiosanluis.com.mx
        $emailClassroom = $primerNombre . $primerApellido . '@colegiosanluis.com.mx';
    
        // Comprobamos si el correo ya existe en la tabla alumno_plataforma
        $contador = 1;
        while (AlumnoPlataforma::where('usuario', $emailClassroom)->exists()) {
            $emailClassroom = $primerNombre . $primerApellido . $contador . '@colegiosanluis.com.mx';
            $contador++;
        }
    
        // Obtener el año de inscripción
        $anioInscripcion = date('y', strtotime($fechaInscripcion));
    
        // Generar las contraseñas
        $contraseñaClassroom = 'Csl$' . $primerNombre . $anioInscripcion;
        $usuarioMoodle = 'Csl-' . $matricula;
        $contraseñaMoodle = $matricula;
    
        // Devolvemos las credenciales generadas
        return [
            'usuario_classroom' => $emailClassroom, // Solo guardamos el correo para usuario_classroom
            'contraseña_classroom' => $contraseñaClassroom,
            'usuario_moodle' => $usuarioMoodle,
            'contraseña_moodle' => $contraseñaMoodle,
        ];
    }
    
    // Función para guardar las credenciales en alumno_plataforma
    protected function guardarCredencialesPlataforma(Alumno $alumno, $credenciales)
    {
        // Asignar plataforma_id para Classroom (1) y Moodle (2)
        $classroomPlataformaId = 1;
        $moodlePlataformaId = 2;

        // Guardar en la tabla alumno_plataforma para Classroom
        AlumnoPlataforma::create([
            'alumno_id' => $alumno->id,
            'plataforma_id' => $classroomPlataformaId, // ID de la plataforma
            'usuario' => $credenciales['usuario_classroom'],
            'contraseña' => $credenciales['contraseña_classroom'],
        ]);

        // Guardar en la tabla alumno_plataforma para Moodle
        AlumnoPlataforma::create([
            'alumno_id' => $alumno->id,
            'plataforma_id' => $moodlePlataformaId, // ID de la plataforma
            'usuario' => $credenciales['usuario_moodle'],
            'contraseña' => $credenciales['contraseña_moodle'],
        ]);
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
    if (!empty($contactos)) {
        Contacto::insert($contactos);
    }
}


    private function relacionarHermanos(Request $request, Alumno $alumno)
    {

        $apellido_paterno = $alumno->apellidopaterno;
        $apellido_materno = $alumno->apellidomaterno;

        // Recorrer del 1 al 5 (según los campos del formulario)
        for ($i = 1; $i <= 5; $i++) {
            $nombre = $request->input("hermano{$i}nombre");
            $edad = $request->input("hermano{$i}edad");

            // Si no hay nombre, no lo guardamos
            if (!$nombre) {
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
        $alumno = Alumno::findOrFail($id);
        $contactos = Contacto::where('alumno_id', $alumno->id)->get();
        $grados = Grado::where('nivel_educativo_id', $alumno->nivel_educativo_id)->get();
        $niveles = NivelEducativo::all();  // Obtener todos los niveles educativos
        $hermanos = $alumno->hermanos; // Obtener los hermanos relacionados con el alumno
        $familiares = Familiar::where('alumno_id', $id)
    ->orderByRaw("CASE 
        WHEN tipo_familiar = 'Padre' THEN 1
        WHEN tipo_familiar = 'Madre' THEN 2
        WHEN tipo_familiar = 'Tutor' THEN 3
        ELSE 4 END")
    ->get();

        return view('capturista.edit', compact('alumno', 'contactos', 'grados', 'niveles', 'hermanos', 'familiares'));
    }
    
    
    public function getGrados($nivel_id)
    {
        // Obtener los grados correspondientes al nivel educativo seleccionado
        $grados = Grado::where('nivel_educativo_id', $nivel_id)->get();

        // Devolver los grados como JSON
        return response()->json($grados);
    }   

    public function update(Request $request, $id)
    {    
        // Obtener el alumno a actualizar
        $alumno = Alumno::find($id);
    
        if (!$alumno) {
            return redirect()->back()->with('error', 'Alumno no encontrado.');
        }
    
        // Validación de los campos
        $request->validate($this->getValidationRules($id));
    
        // Asignación de la fecha de inicio
        $fecha_inicio = $request->fecha_inicio ?? now()->toDateString();
    
        // Generar las credenciales
        $credenciales = $this->generarCredenciales(
            $request->nombre ?? $alumno->nombre,
            $request->apellidopaterno ?? $alumno->apellidopaterno,
            $request->matricula ?? $alumno->matricula,
            $request->fecha_inscripcion ?? $alumno->fecha_inscripcion
        );
    
        // Asignar los valores recibidos de la solicitud
        $alumno->update([
            'matricula' => $request->matricula ?? $alumno->matricula,
            'nombre' => $request->nombre ?? $alumno->nombre,
            'apellidopaterno' => $request->apellidopaterno ?? $alumno->apellidopaterno,
            'apellidomaterno' => $request->apellidomaterno ?? $alumno->apellidomaterno,
            'usuario_classroom' => $credenciales['usuario_classroom'],
            'contraseña_classroom' => $credenciales['contraseña_classroom'],
            'usuario_moodle' => $credenciales['usuario_moodle'],
            'contraseña_moodle' => $credenciales['contraseña_moodle'],
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
            'seccion' => $request->seccion ?? $alumno->seccion,
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
    
        // Actualizar plataformas
        $this->actualizarPlataformas($request, $alumno);
    
        // Actualizar familiares
        $this->actualizarFamiliares($request, $alumno);
    
        // Actualizar hermanos
        $this->actualizarHermanos($request, $alumno);
    
        // Actualizar contactos
        $this->actualizarContactos($request, $alumno);
    
        // Obtener los contactos del alumno
        $contactos = $alumno->contactos;
    
        // Enviar correo de notificación
        $this->enviarCorreoNotificacion($alumno, $contactos);
    
        // Redirigir según el rol del usuario
        if (auth()->user()->hasRole('SuperAdmin')) {
            return redirect()->route('alumnos.edit', ['alumno' => $alumno->id])->with('success', 'Alumno actualizado correctamente.');
        }
    
        return back()->with('success', 'Alumno actualizado correctamente.');
    }
    
    private function getValidationRules($id)
    {
        return [
            'matricula' => 'nullable|numeric|unique:alumnos,matricula,' . $id,
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
            'seccion' => 'nullable|in:A,B',
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
            'familiares.*.telefono_fijo' => 'nullable|digits:10',
            'familiares.*.celular' => 'nullable|digits:10',
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
        
    private function enviarCorreoNotificacion($alumno, $contactos)
    {
        $destinatarios = [
            'coordinador_tecnologia@colegiosanluis.com',
            'coordinadora_academica@colegiosanluis.com',
        ];
    
        switch ($alumno->nivel_educativo_id) {
            case 1:
                $destinatarios[] = 'coordinador.preescolar@colegiosanluis.com';
                break;
            case 2:
                $destinatarios[] = 'coordinador.primaria@colegiosanluis.com';
                break;
            case 3:
                $destinatarios[] = 'coordinador.secundaria@colegiosanluis.com';
                break;
        }
    
        Mail::to($destinatarios)->send(new AlumnoUpdate($alumno, $contactos));
    }
    
    
    private function actualizarPlataformas(Request $request, Alumno $alumno)
    {
        $plataformas = $request->input('plataformas', []);
    
        foreach ($plataformas as $plataformaId => $datos) {
            if (!empty($datos['usuario']) && !empty($datos['contraseña'])) {
                $alumno->alumnoPlataforma()->updateOrCreate(
                    ['plataforma_id' => $plataformaId],
                    ['usuario' => $datos['usuario'], 'contraseña' => $datos['contraseña']]
                );
            }
        }    
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
    
        foreach ($hermanosEnviados as $hermano) {
            if (isset($hermano['id']) && $hermano['id'] > 0) {
                // Actualizar hermano existente
                $registroExistente = $alumno->hermanos()->where('id', $hermano['id'])->first();
                if ($registroExistente) {
                    $registroExistente->update([
                        'nombre' => $hermano['nombre'],
                        'apellido_paterno' => $hermano['apellido_paterno'],
                        'apellido_materno' => $hermano['apellido_materno'],
                        'edad' => $hermano['edad'],
                    ]);
                }
            } else {
                // Crear nuevo hermano (no tiene ID)
                if (!empty($hermano['nombre']) || !empty($hermano['apellido_paterno']) || !empty($hermano['apellido_materno']) || !empty($hermano['edad'])) {
                    $alumno->hermanos()->create([
                        'nombre' => $hermano['nombre'],
                        'apellido_paterno' => $hermano['apellido_paterno'],
                        'apellido_materno' => $hermano['apellido_materno'],
                        'edad' => $hermano['edad'],
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
    $alumnos = Alumno::with('grado')
        ->where('status', 1)  // Solo los alumnos con status 1
        ->orderBy('created_at', 'desc') // Ordena por la fecha de creación en orden descendente
        ->get(); // Pagina con 10 registros por página

    $niveles = NivelEducativo::all();
    $grados = Grado::all();

    return view('capturista.baja', compact('alumnos', 'niveles', 'grados'));
}


    public function darBaja(Request $request)
    {
        $id = $request->input('alumno_id');
        $motivo = $request->input('motivo_baja');
        
        // Verificar si el motivo no está vacío
        if (empty($motivo)) {
            return redirect()->back()->with('error', 'El motivo de la baja es obligatorio.');
        }    
    
        DB::transaction(function () use ($id, $motivo) {
            $alumno = Alumno::findOrFail($id);
    
           // Guardar en el historial de bajas
            DB::table('bajas_alumnos')->insert([
                'alumno_id'          => $alumno->id,
                'matricula'          => $alumno->matricula,
                'nombre_completo'    => "{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}",
                'nivel_educativo_id' => $alumno->nivel_educativo_id,
                'grado_id'           => $alumno->grado_id,
                'seccion'            => $alumno->seccion,
                'motivo'             => $motivo,
                'fecha_baja'         => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

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
                    'updated_at'
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
                    'alumno_id'
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
                    'updated_at'
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
                    'updated_at'
                ])), true)
            );


            // Eliminar datos originales
            DB::table('alumno_plataforma')->where('alumno_id', $id)->delete();
            DB::table('contactos')->where('alumno_id', $id)->delete();
            DB::table('familiares')->where('alumno_id', $id)->delete();
            DB::table('hermanos')->where('alumno_id', $id)->delete();
            $alumno->delete();
        });

        return redirect()->route('index.baja')->with('success', 'Alumno dado de baja correctamente.');
    }
    
    public function indexArchivados()
{
    // Obtener todos los alumnos archivados y cargar las relaciones 'nivelEducativo' y 'grado'
    $alumnosArchivados = AlumnoArchivado::with(['nivelEducativo', 'grado'])
    ->where('status', 0)  // Solo los alumnos con status 1
    ->orderBy('created_at', 'desc')
    ->get(); // Paginación de 10 registros por página

    // Obtener todos los niveles educativos y grados
    $niveles = NivelEducativo::all();
    $grados = Grado::all();

    // Pasar las variables a la vista
    return view('capturista.archivados', compact('alumnosArchivados', 'niveles', 'grados'));
}

    
    public function reactivar($id)
    {
    DB::transaction(function () use ($id) {
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

        return redirect()->route('alumnos.archivados')->with('success', 'Alumno reactivado correctamente.');
    }

}
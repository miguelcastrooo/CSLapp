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
            return view('admin.index', compact('alumnos', 'niveles'));
        }
    
        if ($user->hasRole('ControlEscolar')) {
            return view('capturista.index', compact('alumnos', 'niveles'));
        }
    
        // Si no tiene el rol adecuado, redirigir o abortar
        abort(403, 'No tienes permiso para acceder a esta página.');
    }

    public function search(Request $request)
    {
        $query = $request->get('search');
        $nivel = $request->get('nivel');  // Obtener el nivel desde el query
        $orderBy = $request->get('orderBy', 'desc');  // Obtener el orden (por defecto es descendente)

        $alumnos = Alumno::with('nivelEducativo')  // Cargar relación
            ->when($nivel, function ($q) use ($nivel) {
                return $q->where('nivel_educativo_id', $nivel);  // Filtrar por nivel
            })
            ->when($query, function ($q) use ($query) {
                return $q->where('matricula', 'like', "%{$query}%")
                        ->orWhere('nombre', 'like', "%{$query}%")
                        ->orWhere('apellidopaterno', 'like', "%{$query}%")
                        ->orWhere('apellidomaterno', 'like', "%{$query}%");
            })
            ->orderBy('created_at', $orderBy)  // Ordenar por la fecha de creación
            ->get();

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

        // Obtener los últimos dos dígitos del año (por ejemplo, "25" para el año 2025)
        $año = now()->format('y'); // Esto devuelve "25" en el año 2025

        // Obtener el último alumno registrado
        $ultimoAlumno = Alumno::orderBy('id', 'desc')->first();  

        // Verificar si hay una matrícula registrada y obtener el último número de matrícula,
        // o utilizar 1723 si no hay registros previos.
        if ($ultimoAlumno) {
            // Extraer los últimos 4 dígitos del número de matrícula (suponiendo que el formato es "AA####")
            $ultimoNumero = (int) substr($ultimoAlumno->matricula, -4);
        } else {
            // Si no hay registros previos, comenzar con 1723
            $ultimoNumero = 1723;
        }

        // Asegurarse de que el número no sea menor a 1723
        $ultimoNumero = $ultimoNumero < 1723 ? 1723 : $ultimoNumero;

        // Incrementar en 1 y asegurarse de que tenga 4 dígitos
        $nuevoNumero = str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);

        // Crear la matrícula con el formato "AA + Número", por ejemplo "25" para 2025
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
                'tipo' => $tipo, // Padre, Madre, Tutor
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
                'edad' => $edad ?? null,
            ]);
        }
    }
    
    public function edit($id)
    {
        $alumno = Alumno::findOrFail($id);
        $contactos = Contacto::where('alumno_id', $alumno->id)->get();
        $grados = Grado::where('nivel_educativo_id', $alumno->nivel_educativo_id)->get();
        $nivel_id = NivelEducativo::all();  // O como se recupere el nivel
        $hermanos = $alumno->hermanos; // Obtener los hermanos relacionados con el alumno
        
        return view('capturista.edit', compact('alumno', 'contactos', 'grados', 'nivel_id', 'hermanos'));
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
        $validationRules = [
            'matricula' => 'nullable|numeric|unique:alumnos,matricula,' . $id,
            'nombre' => 'nullable|string|max:255',
            'apellidopaterno' => 'nullable|string|max:255',
            'apellidomaterno' => 'nullable|string|max:255',
            'contacto1nombre' => 'nullable|string|max:255',
            'telefono1' => 'nullable|digits:10',
            'contacto1tipo_contacto' => 'nullable|string|max:255',
            'correo1' => 'nullable|email',
            'contacto2nombre' => 'nullable|string|max:255',
            'telefono2' => 'nullable|digits:10',
            'contacto2tipo_contacto' => 'nullable|string|max:255',
            'correo2' => 'nullable|email',
            'contacto3nombre' => 'nullable|string|max:255',
            'telefono3' => 'nullable|digits:10',
            'contacto3tipo_contacto' => 'nullable|string|max:255',
            'correo3' => 'nullable|email',
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
            'seccion' => 'nullable|in:A,B', // Validación para la sección
        ];
    
        $request->validate($validationRules);
    
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
        $nivel_educativo_id = $request->nivel_educativo_id ?? $alumno->nivel_educativo_id;
        $grado_id = $request->grado_id ?? $alumno->grado_id;
        $seccion = $request->seccion ?? $alumno->seccion;
    
        // Actualización del alumno
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
            'nivel_educativo_id' => $nivel_educativo_id,
            'grado_id' => $grado_id,
            'fecha_inscripcion' => $request->fecha_inscripcion ?? $alumno->fecha_inscripcion,
            'fecha_inicio' => $fecha_inicio,
            'seccion' => $seccion,
        ]);
    
        // Actualizar o crear las credenciales en alumno_plataforma
        $this->actualizarPlataformas($request, $alumno);
    
         // Actualizar contactos si es necesario
        $this->actualizarContactos($request, $alumno);

        // Obtener los contactos del alumno, si tienes una relación definida
        $contactos = $alumno->contactos;  // Esto asume que tienes una relación "contactos" en el modelo Alumno
    
        // Enviar correo de notificación
        $destinatarios = [
            'coordinador_tecnologia@colegiosanluis.com',
            'coordinadora_academica@colegiosanluis.com',
        ];
    
        // Añadir el coordinador de nivel educativo
        switch ($nivel_educativo_id) {
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
    
        // Redirigir según el rol del usuario
        if (auth()->user()->hasRole('SuperAdmin')) {
            return redirect()->route('admin.selectadmin')->with('success', 'Alumno actualizado correctamente.');
        }
    
        return redirect()->route('capturista.selectsearch')->with('success', 'Alumno actualizado correctamente.');
    }
    
    public function actualizarPlataformas(Request $request, Alumno $alumno)
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
    
        return redirect()->back()->with('success', 'Datos actualizados correctamente');
    }
    
    private function actualizarContactos(Request $request, Alumno $alumno)
{
    // Aquí actualizamos o creamos los contactos
    for ($i = 1; $i <= 3; $i++) {
        $contactoNombre = 'contacto' . $i . 'nombre';
        $contactoTelefono = 'telefono' . $i;
        $contactoTipo = 'contacto' . $i . 'tipo_contacto';
        $contactoCorreo = 'correo' . $i;

        // Buscar si el contacto ya existe
        $contactoExistente = Contacto::where('alumno_id', $alumno->id)
            ->where('tipo_contacto', $request->$contactoTipo)
            ->first();

        // Si existe, actualizamos los campos
        if ($contactoExistente) {
            $contactoExistente->update([
                'nombre' => $request->$contactoNombre ?? $contactoExistente->nombre,
                'telefono' => $request->$contactoTelefono ?? $contactoExistente->telefono,
                'correo' => $request->$contactoCorreo ?? $contactoExistente->correo,
                'tipo_contacto' => $request->$contactoTipo ?? $contactoExistente->tipo_contacto,
            ]);
        } else {
            // Si no existe, creamos un nuevo contacto
            if ($request->$contactoNombre || $request->$contactoTelefono || $request->$contactoCorreo) {
                Contacto::create([
                    'alumno_id' => $alumno->id,
                    'nombre' => $request->$contactoNombre,
                    'telefono' => $request->$contactoTelefono,
                    'correo' => $request->$contactoCorreo,
                    'tipo_contacto' => $request->$contactoTipo,
                ]);
                }
            }
        }
    }    
}
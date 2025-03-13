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
use App\Mail\AlumnoRegistered;
use App\Mail\AlumnoUpdate;
use Illuminate\Support\Facades\Mail;
use App\Models\Parentesco;


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
    
        // Obtener los parentescos y grados relacionados
        $parentescos = Parentesco::all();
        $grados = Grado::where('nivel_educativo_id', $nivel->id)->get();
        
        // Pasar las variables a la vista, incluyendo el id del nivel educativo
        return view('capturista.create', compact('nivel', 'grados', 'parentescos', 'nivel_id'));
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
            'contacto1tipo' => 'required|string|max:255',
            'correo1' => 'required|email', // Validar el correo del primer contacto
            'contacto2nombre' => 'nullable|string|max:255',
            'telefono2' => 'nullable|digits:10',
            'contacto2tipo' => 'nullable|string|max:255',
            'correo2' => 'nullable|email', // Validar el correo del segundo contacto
            'contacto3nombre' => 'nullable|string|max:255',
            'telefono3' => 'nullable|digits:10',
            'contacto3tipo' => 'nullable|string|max:255',
            'correo3' => 'nullable|email', // Validar el correo del tercer contacto
            'nivel_educativo_id' => 'required|exists:nivel_educativo,id',
            'grado_id' => 'required|exists:grados,id',
            'fecha_inscripcion' => 'required|date',
            'fecha_inicio' => 'nullable|date',
        ]);
    
        // Asignación de fecha_inicio: si no se recibe, se asigna la fecha actual
        $fecha_inicio = $request->fecha_inicio ?? now()->toDateString(); // Usa la fecha actual si es nula
    
        // Generar credenciales solo para Classroom y Moodle
        $credenciales = $this->generarCredenciales(
            $request->nombre,
            $request->apellidopaterno,
            $request->matricula,
            $request->fecha_inscripcion
        );
    
        // Crear el alumno
        $alumno = Alumno::create([
            'matricula' => $request->matricula,
            'nombre' => $request->nombre,
            'apellidopaterno' => $request->apellidopaterno,
            'apellidomaterno' => $request->apellidomaterno,
            'nivel_educativo_id' => $request->nivel_educativo_id,
            'grado_id' => $request->grado_id,
            'fecha_inscripcion' => $request->fecha_inscripcion,
            'fecha_inicio' => $fecha_inicio,
        ]);
    
        if (!$alumno) {
            return redirect()->back()->with('error', 'Error al crear el alumno.');
        }
    
        // Relacionar los contactos
        $this->relacionarContactos($request, $alumno);
    
        // Guardar las credenciales en la tabla alumno_plataforma
        $this->guardarCredencialesPlataforma($alumno, $credenciales);
    
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
        Mail::to($destinatarios)->send(new AlumnoRegistered($alumno, $contactos));
    
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
    // Función para guardar los contactos
    public function relacionarContactos(Request $request, Alumno $alumno)
    {
        // Crear los datos de los contactos
        $contactos = [];
    
        // Primer contacto
        if ($request->contacto1nombre && $request->correo1) {
            $contactos[] = [
                'nombre' => $request->contacto1nombre,
                'telefono' => $request->telefono1,
                'correo' => $request->correo1, // Correo del primer contacto
                'tipo_contacto' => $request->contacto1tipo,
                'alumno_id' => $alumno->id,
            ];
        }
    
        // Segundo contacto
        if ($request->contacto2nombre && $request->correo2) {
            $contactos[] = [
                'nombre' => $request->contacto2nombre,
                'telefono' => $request->telefono2,
                'correo' => $request->correo2, // Correo del segundo contacto
                'tipo_contacto' => $request->contacto2tipo,
                'alumno_id' => $alumno->id,
            ];
        }
    
        // Tercer contacto
        if ($request->contacto3nombre && $request->correo3) {
            $contactos[] = [
                'nombre' => $request->contacto3nombre,
                'telefono' => $request->telefono3,
                'correo' => $request->correo3, // Correo del tercer contacto
                'tipo_contacto' => $request->contacto3tipo,
                'alumno_id' => $alumno->id,
            ];
        }
    
        // Guardar los contactos en la base de datos
        if (count($contactos) > 0) {
            Contacto::insert($contactos);
        }
    }
    
    public function edit($id)
    {
        $alumno = Alumno::findOrFail($id);
        $contactos = Contacto::where('alumno_id', $alumno->id)->get();
        $grados = Grado::where('nivel_educativo_id', $alumno->nivel_educativo_id)->get();
        $nivel_id = NivelEducativo::all();  // O como se recupere el nivel
    
        return view('capturista.edit', compact('alumno', 'contactos', 'grados', 'nivel_id'));
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
        $contactos = Contacto::where('alumno_id', $alumno->id)->get();
        foreach ($contactos as $contacto) {
            $contactoData = [
                'nombre' => $request->{'contacto' . $contacto->tipo_contacto . 'nombre'} ?? $contacto->nombre,
                'telefono' => $request->{'telefono' . $contacto->tipo_contacto} ?? $contacto->telefono,
                'tipo_contacto' => $request->{'contacto' . $contacto->tipo_contacto . 'tipo_contacto'} ?? $contacto->tipo_contacto,
                'correo' => $request->{'correo' . $contacto->tipo_contacto} ?? $contacto->correo,
            ];
    
            if ($contacto->nombre !== $contactoData['nombre'] || 
                $contacto->telefono !== $contactoData['telefono'] ||
                $contacto->tipo_contacto !== $contactoData['tipo_contacto'] ||
                $contacto->correo !== $contactoData['correo']) {
                $contacto->update($contactoData);
            }
        }
    
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
    
    private function eliminarContactosDuplicados(Alumno $alumno)
    {
        // Obtener todos los contactos del alumno
        $contactos = Contacto::where('alumno_id', $alumno->id)->get();
    
        // Comprobar si hay contactos duplicados por el mismo nombre, teléfono o correo
        $contactosUnicos = $contactos->unique(function ($contacto) {
            return $contacto->nombre . $contacto->telefono . $contacto->correo;
        });
    
        // Eliminar los contactos duplicados
        $duplicados = $contactos->diff($contactosUnicos);
        foreach ($duplicados as $duplicado) {
            $duplicado->delete();
        }
    }
    
    private function actualizarContactos(Request $request, Alumno $alumno)
    {
        // Aquí solo actualizamos los contactos que hayan cambiado (si es que hay cambios)
        $contactos = ['contacto1', 'contacto2', 'contacto3'];
    
        foreach ($contactos as $contacto) {
            $contactoNombre = $contacto . 'nombre';
            $contactoTelefono = $contacto . 'telefono';
            $contactoTipo = $contacto . 'tipo';
            $contactoCorreo = $contacto . 'correo';
    
            $contactoExistente = Contacto::where('alumno_id', $alumno->id)
                ->where('tipo', $request->$contactoTipo)
                ->first();
    
            // Si existe, solo actualizamos los campos modificados
            if ($contactoExistente) {
                $contactoExistente->update([
                    'nombre' => $request->$contactoNombre ?? $contactoExistente->nombre,
                    'telefono' => $request->$contactoTelefono ?? $contactoExistente->telefono,
                    'correo' => $request->$contactoCorreo ?? $contactoExistente->correo,
                ]);
            } else {
                // Si no existe, solo actualizamos los datos de los contactos que sí se envían
                if ($request->$contactoNombre || $request->$contactoTelefono || $request->$contactoCorreo) {
                    Contacto::create([
                        'alumno_id' => $alumno->id,
                        'nombre' => $request->$contactoNombre,
                        'telefono' => $request->$contactoTelefono,
                        'correo' => $request->$contactoCorreo,
                        'tipo' => $request->$contactoTipo,
                    ]);
                }
            }
        }
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
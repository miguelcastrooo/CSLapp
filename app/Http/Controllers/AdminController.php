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
use Barryvdh\DomPDF\Facade\Pdf;


class AdminController extends Controller
{
        // Función para mostrar los alumnos para la vista de admin (capturista)
    public function index()
    {
        $alumnos = Alumno::paginate(10);
        return view('admin.index', compact('alumnos'));
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
        // Buscar el alumno con su nivel educativo y plataformas
        $alumno = Alumno::with(['nivelEducativo', 'grado', 'plataformas'])->findOrFail($id);
        
        // Validar que el nivel proporcionado coincide con el nivel del alumno
        if ($alumno->nivelEducativo->nombre !== $nivel) {
            abort(404, 'El nivel educativo del alumno no coincide con el nivel proporcionado.');
        }
    
        // Obtener el nivel educativo del alumno
        $nivelEducativo = $alumno->nivelEducativo->nombre;
        
        // Filtrar las plataformas según el nivel educativo
        $plataformas = $this->obtenerPlataformasPorNivel($nivelEducativo);
        
        // Generar el PDF con la vista 'pdf.alumno'
        $pdf = Pdf::loadView('admin.pdf', compact('alumno', 'plataformas'));
        
        // Descargar el PDF
        return $pdf->stream("credenciales_alumno_{$alumno->matricula}.pdf");
    }
    
    // Función privada para obtener las plataformas según el nivel educativo
    private function obtenerPlataformasPorNivel($nivelEducativo)
    {
        // Normalizar el valor del nivel educativo a una forma consistente (por ejemplo, minúsculas)
        $nivelEducativo = ucfirst(strtolower($nivelEducativo)); // Capitaliza la primera letra y asegura que el resto esté en minúsculas
        
        // Define las plataformas por nivel educativo
        $plataformasPorNivel = [
            'Preescolar' => ['Classroom', 'Moodle'],
            'Primaria Baja' => ['Classroom', 'Moodle', 'HMH'],
            'Primaria Alta' => ['Classroom', 'Moodle', 'HMH', 'Mathletics', 'Progrentis'],
            'Secundaria' => ['Classroom', 'Moodle', 'Mathletics'],
        ];
        
        // Obtener las plataformas asociadas al nivel educativo
        if (isset($plataformasPorNivel[$nivelEducativo])) {
            return $plataformasPorNivel[$nivelEducativo];
        }
        
        // Si no hay un nivel educativo conocido, devolver un arreglo vacío
        return [];
    }

        // Método para listar todos los niveles educativos
    // Listar todos los niveles educativos
    public function nivelesIndex()
    {
        $niveles = NivelEducativo::all();
        return view('admin.niveles', compact('niveles'));
    }

    // Mostrar los alumnos de un nivel educativo específico (usando ID en lugar de nombre)
    public function nivelesShow($id)
    {
        // Obtener el nivel por ID
        $nivelEducativo = NivelEducativo::findOrFail($id);
    
        // Obtener los alumnos de ese nivel
        $alumnos = Alumno::where('nivel_educativo_id', $nivelEducativo->id)->paginate(10);
    
        // Pasar los datos a la vista
        return view('admin.niveles', compact('nivelEducativo', 'alumnos'));
    }
    


}

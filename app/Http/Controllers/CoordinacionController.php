<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grado;
use App\Models\Alumno;

class CoordinacionController extends Controller
{
    public function selectGrado()
    {
        $user = auth()->user(); // Obtener usuario autenticado
    
        // Definir los IDs de nivel educativo para cada rol
        $niveles = [];
    
        if ($user->hasRole('CoordinacionPreescolar')) {
            $niveles = [1]; // ID del nivel "Preescolar"
        } elseif ($user->hasRole('CoordinacionPrimaria')) {
            $niveles = [2, 3]; // ID del nivel "Primaria" (Baja y Alta)
        } elseif ($user->hasRole('CoordinacionSecundaria')) {
            $niveles = [4]; // ID del nivel "Secundaria"
        } else {
            // Si es SuperAdmin u otro rol, mostrar todos los grados
            $grados = Grado::all();
            return view('coordinacion.grados.select', compact('grados'));
        }
    
        // Filtrar grados por nivel educativo
        $grados = Grado::whereIn('nivel_educativo_id', $niveles)->get();
    
        // Pasar los grados filtrados a la vista
        return view('coordinacion.grados.select', compact('grados'));
    }
    

    public function showGradoAlumnos($gradoId)
{
    $user = auth()->user(); // Obtener usuario autenticado

    // Definir los IDs de nivel educativo según el rol
    $niveles = [];

    if ($user->hasRole('CoordinacionPreescolar')) {
        $niveles = [1]; // ID del nivel "Preescolar"
    } elseif ($user->hasRole('CoordinacionPrimaria')) {
        $niveles = [2, 3]; // ID del nivel "Primaria" (Baja y Alta)
    } elseif ($user->hasRole('CoordinacionSecundaria')) {
        $niveles = [4]; // ID del nivel "Secundaria"
    }

    // Obtener el grado seleccionado
    $grado = Grado::findOrFail($gradoId);

    // Verificar si el grado seleccionado pertenece a un nivel que el usuario puede ver
    if (!in_array($grado->nivel_educativo_id, $niveles)) {
        abort(403, 'No tienes permisos para ver este grado.');
    }

    // Obtener los alumnos asociados al grado
    $alumnos = Alumno::where('grado_id', $gradoId)->paginate(10);

    return view('coordinacion.grados.alumnos', compact('grado', 'alumnos'));
}

public function show($id)
{
    // Obtener el alumno por ID
    $alumno = Alumno::findOrFail($id);

    // Pasar el alumno a la vista
    return view('admin.show', compact('alumno'));
}
public function showAlumno($id)
{
    // Buscar al alumno por su ID
    $alumnos = Alumno::findOrFail($id); // Esto lanza un 404 si no encuentra el alumno

    // Obtener el grado del alumno (si se necesita)
    $grado = $alumno->grado;

    // Pasar los datos a la vista
    return view('coordinacion.show', compact('alumno', 'grado'));
}


public function gradosShow($gradoId)
{
    $grado = Grado::findOrFail($gradoId);
    return view('coordinacion.grados.show', compact('grado'));
}

public function mostrarAlumnos($nivelId)
{
    // Obtener el nivel educativo
    $nivelEducativo = NivelEducativo::findOrFail($nivelId);

    // Obtener los alumnos que pertenecen a ese nivel
    $alumnos = Alumno::where('nivel_id', $nivelId)
                     ->with('alumnoPlataforma') // Asegurar que se carguen las plataformas
                     ->paginate(10);

    // Pasar los datos a la vista
    return view('coordinacion.grados.alumnos', compact('nivelEducativo', 'alumnos'));
}


}

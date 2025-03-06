<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Grado;
use App\Models\NivelEducativo;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Permission\Models\Role;

class CoordinacionController extends Controller
{
    // Constructor para asegurar que solo los coordinadores con el rol correspondiente puedan acceder
    public function __construct()
    {
    }

    public function index()
{
    // Obtener el rol del usuario logueado
    $role = auth()->user()->roles->first(); // Asumiendo que un usuario tiene un solo rol
    dd($role); // Verifica el rol que obtienes

    
    // Verificar si el rol existe
    if (!$role) {
        return view('coordinacion.index', ['mensaje' => 'No se encontró el rol del usuario']);
    }

    // Buscar el nivel educativo basado en el nombre del rol (sin importar mayúsculas/minúsculas)
    $nivelEducativo = NivelEducativo::where('nombre', $role->name)->first();
    dd($nivelEducativo); // Verifica el nivel educativo que obtienes

    // Verificar si el nivel educativo existe
    if (!$nivelEducativo) {
        return view('coordinacion.index', ['mensaje' => 'No hay nivel educativo asignado para este rol']);
    }

    // Obtener los alumnos asociados a ese nivel educativo
    $alumnos = Alumno::where('nivel_educativo_id', $nivelEducativo->id)->paginate(10);
    
    // Pasar los datos a la vista
    return view('coordinacion.index', compact('nivelEducativo', 'alumnos'));
}

        
    public function show($id)
    {
        // Buscar al alumno por su ID
        $alumno = Alumno::findOrFail($id);

        // Mostrar la vista con los detalles del alumno
        return view('coordinacion.show', compact('alumno'));
    }    
    
    // Editar la información de un alumno
    public function edit(Alumno $alumno)
    {
        return view('coordinador.edit', compact('alumno'));
    }

    // Actualizar la información de un alumno
    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario_classroom' => 'nullable|string',
            'contraseña_classroom' => 'nullable|string',
        ]);

        $alumno->update($request->all());

        return redirect()->route('coordinador.index')->with('success', 'Alumno actualizado correctamente');
    }

    // Generar PDF del alumno
    public function generatePdf(Alumno $alumno)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('coordinador.pdf', compact('alumno'));
        return $pdf->download('alumno-' . $alumno->id . '.pdf');
    }
}
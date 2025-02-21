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
    // Mostrar la información del alumno con las plataformas permitidas según el rol
    public function showAlumnoInfo($id)
    {
        $alumno = Alumno::find($id);
        $roles = auth()->user()->roles; // Obtener los roles del usuario logueado

        // Determinar qué plataformas se deben mostrar según el rol
        $plataformasPermitidas = $this->obtenerPlataformasSegunRol();

        return view('alumno.show', compact('alumno', 'roles', 'plataformasPermitidas'));
    }

    // Obtener las plataformas permitidas según el rol del usuario
    public function obtenerPlataformasSegunRol()
    {
        $role = auth()->user()->roles->first()->name; // Obtener el primer rol del usuario (asumiendo que solo tiene un rol)

        switch ($role) {
            case 'AdministracionPreescolar':
                return ['classroom', 'moodle'];
            case 'AdministracionPrimariaBaja':
                return ['classroom', 'moodle', 'hmh'];
            case 'AdministracionPrimariaAlta':
                return ['classroom', 'moodle', 'hmh', 'mathletics', 'progrentis'];
            case 'AdministracionSecundaria':
                return ['classroom', 'moodle', 'mathletics'];
            default:
                return [];
        }
    }

    // Método para ver los grados disponibles
    public function index()
    {
        $grados = Grado::all(); // Obtiene todos los grados

        return view('coordinacion.index', compact('grados'));
    }

    // Mostrar el formulario de edición para un grado
    public function edit($id)
    {
        $grado = Grado::findOrFail($id);

        return view('coordinacion.edit', compact('grado'));
    }

    // Actualizar un grado
    public function update(Request $request, $id)
    {
        // Validación de los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'nivel' => 'required|string',
        ]);

        $grado = Grado::findOrFail($id);
        $grado->update([
            'nombre' => $request->nombre,
            'nivel' => $request->nivel,
        ]);

        return redirect()->route('coordinacion.index')->with('success', 'Grado actualizado correctamente');
    }
}

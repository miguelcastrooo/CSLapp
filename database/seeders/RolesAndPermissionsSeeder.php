<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Crear roles si no existen
        $controlEscolar = Role::firstOrCreate(['name' => 'ControlEscolar']);
        $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin']);

        // Crear permisos
        $verAlumnos = Permission::firstOrCreate(['name' => 'ver_alumnos']);
        $registrarAlumnos = Permission::firstOrCreate(['name' => 'registrar_alumnos']);
        $editarAlumnos = Permission::firstOrCreate(['name' => 'editar_alumnos']);

        // Asignar permisos al rol ControlEscolar
        $controlEscolar->givePermissionTo([$verAlumnos, $registrarAlumnos]);

        // Asignar permisos al rol SuperAdmin (puede tener todos los permisos)
        $superAdmin->givePermissionTo(Permission::all());
    }
}

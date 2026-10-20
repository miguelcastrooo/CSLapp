<?php

// database/seeders/PermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        Permission::create(['name' => 'ver-alumnos']);
        Permission::create(['name' => 'editar-alumnos']);
        Permission::create(['name' => 'eliminar-alumnos']);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear roles
        Role::create(['name' => 'ControlEscolar']);
        Role::create(['name' => 'SuperAdmin']);
        Role::create(['name' => 'AdministracionPreescolar']);
        Role::create(['name' => 'AdministracionPrimariaBaja']);
        Role::create(['name' => 'AdministracionPrimariaAlta']);
        Role::create(['name' => 'AdministracionSecundaria']);
    }
}

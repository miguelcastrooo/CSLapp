<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Asignar el rol "ControlEscolar" al usuario con ID 6
        $userControl = User::find(6);
        if ($userControl) {
            $userControl->assignRole('ControlEscolar');
        }

        // Asignar el rol "SuperAdmin" al usuario con ID 9
        $userSuperAdmin = User::find(9);
        if ($userSuperAdmin) {
            $userSuperAdmin->assignRole('SuperAdmin');
        }
    }
}

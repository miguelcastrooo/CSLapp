<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignRolesToUsersSeeder extends Seeder
{
    /**
     * Ejecuta la semilla para asignar roles a los usuarios.
     *
     * @return void
     */
    public function run()
    {
        // Asignar el rol CoordinacionPreescolar al usuario con ID 12
        $userPreescolar = User::find(12);
        if ($userPreescolar) {
            $userPreescolar->assignRole('CoordinacionPreescolar');
            $this->command->info('Rol CoordinacionPreescolar asignado al usuario con ID 12.');
        }

        // Asignar el rol CoordinacionPrimaria al usuario con ID 13 (Coordinacion Primaria Baja)
        $userPrimariaBaja = User::find(13);
        if ($userPrimariaBaja) {
            $userPrimariaBaja->assignRole('CoordinacionPrimaria');
            $this->command->info('Rol CoordinacionPrimaria asignado al usuario con ID 13 (Primaria Baja).');
        }

        // Asignar el rol CoordinacionPrimaria al usuario con ID 14 (Coordinacion Primaria Alta)
        $userPrimariaAlta = User::find(14);
        if ($userPrimariaAlta) {
            $userPrimariaAlta->assignRole('CoordinacionPrimaria');
            $this->command->info('Rol CoordinacionPrimaria asignado al usuario con ID 14 (Primaria Alta).');
        }

        // Asignar el rol CoordinacionSecundaria al usuario con ID 15
        $userSecundaria = User::find(15);
        if ($userSecundaria) {
            $userSecundaria->assignRole('CoordinacionSecundaria');
            $this->command->info('Rol CoordinacionSecundaria asignado al usuario con ID 15.');
        }

        // Asignar el rol CoordinacionPrimaria al usuario con ID 16 (Coordinacion Primaria)
        $userPrimaria = User::find(16);
        if ($userPrimaria) {
            $userPrimaria->assignRole('CoordinacionPrimaria');
            $this->command->info('Rol CoordinacionPrimaria asignado al usuario con ID 16.');
        }
    }
}

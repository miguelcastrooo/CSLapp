<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        // Relacionar los roles con los niveles educativos en la tabla nivel_educativo_role

        // Relación entre el rol 29 (Coordinación Preescolar) y nivel 1 (Preescolar)
        DB::table('nivel_educativo_role')->insert([
            'role_id' => 29,
            'nivel_educativo_id' => 1,  // Preescolar
        ]);

        // Relación entre el rol 30 (Coordinación Primaria) y niveles 2 (Primaria Baja) y 3 (Primaria Alta)
        DB::table('nivel_educativo_role')->insert([
            'role_id' => 30,
            'nivel_educativo_id' => 2,  // Primaria Baja
        ]);
        DB::table('nivel_educativo_role')->insert([
            'role_id' => 30,
            'nivel_educativo_id' => 3,  // Primaria Alta
        ]);

        // Relación entre el rol 31 (Coordinación Secundaria) y nivel 4 (Secundaria)
        DB::table('nivel_educativo_role')->insert([
            'role_id' => 31,
            'nivel_educativo_id' => 4,  // Secundaria
        ]);
    }
}

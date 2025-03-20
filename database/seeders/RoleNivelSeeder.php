<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\NivelEducativo;

class RoleNivelSeeder extends Seeder
{
    public function run()
    {
        // Buscar los niveles en la base de datos
        $preescolar = NivelEducativo::where('nombre', 'Preescolar')->first();
        $primariaBaja = NivelEducativo::where('nombre', 'Primaria Baja')->first();
        $primariaAlta = NivelEducativo::where('nombre', 'Primaria Alta')->first();
        $secundaria = NivelEducativo::where('nombre', 'Secundaria')->first();

        // Verificar que existan los niveles antes de asignar
        if (!$preescolar || !$primariaBaja || !$primariaAlta || !$secundaria) {
            dd("Faltan niveles educativos en la base de datos.");
        }

        // Crear roles
        $coordinacionPreescolar = Role::updateOrCreate(['name' => 'CoordinacionPreescolar']);
        $coordinacionPrimaria = Role::updateOrCreate(['name' => 'CoordinacionPrimaria']);
        $coordinacionSecundaria = Role::updateOrCreate(['name' => 'CoordinacionSecundaria']);

        // Asignar niveles
        $coordinacionPreescolar->nivelesEducativos()->sync([$preescolar->id]);
        $coordinacionPrimaria->nivelesEducativos()->sync([$primariaBaja->id, $primariaAlta->id]);
        $coordinacionSecundaria->nivelesEducativos()->sync([$secundaria->id]);

        echo "Roles con niveles educativos asignados correctamente.\n";
    }
}

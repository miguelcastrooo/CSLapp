<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Grado;
use App\Models\NivelEducativo;

class GradoSeeder extends Seeder
{
    public function run()
    {
        // Relación con Preescolar
        $preescolar = NivelEducativo::where('nombre', 'Preescolar')->first();
        Grado::create(['nombre' => 'BabiesRoom', 'nivel_educativo_id' => $preescolar->id]);
        Grado::create(['nombre' => '1° Kinder', 'nivel_educativo_id' => $preescolar->id]);
        Grado::create(['nombre' => '2° Kinder', 'nivel_educativo_id' => $preescolar->id]);
        Grado::create(['nombre' => '3° Kinder', 'nivel_educativo_id' => $preescolar->id]);

        // Relación con Primaria Baja
        $primariaBaja = NivelEducativo::where('nombre', 'Primaria Baja')->first();
        Grado::create(['nombre' => '1°', 'nivel_educativo_id' => $primariaBaja->id]);
        Grado::create(['nombre' => '2°', 'nivel_educativo_id' => $primariaBaja->id]);
        Grado::create(['nombre' => '3°', 'nivel_educativo_id' => $primariaBaja->id]);

        // Relación con Primaria Alta
        $primariaAlta = NivelEducativo::where('nombre', 'Primaria Alta')->first();
        Grado::create(['nombre' => '4°', 'nivel_educativo_id' => $primariaAlta->id]);
        Grado::create(['nombre' => '5°', 'nivel_educativo_id' => $primariaAlta->id]);
        Grado::create(['nombre' => '6°', 'nivel_educativo_id' => $primariaAlta->id]);

        // Relación con Secundaria
        $secundaria = NivelEducativo::where('nombre', 'Secundaria')->first();
        Grado::create(['nombre' => '1° Secundaria', 'nivel_educativo_id' => $secundaria->id]);
        Grado::create(['nombre' => '2° Secundaria', 'nivel_educativo_id' => $secundaria->id]);
        Grado::create(['nombre' => '3° Secundaria', 'nivel_educativo_id' => $secundaria->id]);
    }
}

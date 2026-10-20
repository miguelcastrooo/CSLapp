<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\NivelPlataforma;
use App\Models\Plataforma;
use App\Models\NivelEducativo;

class NivelPlataformaSeeder extends Seeder
{
    public function run()
    {
        // Relacionar con Preescolar
        $preescolar = NivelEducativo::where('nombre', 'Preescolar')->first();
        $plataforma = Plataforma::where('nombre', 'Classroom')->first();
        NivelPlataforma::create(['nivel_educativo_id' => $preescolar->id, 'plataforma_id' => $plataforma->id]);

        // Relacionar con Primaria Baja
        $primariaBaja = NivelEducativo::where('nombre', 'Primaria Baja')->first();
        $plataforma = Plataforma::where('nombre', 'Moodle')->first();
        NivelPlataforma::create(['nivel_educativo_id' => $primariaBaja->id, 'plataforma_id' => $plataforma->id]);

        // Relacionar con Primaria Alta
        $primariaAlta = NivelEducativo::where('nombre', 'Primaria Alta')->first();
        $plataforma = Plataforma::where('nombre', 'Classroom')->first();
        NivelPlataforma::create(['nivel_educativo_id' => $primariaAlta->id, 'plataforma_id' => $plataforma->id]);

        // Relacionar con Secundaria
        $secundaria = NivelEducativo::where('nombre', 'Secundaria')->first();
        $plataforma = Plataforma::where('nombre', 'Moodle')->first();
        NivelPlataforma::create(['nivel_educativo_id' => $secundaria->id, 'plataforma_id' => $plataforma->id]);

        // Crear 10 registros adicionales de NivelPlataforma
        for ($i = 0; $i < 10; $i++) {
            NivelPlataforma::create([
                'nivel_educativo_id' => rand(1, 4), // Aleatorio para nivel educativo
                'plataforma_id' => rand(1, 2),      // Aleatorio para plataforma
            ]);
        }
    }
}

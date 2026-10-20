<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NivelEducativo;

class NivelEducativoSeeder extends Seeder
{
    public function run()
    {
        NivelEducativo::create(['nombre' => 'Preescolar']);
        NivelEducativo::create(['nombre' => 'Primaria Baja']);
        NivelEducativo::create(['nombre' => 'Primaria Alta']);
        NivelEducativo::create(['nombre' => 'Secundaria']);
    }
}

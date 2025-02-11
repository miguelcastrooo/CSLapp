<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plataforma;

class PlataformaSeeder extends Seeder
{
    public function run()
    {
        Plataforma::create(['nombre' => 'ClassRoom']);
        Plataforma::create(['nombre' => 'Moodle']);
        Plataforma::create(['nombre' => 'hmh']);
        Plataforma::create(['nombre' => 'Mathletics']);
        Plataforma::create(['nombre' => 'Progrentis']);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hermano;

class HermanoSeeder extends Seeder
{
    public function run(): void
    {
        Hermano::factory()->count(50)->create(); // Crea 500 alumnos
    }
}

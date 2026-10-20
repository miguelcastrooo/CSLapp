<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Familiar;

class FamiliarSeeder extends Seeder
{
    public function run(): void
    {
        Familiar::factory()->count(50)->create(); // Crea 500 alumnos
    }
}

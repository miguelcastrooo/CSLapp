<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AlumnoPlataforma;
use App\Models\Alumno;
use App\Models\Grado;
use App\Models\NivelEducativo;
use App\Models\NivelPlataforma;
use App\Models\Plataforma;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test' . now()->timestamp . '@example.com',
        ]);
        

        // Llamar a los seeders
        $this->call([
            AlumnoPlataformaSeeder::class,
            AlumnosSeeder::class,
            GradoSeeder::class,
            NivelEducativoSeeder::class,
            NivelPlataformaSeeder::class,
            PlataformaSeeder::class,
        ]);
    }
}

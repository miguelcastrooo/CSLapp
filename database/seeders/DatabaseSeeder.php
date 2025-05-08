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
use App\Models\Contacto;
use App\Models\Familiar;
use App\Models\Hermano;


class DatabaseSeeder extends Seeder
{
    public function run(): void
{
    $this->call([
        AlumnoSeeder::class,
        ContactoSeeder::class,
        HermanoSeeder::class,
        FamiliarSeeder::class,
    ]);
}

}

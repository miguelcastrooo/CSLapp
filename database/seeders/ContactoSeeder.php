<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contacto;

class ContactoSeeder extends Seeder
{
    public function run(): void
    {
        Contacto::factory()->count(50)->create(); // Crea 500 alumnos
    }
}

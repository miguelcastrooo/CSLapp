<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Alumno;
use App\Models\Plataforma;
use Illuminate\Support\Facades\DB;

class AlumnoPlataformaSeeder extends Seeder
{
    public function run()
    {
        // Obtener alumnos y plataformas
        $alumnos = Alumno::all();
        $plataformas = Plataforma::all();

        if ($alumnos->isEmpty() || $plataformas->isEmpty()) {
            return;
        }

        // Asignar plataformas con usuario y contraseña a los alumnos
        foreach ($alumnos as $alumno) {
            $plataformasAleatorias = $plataformas->random(rand(1, $plataformas->count()));

            foreach ($plataformasAleatorias as $plataforma) {
                DB::table('alumno_plataforma')->insert([
                    'alumno_id'     => $alumno->id,
                    'plataforma_id' => $plataforma->id,
                    'usuario'       => strtolower($alumno->nombre) . rand(100, 999),
                    'contraseña'    => Str::random(10),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}

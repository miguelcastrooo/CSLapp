<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Hermano;
use App\Models\Alumno;

class HermanoFactory extends Factory
{
    protected $model = Hermano::class;

    public function definition(): array
    {
        return [
            'alumno_id' => Alumno::factory(), // Relaciona con un alumno
            'nombre' => $this->faker->firstName,
            'apellido_paterno' => $this->faker->lastName,
            'apellido_materno' => $this->faker->lastName,
            'edad' => $this->faker->numberBetween(1, 18),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

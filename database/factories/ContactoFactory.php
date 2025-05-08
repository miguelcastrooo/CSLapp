<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contacto;
use App\Models\Alumno;

class ContactoFactory extends Factory
{
    protected $model = Contacto::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name,
            'telefono' => $this->faker->phoneNumber,
            'tipo_contacto' => $this->faker->randomElement(['Padre', 'Madre', 'Tutor', 'Otro']),
            'correo' => $this->faker->unique()->safeEmail,
            'alumno_id' => Alumno::factory(), // Relaciona con un alumno
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

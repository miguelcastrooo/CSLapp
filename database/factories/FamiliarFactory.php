<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Familiar;
use App\Models\Alumno;

class FamiliarFactory extends Factory
{
    protected $model = Familiar::class;

    public function definition(): array
    {
        return [
            'alumno_id' => Alumno::factory(), // Relaciona con un alumno
            'nombre' => $this->faker->firstName,
            'apellido_paterno' => $this->faker->lastName,
            'apellido_materno' => $this->faker->lastName,
            'fecha_nacimiento' => $this->faker->date(),
            'estado_civil' => $this->faker->randomElement(['Casado', 'Soltero', 'Divorciado', 'Viudo', 'Otro']),
            'domicilio' => $this->faker->streetAddress,
            'no_domicilio' => $this->faker->numberBetween(1, 100),
            'cp' => $this->faker->postcode,
            'colonia' => $this->faker->citySuffix,
            'ciudad' => $this->faker->city,
            'estado' => $this->faker->state,
            'telefono_fijo' => $this->faker->optional()->phoneNumber,
            'celular' => $this->faker->phoneNumber,
            'correo' => $this->faker->unique()->safeEmail,
            'profesion' => $this->faker->jobTitle,
            'ocupacion' => $this->faker->word,
            'empresa_nombre' => $this->faker->company,
            'empresa_telefono' => $this->faker->phoneNumber,
            'empresa_domicilio' => $this->faker->address,
            'empresa_ciudad' => $this->faker->city,
            'tipo_familiar' => $this->faker->randomElement(['Padre', 'Madre', 'Tutor', 'Otro']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

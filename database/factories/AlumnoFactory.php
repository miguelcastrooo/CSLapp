<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Alumno;

class AlumnoFactory extends Factory
{
    protected $model = Alumno::class;

    public function definition(): array
    {
        return [
            'matricula' => $this->faker->unique()->numerify('MAT######'),
            'nombre' => $this->faker->firstName,
            'apellidopaterno' => $this->faker->lastName,
            'apellidomaterno' => $this->faker->lastName,
            'nivel_educativo_id' => $this->faker->numberBetween(1, 4), // Depende de los niveles educativos en la BD
            'grado_id' => $this->faker->numberBetween(1, 13), // Depende de los grados en la BD
            'seccion' => $this->faker->randomElement(['A', 'B', 'C']),
            'fecha_inscripcion' => $this->faker->date(),
            'status' => $this->faker->boolean ? 1 : 0,
            'fecha_inicio' => $this->faker->date(),
            'lugar_nacimiento' => $this->faker->city,
            'fecha_nacimiento' => $this->faker->date('Y-m-d', '-5 years'),
            'edad_anios' => $this->faker->numberBetween(3, 16),
            'edad_meses' => $this->faker->numberBetween(0, 11),
            'sexo' => $this->faker->randomElement(['Masculino', 'Femenino', 'Sin Definir']),
            'domicilio' => $this->faker->streetAddress,
            'cp' => $this->faker->postcode,
            'cerrada' => $this->faker->streetName,
            'colonia' => $this->faker->citySuffix,
            'ciudad' => $this->faker->city,
            'estado' => $this->faker->state,
            'enfermedades_alergias' => $this->faker->optional()->sentence,
            'pediatra_nombre' => $this->faker->name,
            'pediatra_telefono' => $this->faker->phoneNumber,
            'no_domicilio' => $this->faker->buildingNumber,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

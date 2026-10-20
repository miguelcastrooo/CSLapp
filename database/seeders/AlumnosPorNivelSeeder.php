<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\AlumnoPlataforma;
use App\Models\Contacto;
use App\Models\Familiar;
use App\Models\Grado;
use App\Models\Hermano;
use Illuminate\Database\Seeder;

class AlumnosPorNivelSeeder extends Seeder
{
    private array $hombres = ['Santiago','Mateo','Sebastián','Emiliano','Diego','Leonardo','Daniel','Ángel','Iker','Rodrigo','Carlos','Fernando','Andrés','Luis','Gael'];
    private array $mujeres = ['Sofía','Valentina','Camila','Regina','Ximena','Renata','Mariana','Fernanda','Isabella','Paula','Daniela','Ana Paula','Julieta','Victoria','Lucía'];
    private array $apellidos = ['García','Hernández','Martínez','López','González','Rodríguez','Pérez','Sánchez','Ramírez','Flores','Gómez','Díaz','Reyes','Morales','Cruz','Ortiz','Castillo','Vargas','Mendoza','Ruiz','Torres','Aguilar','Salazar','Treviño','Garza'];
    private array $colonias = ['Centro','Torreón Jardín','Los Ángeles','Residencial del Norte','Villas Universidad','Campestre La Rosita','Navarro','San Isidro'];
    private array $calles = ['Av. Juárez','Blvd. Revolución','Calle Morelos','Av. Hidalgo','Calle Allende','Blvd. Independencia','Calle Cuauhtémoc'];

    // Plataformas por nivel (ids según PlataformaSeeder: 1 Classroom, 2 Moodle, 3 hmh, 4 Mathletics, 5 Progrentis)
    private array $plataformasPorNivel = [
        1 => [1, 2],
        2 => [1, 2, 3],
        3 => [1, 2, 3, 4, 5],
        4 => [1, 2, 4],
    ];

    private array $edadPorGrado = [
        'BabiesRoom' => 2, '1° Kinder' => 3, '2° Kinder' => 4, '3° Kinder' => 5,
        '1°' => 6, '2°' => 7, '3°' => 8, '4°' => 9, '5°' => 10, '6°' => 11,
        '1° Secundaria' => 12, '2° Secundaria' => 13, '3° Secundaria' => 14,
    ];

    private int $contadorMatricula;

    public function run(): void
    {
        $niveles = Grado::query()->distinct()->pluck('nivel_educativo_id');

        if ($niveles->isEmpty()) {
            $this->command->error('No hay grados. Corre antes NivelEducativoSeeder, GradoSeeder y PlataformaSeeder.');
            return;
        }

        $anio = now()->format('y');
        $ultima = Alumno::where('matricula', 'like', "$anio%")->max('matricula');
        $this->contadorMatricula = ($ultima && strlen($ultima) === 6) ? (int) substr($ultima, 2) : 1723;

        foreach ($niveles as $nivelId) {
            $grados = Grado::where('nivel_educativo_id', $nivelId)->get();

            for ($i = 0; $i < 10; $i++) {
                $this->crearAlumno((int) $nivelId, $grados->random(), $anio);
            }
            $this->command->info("Nivel $nivelId: 10 alumnos creados.");
        }
    }

    private function crearAlumno(int $nivelId, Grado $grado, string $anio): void
    {
        $esNino = (bool) random_int(0, 1);
        $nombre = $esNino ? $this->hombres[array_rand($this->hombres)] : $this->mujeres[array_rand($this->mujeres)];
        $paterno = $this->apellidos[array_rand($this->apellidos)];
        $materno = $this->apellidos[array_rand($this->apellidos)];

        $this->contadorMatricula++;
        $matricula = $anio . str_pad($this->contadorMatricula, 4, '0', STR_PAD_LEFT);

        $edad = $this->edadPorGrado[$grado->nombre] ?? 8;
        $meses = random_int(0, 11);
        $nacimiento = now()->subYears($edad)->subMonths($meses);
        $inscripcion = now()->subMonths(random_int(1, 18));

        $calle = $this->calles[array_rand($this->calles)];
        $colonia = $this->colonias[array_rand($this->colonias)];
        $cp = (string) random_int(27000, 27299);
        $noDom = (string) random_int(100, 3500);

        $alumno = Alumno::forceCreate([
            'matricula' => $matricula,
            'nombre' => $nombre,
            'apellidopaterno' => $paterno,
            'apellidomaterno' => $materno,
            'nivel_educativo_id' => $nivelId,
            'grado_id' => $grado->id,
            'seccion' => random_int(0, 1) ? 'A' : 'B',
            'fecha_inscripcion' => $inscripcion->toDateString(),
            'fecha_inicio' => $inscripcion->copy()->addWeeks(2)->toDateString(),
            'lugar_nacimiento' => 'Torreón, Coahuila',
            'fecha_nacimiento' => $nacimiento->toDateString(),
            'edad_anios' => $edad,
            'edad_meses' => $meses,
            'sexo' => $esNino ? 'Masculino' : 'Femenino',
            'domicilio' => $calle,
            'no_domicilio' => $noDom,
            'cp' => $cp,
            'cerrada' => null,
            'colonia' => $colonia,
            'ciudad' => 'Torreón',
            'estado' => 'Coahuila',
            'enfermedades_alergias' => random_int(0, 4) === 0 ? 'Alergia a la penicilina' : 'Ninguna',
            'pediatra_nombre' => 'Dr. ' . $this->apellidos[array_rand($this->apellidos)],
            'pediatra_telefono' => $this->telefono(),
            // Columnas heredadas de la migración original (NOT NULL / sin uso actual)
            'contacto1nombre' => null,
            'telefono1' => null,
            'correo_familia' => null,
            'status' => 1,
        ]);

        $this->crearFamiliares($alumno, $paterno, $materno, $calle, $noDom, $cp, $colonia);
        $this->crearContactos($alumno);
        $this->crearHermanos($alumno, $paterno, $materno);
        $this->crearPlataformas($alumno, $nivelId, $nombre, $matricula, $inscripcion->format('y'));
    }

    private function crearFamiliares(Alumno $alumno, string $paterno, string $materno, string $calle, string $noDom, string $cp, string $colonia): void
    {
        $personas = [
            'Padre' => [$this->hombres[array_rand($this->hombres)], $paterno, $this->apellidos[array_rand($this->apellidos)]],
            'Madre' => [$this->mujeres[array_rand($this->mujeres)], $materno, $this->apellidos[array_rand($this->apellidos)]],
            'Tutor' => [$this->hombres[array_rand($this->hombres)], $this->apellidos[array_rand($this->apellidos)], $this->apellidos[array_rand($this->apellidos)]],
        ];

        foreach ($personas as $tipo => [$n, $ap, $am]) {
            Familiar::create([
                'alumno_id' => $alumno->id,
                'tipo_familiar' => $tipo,
                'nombre' => $n,
                'apellido_paterno' => $ap,
                'apellido_materno' => $am,
                'fecha_nacimiento' => now()->subYears(random_int(28, 55))->subDays(random_int(0, 364))->toDateString(),
                'estado_civil' => ['Casado', 'Soltero', 'Divorciado', 'Viudo'][array_rand(['Casado', 'Soltero', 'Divorciado', 'Viudo'])],
                'domicilio' => $calle,
                'no_domicilio' => $noDom,
                'cp' => $cp,
                'colonia' => $colonia,
                'ciudad' => 'Torreón',
                'estado' => 'Coahuila',
                'telefono_fijo' => '871' . random_int(1000000, 9999999),
                'celular' => $this->telefono(),
                'correo' => strtolower($this->limpiar($n . '.' . $ap)) . random_int(1, 99) . '@example.com',
                'profesion' => ['Ingeniero', 'Contador', 'Médico', 'Abogado', 'Docente', 'Arquitecto'][random_int(0, 5)],
                'ocupacion' => ['Empleado', 'Empresario', 'Independiente', 'Servidor público'][random_int(0, 3)],
                'empresa_nombre' => ['Grupo Lala', 'Peñoles', 'Gómez Palacio Logística', 'Consultorio Particular', 'Negocio Propio'][random_int(0, 4)],
                'empresa_telefono' => '871' . random_int(1000000, 9999999),
                'empresa_domicilio' => $this->calles[array_rand($this->calles)] . ' ' . random_int(100, 2500),
                'empresa_ciudad' => 'Torreón',
            ]);
        }
    }

    private function crearContactos(Alumno $alumno): void
    {
        $tipos = ['Abuelo(a)', 'Tío(a)', 'Vecino(a)', 'Amigo(a) de la familia'];

        for ($i = 1; $i <= random_int(1, 2); $i++) {
            $n = random_int(0, 1) ? $this->hombres[array_rand($this->hombres)] : $this->mujeres[array_rand($this->mujeres)];
            $ap = $this->apellidos[array_rand($this->apellidos)];

            Contacto::create([
                'alumno_id' => $alumno->id,
                'nombre' => "$n $ap",
                'telefono' => $this->telefono(),
                'correo' => strtolower($this->limpiar($n . $ap)) . random_int(1, 99) . '@example.com',
                'tipo_contacto' => $tipos[array_rand($tipos)],
            ]);
        }
    }

    private function crearHermanos(Alumno $alumno, string $paterno, string $materno): void
    {
        for ($i = 0; $i < random_int(0, 2); $i++) {
            Hermano::create([
                'alumno_id' => $alumno->id,
                'nombre' => random_int(0, 1) ? $this->hombres[array_rand($this->hombres)] : $this->mujeres[array_rand($this->mujeres)],
                'apellido_paterno' => $paterno,
                'apellido_materno' => $materno,
                'edad' => random_int(1, 20),
            ]);
        }
    }

    private function crearPlataformas(Alumno $alumno, int $nivelId, string $nombre, string $matricula, string $anioIns): void
    {
        $primerNombre = strtolower($this->limpiar(explode(' ', $nombre)[0]));
        $primerApellido = strtolower($this->limpiar($alumno->apellidopaterno));

        foreach ($this->plataformasPorNivel[$nivelId] ?? [1, 2] as $plataformaId) {
            [$usuario, $pass] = match ($plataformaId) {
                1 => ["{$primerNombre}{$primerApellido}{$matricula}@colegiosanluis.com.mx", 'Csl$' . $primerNombre . $anioIns],
                2 => ['Csl-' . $matricula, $matricula],
                default => ["{$primerNombre}{$matricula}", 'Pass' . random_int(1000, 9999)],
            };

            AlumnoPlataforma::create([
                'alumno_id' => $alumno->id,
                'plataforma_id' => $plataformaId,
                'usuario' => $usuario,
                'contraseña' => $pass,
            ]);
        }
    }

    private function telefono(): string
    {
        return '871' . random_int(1000000, 9999999);
    }

    private function limpiar(string $texto): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', strtr($texto, 'áéíóúÁÉÍÓÚñÑ', 'aeiouAEIOUnN'));
    }
}
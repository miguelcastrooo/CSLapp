<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdditionalDemoStudentsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();
            $demoPassword = env('DEMO_SEED_PASSWORD') ?: Str::random(32);
            $superAdminId = DB::table('users')->orderBy('id')->value('id');
            $levels = DB::table('nivel_educativo')->pluck('id', 'nombre');
            $platforms = DB::table('plataformas')->pluck('id', 'nombre');

            $students = [
                ['matricula' => 'NUEVO-DEMO-01', 'nombre' => 'Sofía', 'paterno' => 'Ramírez', 'materno' => 'Luna', 'nivel' => 'Preescolar', 'sexo' => 'Femenino'],
                ['matricula' => 'NUEVO-DEMO-02', 'nombre' => 'Mateo', 'paterno' => 'Hernández', 'materno' => 'Cruz', 'nivel' => 'Primaria Baja', 'sexo' => 'Masculino'],
                ['matricula' => 'NUEVO-DEMO-03', 'nombre' => 'Valentina', 'paterno' => 'Torres', 'materno' => 'Mendoza', 'nivel' => 'Primaria Alta', 'sexo' => 'Femenino'],
                ['matricula' => 'NUEVO-DEMO-04', 'nombre' => 'Diego', 'paterno' => 'Castillo', 'materno' => 'Vega', 'nivel' => 'Secundaria', 'sexo' => 'Masculino'],
                ['matricula' => 'NUEVO-DEMO-05', 'nombre' => 'Camila', 'paterno' => 'Navarro', 'materno' => 'Santos', 'nivel' => 'Primaria Baja', 'sexo' => 'Femenino'],
            ];

            foreach ($students as $index => $student) {
                $levelId = $levels[$student['nivel']];
                $gradeId = DB::table('grados')->where('nivel_educativo_id', $levelId)->orderBy('id')->value('id');
                $number = $index + 1;
                $emailBase = strtolower(str_replace('-', '.', $student['matricula']));

                DB::table('alumnos')->updateOrInsert(['matricula' => $student['matricula']], [
                    'nombre' => $student['nombre'],
                    'apellidopaterno' => $student['paterno'],
                    'apellidomaterno' => $student['materno'],
                    'correo' => "{$emailBase}@example.test",
                    'contacto1nombre' => "Padre {$student['paterno']}",
                    'telefono1' => '55581000' . $number,
                    'correo_familia' => "familia.{$emailBase}@example.test",
                    'contacto2nombre' => "Madre {$student['materno']}",
                    'telefono2' => '55582000' . $number,
                    'nivel_educativo_id' => $levelId,
                    'grado_id' => $gradeId,
                    'plataforma_id' => $platforms['ClassRoom'] ?? null,
                    'seccion' => $number % 2 === 0 ? 'B' : 'A',
                    'fecha_inscripcion' => now()->subDays(10 + $number)->toDateString(),
                    'fecha_inicio' => now()->subDays(10 + $number)->toDateString(),
                    'lugar_nacimiento' => 'Ciudad de México',
                    'fecha_nacimiento' => now()->subYears(6 + $number)->toDateString(),
                    'edad_anios' => 6 + $number,
                    'edad_meses' => $number,
                    'sexo' => $student['sexo'],
                    'domicilio' => "Calle Horizonte {$number}",
                    'no_domicilio' => 200 + $number,
                    'cp' => '0100' . $number,
                    'cerrada' => 'Privada Horizonte',
                    'colonia' => 'Centro',
                    'ciudad' => 'Ciudad de México',
                    'estado' => 'CDMX',
                    'enfermedades_alergias' => $number === 3 ? 'Alergia estacional' : null,
                    'pediatra_nombre' => 'Dra. Rivera',
                    'pediatra_telefono' => '555830000' . $number,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $alumnoId = DB::table('alumnos')->where('matricula', $student['matricula'])->value('id');

                foreach ([
                    'Padre' => ['nombre' => "Carlos {$student['paterno']}", 'celular' => '55584000' . $number],
                    'Madre' => ['nombre' => "Laura {$student['materno']}", 'celular' => '55585000' . $number],
                    'Tutor' => ['nombre' => "Elena {$student['paterno']}", 'celular' => '55586000' . $number],
                ] as $tipo => $familiar) {
                    DB::table('familiares')->updateOrInsert(['alumno_id' => $alumnoId, 'tipo_familiar' => $tipo], [
                        'nombre' => $familiar['nombre'], 'apellido_paterno' => $student['paterno'], 'apellido_materno' => $student['materno'],
                        'fecha_nacimiento' => now()->subYears(35 + $number)->toDateString(), 'estado_civil' => 'Casado',
                        'domicilio' => "Calle Horizonte {$number}", 'no_domicilio' => 200 + $number, 'cp' => '0100' . $number,
                        'colonia' => 'Centro', 'ciudad' => 'Ciudad de México', 'estado' => 'CDMX', 'telefono_fijo' => null,
                        'celular' => $familiar['celular'], 'correo' => strtolower("{$tipo}.{$emailBase}@example.test"),
                        'profesion' => 'Profesional', 'ocupacion' => 'Empleado', 'empresa_nombre' => 'Empresa Horizonte',
                        'empresa_telefono' => '555870000' . $number, 'empresa_domicilio' => 'Avenida Central 10', 'empresa_ciudad' => 'Ciudad de México',
                        'created_at' => $now, 'updated_at' => $now,
                    ]);
                }

                foreach (['Padre', 'Madre'] as $tipo) {
                    DB::table('contactos')->updateOrInsert(['alumno_id' => $alumnoId, 'tipo_contacto' => $tipo], [
                        'nombre' => "{$tipo} de {$student['nombre']}", 'telefono' => '55588000' . $number,
                        'correo' => strtolower("{$tipo}.{$emailBase}@example.test"), 'created_at' => $now, 'updated_at' => $now,
                    ]);
                    $contactoId = DB::table('contactos')->where('alumno_id', $alumnoId)->where('tipo_contacto', $tipo)->value('id');
                    DB::table('alumno_contacto')->updateOrInsert(['alumno_id' => $alumnoId, 'contacto_id' => $contactoId], ['created_at' => $now, 'updated_at' => $now]);
                }

                DB::table('hermanos')->updateOrInsert(['alumno_id' => $alumnoId, 'nombre' => "Hermano de {$student['nombre']}"], [
                    'apellido_paterno' => $student['paterno'], 'apellido_materno' => $student['materno'], 'edad' => 9 + $number, 'created_at' => $now, 'updated_at' => $now,
                ]);

                foreach (['ClassRoom', 'Moodle'] as $platformName) {
                    if (isset($platforms[$platformName])) {
                        DB::table('alumno_plataforma')->updateOrInsert(['alumno_id' => $alumnoId, 'plataforma_id' => $platforms[$platformName]], [
                            'usuario' => $emailBase, 'contraseña' => $demoPassword, 'created_at' => $now, 'updated_at' => $now,
                        ]);
                    }
                }

                DB::table('padres_tutores')->updateOrInsert(['alumno_id' => $alumnoId, 'tipo' => 'padre'], [
                    'nombre' => "Carlos {$student['paterno']}", 'fecha_nacimiento' => now()->subYears(40)->toDateString(), 'estado_civil' => 'Casado',
                    'domicilio' => "Calle Horizonte {$number}", 'telefono_fijo' => null, 'telefono_celular' => '55584000' . $number,
                    'email' => "padre.{$emailBase}@example.test", 'profesion' => 'Profesional', 'ocupacion' => 'Empleado',
                    'empresa_nombre' => 'Empresa Horizonte', 'empresa_telefono' => '555870000' . $number, 'empresa_domicilio' => 'Avenida Central 10',
                    'empresa_ciudad' => 'Ciudad de México', 'created_at' => $now, 'updated_at' => $now,
                ]);

                DB::table('contactos_alumno')->updateOrInsert(['alumno_id' => $alumnoId], [
                    'padre_nombre' => "Carlos {$student['paterno']}", 'padre_domicilio' => "Calle Horizonte {$number}", 'padre_no' => 200 + $number,
                    'padre_cp' => '0100' . $number, 'padre_colonia' => 'Centro', 'padre_ciudad' => 'Ciudad de México', 'padre_estado' => 'CDMX',
                    'padre_celular' => '55584000' . $number, 'padre_correo' => "padre.{$emailBase}@example.test", 'madre_nombre' => "Laura {$student['materno']}",
                    'madre_domicilio' => "Calle Horizonte {$number}", 'madre_no' => 200 + $number, 'madre_cp' => '0100' . $number,
                    'madre_colonia' => 'Centro', 'madre_ciudad' => 'Ciudad de México', 'madre_estado' => 'CDMX', 'madre_celular' => '55585000' . $number,
                    'madre_correo' => "madre.{$emailBase}@example.test", 'created_at' => $now, 'updated_at' => $now,
                ]);

                DB::table('historial_alumnos')->updateOrInsert(['alumno_id' => $alumnoId, 'accion' => 'alta_demo'], [
                    'alumno_nombre' => "{$student['nombre']} {$student['paterno']} {$student['materno']}", 'user_id' => $superAdminId,
                    'motivo' => 'Registro adicional de prueba', 'cambios' => null, 'fecha' => $now, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        });
    }
}

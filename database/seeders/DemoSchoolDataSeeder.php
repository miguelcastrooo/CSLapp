<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DemoSchoolDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();
            $demoPassword = env('DEMO_SEED_PASSWORD') ?: Str::random(32);
            $levels = ['Preescolar', 'Primaria Baja', 'Primaria Alta', 'Secundaria'];
            $gradesByLevel = [
                'Preescolar' => ['Babies Room', '1 Kinder', '2 Kinder', '3 Kinder'],
                'Primaria Baja' => ['1 Primaria', '2 Primaria', '3 Primaria'],
                'Primaria Alta' => ['4 Primaria', '5 Primaria', '6 Primaria'],
                'Secundaria' => ['1 Secundaria', '2 Secundaria', '3 Secundaria'],
            ];
            $platformNames = ['ClassRoom', 'Moodle', 'HMH', 'Mathletics', 'Progrentis'];

            foreach ($levels as $name) {
                DB::table('nivel_educativo')->updateOrInsert(['nombre' => $name], [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $levelIds = DB::table('nivel_educativo')->pluck('id', 'nombre');
            foreach ($gradesByLevel as $level => $grades) {
                foreach ($grades as $grade) {
                    DB::table('grados')->updateOrInsert([
                        'nombre' => $grade,
                        'nivel_educativo_id' => $levelIds[$level],
                    ], ['created_at' => $now, 'updated_at' => $now]);
                }
            }

            foreach ($platformNames as $name) {
                DB::table('plataformas')->updateOrInsert(['nombre' => $name], [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $platformIds = DB::table('plataformas')->pluck('id', 'nombre');
            foreach ($levels as $level) {
                foreach ($platformNames as $platform) {
                    DB::table('nivel_plataforma')->updateOrInsert([
                        'nivel_educativo_id' => $levelIds[$level],
                        'plataforma_id' => $platformIds[$platform],
                    ], ['created_at' => $now, 'updated_at' => $now]);
                }
            }

            $permissions = collect(['ver_alumnos', 'registrar_alumnos', 'editar_alumnos'])
                ->map(fn (string $name) => Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']));
            $roles = collect(['SuperAdmin', 'ControlEscolar', 'CoordinacionPreescolar', 'CoordinacionPrimaria', 'CoordinacionSecundaria'])
                ->mapWithKeys(fn (string $name) => [$name => Role::firstOrCreate(['name' => $name, 'guard_name' => 'web'])]);

            $roles['SuperAdmin']->syncPermissions($permissions);
            $roles['ControlEscolar']->syncPermissions($permissions->take(2));

            foreach (['CoordinacionPreescolar', 'CoordinacionPrimaria', 'CoordinacionSecundaria'] as $roleName) {
                $role = $roles[$roleName];
                $assignedLevels = match ($roleName) {
                    'CoordinacionPreescolar' => ['Preescolar'],
                    'CoordinacionPrimaria' => ['Primaria Baja', 'Primaria Alta'],
                    default => ['Secundaria'],
                };

                foreach ($assignedLevels as $level) {
                    DB::table('nivel_educativo_role')->updateOrInsert([
                        'role_id' => $role->id,
                        'nivel_educativo_id' => $levelIds[$level],
                    ]);
                }
            }

            foreach ([
                'control.demo@colegiosanluis.local' => ['Control Escolar Demo', 'ControlEscolar'],
                'preescolar.demo@colegiosanluis.local' => ['Coordinacion Preescolar Demo', 'CoordinacionPreescolar'],
                'primaria.demo@colegiosanluis.local' => ['Coordinacion Primaria Demo', 'CoordinacionPrimaria'],
                'secundaria.demo@colegiosanluis.local' => ['Coordinacion Secundaria Demo', 'CoordinacionSecundaria'],
            ] as $email => [$name, $roleName]) {
                $user = User::updateOrCreate(['email' => $email], [
                    'name' => $name,
                    'password' => Hash::make($demoPassword),
                ]);
                $user->syncRoles([$roles[$roleName]]);
            }

            foreach ($levels as $levelIndex => $level) {
                $levelId = $levelIds[$level];
                $gradeIds = DB::table('grados')->where('nivel_educativo_id', $levelId)->pluck('id')->all();

                for ($studentNumber = 1; $studentNumber <= 10; $studentNumber++) {
                    $matricula = sprintf('DEMO-%02d-%02d', $levelIndex + 1, $studentNumber);
                    $gradeId = $gradeIds[($studentNumber - 1) % count($gradeIds)];
                    $student = $this->studentData($matricula, $levelId, $gradeId, $platformIds['ClassRoom'], $studentNumber, $now);
                    DB::table('alumnos')->updateOrInsert(['matricula' => $matricula], $student);

                    $alumnoId = DB::table('alumnos')->where('matricula', $matricula)->value('id');
                $this->seedStudentRelations($alumnoId, $matricula, $studentNumber, $platformIds, $now, $demoPassword);
                }

                $gradeId = $gradeIds[0];
                $this->seedGraduate($levelIndex + 1, $levelId, $gradeId, $platformIds['Moodle'], $now);
                $this->seedArchivedStudent($levelIndex + 1, $levelId, $gradeId, $platformIds['Moodle'], $now, $demoPassword);

                $referenceAlumnoId = DB::table('alumnos')->where('matricula', sprintf('DEMO-%02d-01', $levelIndex + 1))->value('id');
                DB::table('bajas_alumnos')->updateOrInsert(['alumno_id' => $referenceAlumnoId], [
                    'matricula' => sprintf('DEMO-%02d-01', $levelIndex + 1),
                    'nombre_completo' => 'Alumno Demo ' . ($levelIndex + 1) . ' 01',
                    'nivel_educativo_id' => $levelId,
                    'grado_id' => $gradeId,
                    'seccion' => 'A',
                    'motivo' => 'Historial de prueba',
                    'fecha_baja' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                DB::table('reactivaciones_alumnos')->updateOrInsert(['alumno_id' => $referenceAlumnoId], [
                    'motivo' => 'Reactivacion de prueba',
                    'fecha_reactivacion' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }

    private function studentData(string $matricula, int $levelId, int $gradeId, int $platformId, int $number, $now): array
    {
        return [
            'nombre' => 'Alumno Demo ' . $number,
            'apellidopaterno' => 'Prueba',
            'apellidomaterno' => 'CSL',
            'correo' => strtolower($matricula) . '@example.test',
            'contacto1nombre' => 'Padre Demo ' . $number,
            'telefono1' => '555000' . str_pad((string) $number, 4, '0', STR_PAD_LEFT),
            'correo_familia' => 'familia.' . strtolower($matricula) . '@example.test',
            'contacto2nombre' => 'Madre Demo ' . $number,
            'telefono2' => '555100' . str_pad((string) $number, 4, '0', STR_PAD_LEFT),
            'nivel_educativo_id' => $levelId,
            'grado_id' => $gradeId,
            'plataforma_id' => $platformId,
            'seccion' => ['A', 'B', 'C'][($number - 1) % 3],
            'fecha_inscripcion' => now()->subMonths($number)->toDateString(),
            'fecha_inicio' => now()->subMonths($number)->toDateString(),
            'lugar_nacimiento' => 'Ciudad de Mexico',
            'fecha_nacimiento' => now()->subYears(5 + ($number % 10))->toDateString(),
            'edad_anios' => 5 + ($number % 10),
            'edad_meses' => $number % 12,
            'sexo' => $number % 2 ? 'Masculino' : 'Femenino',
            'domicilio' => 'Calle Prueba',
            'no_domicilio' => (string) (100 + $number),
            'cp' => '01000',
            'cerrada' => 'Cerrada Demo',
            'colonia' => 'Colonia Demo',
            'ciudad' => 'Ciudad de Mexico',
            'estado' => 'CDMX',
            'enfermedades_alergias' => null,
            'pediatra_nombre' => 'Dra. Demo',
            'pediatra_telefono' => '5552000000',
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function seedStudentRelations(int $alumnoId, string $matricula, int $number, $platformIds, $now, string $demoPassword): void
    {
        $contacts = [];
        foreach (['Padre', 'Madre'] as $type) {
            DB::table('contactos')->updateOrInsert(['alumno_id' => $alumnoId, 'tipo_contacto' => $type], [
                'nombre' => $type . ' Demo ' . $number,
                'telefono' => '5553' . str_pad((string) $number, 6, '0', STR_PAD_LEFT),
                'correo' => strtolower($type) . '.' . strtolower($matricula) . '@example.test',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $contacts[] = DB::table('contactos')->where('alumno_id', $alumnoId)->where('tipo_contacto', $type)->value('id');

            DB::table('familiares')->updateOrInsert(['alumno_id' => $alumnoId, 'tipo_familiar' => $type], [
                'nombre' => $type . ' Demo',
                'apellido_paterno' => 'Prueba',
                'apellido_materno' => 'CSL',
                'fecha_nacimiento' => now()->subYears(35 + $number)->toDateString(),
                'estado_civil' => 'Casado',
                'domicilio' => 'Calle Prueba',
                'no_domicilio' => '100',
                'cp' => '01000',
                'colonia' => 'Colonia Demo',
                'ciudad' => 'Ciudad de Mexico',
                'estado' => 'CDMX',
                'telefono_fijo' => null,
                'celular' => '5554' . str_pad((string) $number, 6, '0', STR_PAD_LEFT),
                'correo' => strtolower($type) . '.' . strtolower($matricula) . '@example.test',
                'profesion' => 'Profesional',
                'ocupacion' => 'Empleado',
                'empresa_nombre' => 'Empresa Demo',
                'empresa_telefono' => '5555000000',
                'empresa_domicilio' => 'Avenida Demo 1',
                'empresa_ciudad' => 'Ciudad de Mexico',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ($contacts as $contactId) {
            DB::table('alumno_contacto')->updateOrInsert(['alumno_id' => $alumnoId, 'contacto_id' => $contactId], [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('hermanos')->updateOrInsert(['alumno_id' => $alumnoId, 'nombre' => 'Hermano Demo'], [
            'apellido_paterno' => 'Prueba',
            'apellido_materno' => 'CSL',
            'edad' => 8 + ($number % 6),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        foreach (['ClassRoom', 'Moodle'] as $platform) {
            DB::table('alumno_plataforma')->updateOrInsert(['alumno_id' => $alumnoId, 'plataforma_id' => $platformIds[$platform]], [
                'usuario' => strtolower($matricula),
                'contraseña' => $demoPassword,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('padres_tutores')->updateOrInsert(['alumno_id' => $alumnoId, 'tipo' => 'padre'], [
            'nombre' => 'Padre Demo ' . $number,
            'fecha_nacimiento' => now()->subYears(40)->toDateString(),
            'estado_civil' => 'Casado',
            'domicilio' => 'Calle Prueba 100',
            'telefono_fijo' => null,
            'telefono_celular' => '5556000000',
            'email' => 'padre.' . strtolower($matricula) . '@example.test',
            'profesion' => 'Profesional',
            'ocupacion' => 'Empleado',
            'empresa_nombre' => 'Empresa Demo',
            'empresa_telefono' => '5555000000',
            'empresa_domicilio' => 'Avenida Demo 1',
            'empresa_ciudad' => 'Ciudad de Mexico',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('contactos_alumno')->updateOrInsert(['alumno_id' => $alumnoId], [
            'padre_nombre' => 'Padre Demo ' . $number,
            'padre_domicilio' => 'Calle Prueba',
            'padre_no' => '100',
            'padre_cp' => '01000',
            'padre_colonia' => 'Colonia Demo',
            'padre_ciudad' => 'Ciudad de Mexico',
            'padre_estado' => 'CDMX',
            'padre_celular' => '5556000000',
            'padre_correo' => 'padre.' . strtolower($matricula) . '@example.test',
            'madre_nombre' => 'Madre Demo ' . $number,
            'madre_domicilio' => 'Calle Prueba',
            'madre_no' => '100',
            'madre_cp' => '01000',
            'madre_colonia' => 'Colonia Demo',
            'madre_ciudad' => 'Ciudad de Mexico',
            'madre_estado' => 'CDMX',
            'madre_celular' => '5557000000',
            'madre_correo' => 'madre.' . strtolower($matricula) . '@example.test',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function seedGraduate(int $index, int $levelId, int $gradeId, int $platformId, $now): void
    {
        DB::table('egresados')->updateOrInsert(['matricula' => sprintf('EGRESADO-DEMO-%02d', $index)], [
            'nombre' => 'Egresado Demo ' . $index,
            'apellidopaterno' => 'Prueba',
            'apellidomaterno' => 'CSL',
            'contacto1nombre' => 'Familiar Demo',
            'telefono1' => '5558000000',
            'correo_familia' => 'egresado' . $index . '@example.test',
            'nivel_educativo_id' => $levelId,
            'grado_id' => $gradeId,
            'plataforma_id' => $platformId,
            'seccion' => 'A',
            'fecha_inscripcion' => now()->subYears(2)->toDateString(),
            'motivo_baja' => 'Egreso de prueba',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function seedArchivedStudent(int $index, int $levelId, int $gradeId, int $platformId, $now, string $demoPassword): void
    {
        $alumnoId = 900000 + $index;
        $matricula = sprintf('ARCHIVO-DEMO-%02d', $index);
        DB::table('alumnos_archivados')->updateOrInsert(['id' => $alumnoId], [
            'matricula' => $matricula,
            'nombre' => 'Alumno Archivado ' . $index,
            'apellidopaterno' => 'Prueba',
            'apellidomaterno' => 'CSL',
            'nivel_educativo_id' => $levelId,
            'grado_id' => $gradeId,
            'seccion' => 'A',
            'fecha_inscripcion' => now()->subYears(1)->toDateString(),
            'status' => 0,
            'fecha_inicio' => now()->subYears(1)->toDateString(),
            'lugar_nacimiento' => 'Ciudad de Mexico',
            'fecha_nacimiento' => now()->subYears(10)->toDateString(),
            'edad_anios' => 10,
            'edad_meses' => 0,
            'sexo' => 'Sin Definir',
            'domicilio' => 'Calle Archivo',
            'cp' => '01000',
            'cerrada' => 'Cerrada Demo',
            'colonia' => 'Colonia Demo',
            'ciudad' => 'Ciudad de Mexico',
            'estado' => 'CDMX',
            'no_domicilio' => '1',
            'fecha_archivo' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('alumno_plataforma_archivados')->updateOrInsert(['id' => 910000 + $index], [
            'alumno_id' => $alumnoId,
            'plataforma_id' => $platformId,
            'usuario' => strtolower($matricula),
            'contraseña' => $demoPassword,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('contactos_archivados')->updateOrInsert(['id' => 920000 + $index], [
            'alumno_id' => $alumnoId,
            'nombre' => 'Familiar Archivado',
            'telefono' => '5559000000',
            'tipo_contacto' => 'Tutor',
            'correo' => 'archivo' . $index . '@example.test',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('familiares_archivados')->updateOrInsert(['id' => 930000 + $index], [
            'alumno_id' => $alumnoId,
            'nombre' => 'Familiar Archivado',
            'apellido_paterno' => 'Prueba',
            'apellido_materno' => 'CSL',
            'tipo_familiar' => 'Tutor',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('hermanos_archivados')->updateOrInsert(['id' => 940000 + $index], [
            'alumno_id' => $alumnoId,
            'nombre' => 'Hermano Archivado',
            'apellido_paterno' => 'Prueba',
            'apellido_materno' => 'CSL',
            'edad' => 12,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

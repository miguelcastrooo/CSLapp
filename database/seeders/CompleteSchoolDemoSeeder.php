<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

/**
 * Crea cinco expedientes ficticios completos para recorrer el sistema.
 * Todos usan matrículas EJEMPLO y correos example.test, por lo que no son
 * información real ni deben utilizarse fuera de la demostración local.
 */
class CompleteSchoolDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();
            $responsableId = User::query()->where('activo', true)->orderBy('id')->value('id');
            $destinatarios = User::query()->where('activo', true)->orderBy('id')->pluck('id')->all();

            if (! $responsableId || $destinatarios === []) {
                throw new \RuntimeException('Se requiere al menos una cuenta activa para generar los historiales de demostración.');
            }

            $niveles = DB::table('nivel_educativo')->pluck('id', 'nombre');
            $muestras = [
                ['matricula' => 'EJEMPLO-2026-01', 'nombre' => 'Emilia', 'paterno' => 'Sánchez', 'materno' => 'Morales', 'nivel' => 'Preescolar', 'sexo' => 'Femenino', 'edad' => 4, 'seccion' => 'A'],
                ['matricula' => 'EJEMPLO-2026-02', 'nombre' => 'Bruno', 'paterno' => 'García', 'materno' => 'Lozano', 'nivel' => 'Primaria Baja', 'sexo' => 'Masculino', 'edad' => 7, 'seccion' => 'B'],
                ['matricula' => 'EJEMPLO-2026-03', 'nombre' => 'Renata', 'paterno' => 'Del Valle', 'materno' => 'Herrera', 'nivel' => 'Primaria Alta', 'sexo' => 'Femenino', 'edad' => 10, 'seccion' => 'A'],
                ['matricula' => 'EJEMPLO-2026-04', 'nombre' => 'Santiago', 'paterno' => 'Ortega', 'materno' => 'Cervantes', 'nivel' => 'Secundaria', 'sexo' => 'Masculino', 'edad' => 13, 'seccion' => 'B'],
                ['matricula' => 'EJEMPLO-2026-05', 'nombre' => 'Valeria', 'paterno' => 'Mendoza', 'materno' => 'Ríos', 'nivel' => 'Secundaria', 'sexo' => 'Femenino', 'edad' => 14, 'seccion' => 'A'],
            ];

            foreach ($muestras as $indice => $muestra) {
                $nivelId = $niveles[$muestra['nivel']] ?? null;
                $gradoId = $nivelId
                    ? DB::table('grados')->where('nivel_educativo_id', $nivelId)->where('activo', true)->orderBy('orden_promocion')->orderBy('id')->value('id')
                    : null;

                if (! $nivelId || ! $gradoId) {
                    throw new \RuntimeException("Falta configurar un grado activo para {$muestra['nivel']}.");
                }

                $numero = $indice + 1;
                $fechaInscripcion = now()->subDays(40 + $numero)->toDateString();
                $correoBase = strtolower("{$muestra['nombre']}.{$muestra['paterno']}.ejemplo{$numero}");
                $nombreCompleto = "{$muestra['nombre']} {$muestra['paterno']} {$muestra['materno']}";

                DB::table('alumnos')->updateOrInsert(['matricula' => $muestra['matricula']], [
                    'nombre' => $muestra['nombre'],
                    'apellidopaterno' => $muestra['paterno'],
                    'apellidomaterno' => $muestra['materno'],
                    'correo' => "{$correoBase}@example.test",
                    'contacto1nombre' => "Mariana {$muestra['paterno']}",
                    'telefono1' => '55570010' . $numero,
                    'correo_familia' => "familia.{$correoBase}@example.test",
                    'contacto2nombre' => "Jorge {$muestra['materno']}",
                    'telefono2' => '55570020' . $numero,
                    'nivel_educativo_id' => $nivelId,
                    'grado_id' => $gradoId,
                    'seccion' => $muestra['seccion'],
                    'fecha_inscripcion' => $fechaInscripcion,
                    'fecha_inicio' => $fechaInscripcion,
                    'lugar_nacimiento' => 'Ciudad de México',
                    'fecha_nacimiento' => now()->subYears($muestra['edad'])->subMonths($numero)->toDateString(),
                    'edad_anios' => $muestra['edad'],
                    'edad_meses' => $numero,
                    'sexo' => $muestra['sexo'],
                    'domicilio' => "Calle Bosques del Sol {$numero}",
                    'no_domicilio' => (string) (120 + $numero),
                    'cp' => '0310' . $numero,
                    'cerrada' => 'Privada del Roble',
                    'colonia' => 'Del Valle',
                    'ciudad' => 'Ciudad de México',
                    'estado' => 'CDMX',
                    'enfermedades_alergias' => $numero === 3 ? 'Alergia estacional al polen; se notificó a la familia.' : 'Sin alergias conocidas.',
                    'pediatra_nombre' => 'Dra. Elena Flores',
                    'pediatra_telefono' => '55570100' . $numero,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $alumnoId = DB::table('alumnos')->where('matricula', $muestra['matricula'])->value('id');

                foreach ([
                    'Madre' => ['nombre' => 'Mariana', 'telefono' => '55570010' . $numero, 'correo' => "mariana.{$correoBase}@example.test"],
                    'Padre' => ['nombre' => 'Jorge', 'telefono' => '55570020' . $numero, 'correo' => "jorge.{$correoBase}@example.test"],
                ] as $tipo => $familiar) {
                    DB::table('familiares')->updateOrInsert([
                        'alumno_id' => $alumnoId,
                        'tipo_familiar' => $tipo,
                    ], [
                        'nombre' => $familiar['nombre'],
                        'apellido_paterno' => $muestra['paterno'],
                        'apellido_materno' => $muestra['materno'],
                        'fecha_nacimiento' => now()->subYears($tipo === 'Madre' ? 38 : 40)->toDateString(),
                        'estado_civil' => 'Casado',
                        'domicilio' => "Calle Bosques del Sol {$numero}",
                        'no_domicilio' => (string) (120 + $numero),
                        'cp' => '0310' . $numero,
                        'colonia' => 'Del Valle',
                        'ciudad' => 'Ciudad de México',
                        'estado' => 'CDMX',
                        'telefono_fijo' => '55570200' . $numero,
                        'celular' => $familiar['telefono'],
                        'correo' => $familiar['correo'],
                        'profesion' => $tipo === 'Madre' ? 'Docente' : 'Administrador',
                        'ocupacion' => $tipo === 'Madre' ? 'Profesora de primaria' : 'Coordinador de operaciones',
                        'empresa_nombre' => $tipo === 'Madre' ? 'Centro Educativo del Valle' : 'Servicios Integrales del Centro',
                        'empresa_telefono' => '55570300' . $numero,
                        'empresa_domicilio' => 'Avenida Universidad 100',
                        'empresa_ciudad' => 'Ciudad de México',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    DB::table('contactos')->updateOrInsert([
                        'alumno_id' => $alumnoId,
                        'tipo_contacto' => $tipo,
                    ], [
                        'nombre' => "{$familiar['nombre']} {$muestra['paterno']} {$muestra['materno']}",
                        'telefono' => $familiar['telefono'],
                        'correo' => $familiar['correo'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                DB::table('hermanos')->updateOrInsert([
                    'alumno_id' => $alumnoId,
                    'nombre' => $numero % 2 ? 'Lucía' : 'Mateo',
                ], [
                    'apellido_paterno' => $muestra['paterno'],
                    'apellido_materno' => $muestra['materno'],
                    'edad' => $muestra['edad'] + 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $plataformas = DB::table('nivel_plataforma')
                    ->join('plataformas', 'plataformas.id', '=', 'nivel_plataforma.plataforma_id')
                    ->where('nivel_plataforma.nivel_educativo_id', $nivelId)
                    ->where('plataformas.activo', true)
                    ->get(['plataformas.id', 'plataformas.nombre']);

                foreach ($plataformas as $plataforma) {
                    $nombrePlataforma = strtolower(preg_replace('/[^a-z0-9]+/i', '', $plataforma->nombre));
                    DB::table('alumno_plataforma')->updateOrInsert([
                        'alumno_id' => $alumnoId,
                        'plataforma_id' => $plataforma->id,
                    ], [
                        'usuario' => "{$correoBase}.{$nombrePlataforma}",
                        'contraseña' => Crypt::encryptString("Demo{$numero}Acceso!"),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                DB::table('historial_alumnos')->updateOrInsert([
                    'alumno_id' => $alumnoId,
                    'accion' => 'alta_demo_completa',
                ], [
                    'alumno_nombre' => $nombreCompleto,
                    'user_id' => $responsableId,
                    'motivo' => 'Expediente ficticio completo para demostración del sistema.',
                    'cambios' => json_encode(['tipo' => 'demostracion', 'matricula' => $muestra['matricula']]),
                    'fecha' => now()->subDays($numero),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('bitacoras_contacto_familiares')->updateOrInsert([
                    'alumno_id' => $alumnoId,
                    'tipo' => 'llamada',
                ], [
                    'user_id' => $responsableId,
                    'resumen' => "Seguimiento de demostración: se confirmó con la familia de {$muestra['nombre']} la entrega de documentación y los accesos a plataformas.",
                    'fecha_contacto' => now()->subDays($numero)->setTime(16, 30),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('historial_seguridad')->updateOrInsert([
                    'evento' => "expediente_demo_{$numero}",
                ], [
                    'user_id' => $responsableId,
                    'descripcion' => "Se preparó el expediente ficticio {$muestra['matricula']} para una demostración local.",
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Seeder de demostración local',
                    'metadata' => json_encode(['demo' => true, 'alumno_id' => $alumnoId]),
                    'fecha' => now()->subDays($numero),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $notificacionId = sprintf('00000000-0000-4000-8000-%012d', $numero);
                DB::table('notifications')->updateOrInsert(['id' => $notificacionId], [
                    'type' => 'App\\Notifications\\AvisoEscolar',
                    'notifiable_type' => User::class,
                    'notifiable_id' => $destinatarios[$indice % count($destinatarios)],
                    'data' => json_encode([
                        'titulo' => "Expediente de ejemplo: {$muestra['nombre']}",
                        'mensaje' => "Se agregó el expediente ficticio {$muestra['matricula']} para recorrer las funciones del sistema.",
                        'detalles' => ['Enviado por' => 'Sistema de demostración', 'demo' => true],
                        'tipo' => 'aviso',
                        'enlace' => '/avisos',
                        'adjunto' => null,
                    ]),
                    'read_at' => null,
                    'created_at' => now()->subDays($numero),
                    'updated_at' => now()->subDays($numero),
                ]);
            }
        });
    }
}

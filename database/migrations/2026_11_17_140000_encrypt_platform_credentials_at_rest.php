<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Illuminate\Contracts\Encryption\DecryptException;

return new class extends Migration
{
    private const CAMPOS_LEGADOS = [
        'contraseña_classroom',
        'contraseña_moodle',
        'contraseña_mathletics',
        'contraseña_hmh',
        'contraseña_progrentis',
    ];

    public function up(): void
    {
        // El cifrado ocupa más espacio que una contraseña normal.
        Schema::table('alumno_plataforma', function ($table) {
            $table->text('contraseña')->nullable()->change();
        });

        if (Schema::hasTable('alumno_plataforma_archivados')) {
            Schema::table('alumno_plataforma_archivados', function ($table) {
                $table->text('contraseña')->nullable()->change();
            });
        }

        Schema::table('alumnos', function ($table) {
            foreach (self::CAMPOS_LEGADOS as $campo) {
                $table->text($campo)->nullable()->change();
            }
        });

        $this->cifrarColumna('alumno_plataforma', 'contraseña');
        $this->cifrarColumna('alumno_plataforma_archivados', 'contraseña');

        foreach (self::CAMPOS_LEGADOS as $campo) {
            $this->cifrarColumna('alumnos', $campo);
        }
    }

    /** Convierte datos existentes una sola vez, sin volver a cifrar registros ya protegidos. */
    private function cifrarColumna(string $tabla, string $columna): void
    {
        if (! Schema::hasTable($tabla) || ! Schema::hasColumn($tabla, $columna)) {
            return;
        }

        DB::table($tabla)->select('id', $columna)->orderBy('id')->cursor()->each(function ($registro) use ($tabla, $columna): void {
            $valor = $registro->{$columna};

            if ($valor === null || $valor === '') {
                return;
            }

            try {
                Crypt::decryptString($valor);
                return;
            } catch (DecryptException) {
                DB::table($tabla)->where('id', $registro->id)->update([$columna => Crypt::encryptString($valor)]);
            }
        });
    }

    /** No se descifran datos al revertir: conservarlos protegidos evita exponerlos por accidente. */
    public function down(): void
    {
        // Intencionalmente sin cambios destructivos.
    }
};

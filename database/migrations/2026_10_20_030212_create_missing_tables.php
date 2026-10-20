<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ---------- FAMILIARES ----------
        Schema::create('familiares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->string('nombre')->nullable();
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('no_domicilio')->nullable();
            $table->string('cp')->nullable();
            $table->string('colonia')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('estado')->nullable();
            $table->string('telefono_fijo')->nullable();
            $table->string('celular')->nullable();
            $table->string('correo')->nullable();
            $table->string('profesion')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('empresa_nombre')->nullable();
            $table->string('empresa_telefono')->nullable();
            $table->string('empresa_domicilio')->nullable();
            $table->string('empresa_ciudad')->nullable();
            $table->string('tipo_familiar')->nullable(); // Padre, Madre, Tutor
            $table->timestamps();
        });

        // ---------- COLUMNAS FALTANTES EN CONTACTOS ----------
        Schema::table('contactos', function (Blueprint $table) {
            if (!Schema::hasColumn('contactos', 'alumno_id')) {
                $table->foreignId('alumno_id')->nullable()->constrained('alumnos')->onDelete('cascade');
            }
            if (!Schema::hasColumn('contactos', 'tipo_contacto')) {
                $table->string('tipo_contacto')->nullable();
            }
        });

        // ---------- COLUMNAS FALTANTES EN HERMANOS ----------
        // (ya existe la migración add_apellidos_to_hermanos_table)

        // ---------- COLUMNAS FALTANTES EN ALUMNOS ----------
        Schema::table('alumnos', function (Blueprint $table) {
            if (!Schema::hasColumn('alumnos', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable();
            }
            if (!Schema::hasColumn('alumnos', 'no_domicilio')) {
                $table->string('no_domicilio')->nullable();
            }
        });

        // ---------- TABLAS DE ARCHIVADOS ----------
        Schema::create('alumnos_archivados', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('matricula')->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellidopaterno')->nullable();
            $table->string('apellidomaterno')->nullable();
            $table->unsignedBigInteger('nivel_educativo_id')->nullable();
            $table->unsignedBigInteger('grado_id')->nullable();
            $table->string('seccion')->nullable();
            $table->date('fecha_inscripcion')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->date('fecha_inicio')->nullable();
            $table->string('lugar_nacimiento')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->integer('edad_anios')->nullable();
            $table->integer('edad_meses')->nullable();
            $table->string('sexo')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('cp')->nullable();
            $table->string('cerrada')->nullable();
            $table->string('colonia')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('estado')->nullable();
            $table->text('enfermedades_alergias')->nullable();
            $table->string('pediatra_nombre')->nullable();
            $table->string('pediatra_telefono')->nullable();
            $table->string('no_domicilio')->nullable();
            $table->timestamp('fecha_archivo')->nullable();
            $table->timestamps();
        });

        Schema::create('alumno_plataforma_archivados', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('alumno_id')->index();
            $table->unsignedBigInteger('plataforma_id');
            $table->string('usuario')->nullable();
            $table->string('contraseña')->nullable();
            $table->timestamps();
        });

        Schema::create('contactos_archivados', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('alumno_id')->index();
            $table->string('nombre')->nullable();
            $table->string('telefono')->nullable();
            $table->string('tipo_contacto')->nullable();
            $table->string('correo')->nullable();
            $table->timestamps();
        });

        Schema::create('familiares_archivados', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('alumno_id')->index();
            foreach ([
                'nombre','apellido_paterno','apellido_materno','estado_civil','domicilio',
                'no_domicilio','cp','colonia','ciudad','estado','telefono_fijo','celular',
                'correo','profesion','ocupacion','empresa_nombre','empresa_telefono',
                'empresa_domicilio','empresa_ciudad','tipo_familiar',
            ] as $col) {
                $table->string($col)->nullable();
            }
            $table->date('fecha_nacimiento')->nullable();
            $table->timestamps();
        });

        Schema::create('hermanos_archivados', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('alumno_id')->index();
            $table->string('nombre')->nullable();
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();
            $table->integer('edad')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hermanos_archivados');
        Schema::dropIfExists('familiares_archivados');
        Schema::dropIfExists('contactos_archivados');
        Schema::dropIfExists('alumno_plataforma_archivados');
        Schema::dropIfExists('alumnos_archivados');
        Schema::dropIfExists('familiares');
    }
};
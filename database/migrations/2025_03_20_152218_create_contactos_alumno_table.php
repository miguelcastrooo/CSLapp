<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactosAlumnoTable extends Migration
{
    /**
     * Ejecuta la migración.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contactos_alumno', function (Blueprint $table) {
            $table->id(); // ID del registro
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade'); // Relación con la tabla alumnos

            // Información del Padre
            $table->string('padre_nombre');
            $table->date('padre_fecha_nacimiento')->nullable();
            $table->string('padre_estado_civil', 50)->nullable();
            $table->string('padre_domicilio');
            $table->string('padre_no');
            $table->string('padre_cp', 10);
            $table->string('padre_colonia', 100);
            $table->string('padre_ciudad', 100);
            $table->string('padre_estado', 100);
            $table->string('padre_telefono_fijo', 15)->nullable();
            $table->string('padre_celular', 15);
            $table->string('padre_correo', 100)->nullable();
            $table->string('padre_profesion', 100)->nullable();
            $table->string('padre_ocupacion', 100)->nullable();
            $table->string('padre_empresa_nombre', 255)->nullable();
            $table->string('padre_empresa_telefono', 15)->nullable();
            $table->string('padre_empresa_domicilio', 255)->nullable();
            $table->string('padre_empresa_ciudad', 100)->nullable();

            // Información de la Madre
            $table->string('madre_nombre');
            $table->date('madre_fecha_nacimiento')->nullable();
            $table->string('madre_estado_civil', 50)->nullable();
            $table->string('madre_domicilio');
            $table->string('madre_no');
            $table->string('madre_cp', 10);
            $table->string('madre_colonia', 100);
            $table->string('madre_ciudad', 100);
            $table->string('madre_estado', 100);
            $table->string('madre_telefono_fijo', 15)->nullable();
            $table->string('madre_celular', 15);
            $table->string('madre_correo', 100)->nullable();
            $table->string('madre_profesion', 100)->nullable();
            $table->string('madre_ocupacion', 100)->nullable();
            $table->string('madre_empresa_nombre', 255)->nullable();
            $table->string('madre_empresa_telefono', 15)->nullable();
            $table->string('madre_empresa_domicilio', 255)->nullable();
            $table->string('madre_empresa_ciudad', 100)->nullable();

            // Información del Tutor/Responsable Económico
            $table->string('tutor_nombre')->nullable();
            $table->date('tutor_fecha_nacimiento')->nullable();
            $table->string('tutor_estado_civil', 50)->nullable();
            $table->string('tutor_domicilio')->nullable();
            $table->string('tutor_no')->nullable();
            $table->string('tutor_cp', 10)->nullable();
            $table->string('tutor_colonia', 100)->nullable();
            $table->string('tutor_ciudad', 100)->nullable();
            $table->string('tutor_estado', 100)->nullable();
            $table->string('tutor_telefono_fijo', 15)->nullable();
            $table->string('tutor_celular', 15)->nullable();
            $table->string('tutor_correo', 100)->nullable();
            $table->string('tutor_profesion', 100)->nullable();
            $table->string('tutor_ocupacion', 100)->nullable();
            $table->string('tutor_empresa_nombre', 255)->nullable();
            $table->string('tutor_empresa_telefono', 15)->nullable();
            $table->string('tutor_empresa_domicilio', 255)->nullable();
            $table->string('tutor_empresa_ciudad', 100)->nullable();

            $table->timestamps(); // Timestamps para created_at y updated_at
        });
    }

    /**
     * Revierte la migración.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contactos_alumno');
    }
}

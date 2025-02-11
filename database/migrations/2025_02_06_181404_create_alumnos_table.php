<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnosTable extends Migration
{
    public function up()
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('matricula')->unique();
            $table->string('nombre');
            $table->string('apellidopaterno');
            $table->string('apellidomaterno');
            $table->string('correo')->nullable();
            $table->string('contacto1nombre');
            $table->string('telefono1');
            $table->string('correo_familia');
            $table->string('contacto2nombre')->nullable();
            $table->string('telefono2')->nullable();
            $table->string('usuario_classroom')->nullable();
            $table->string('contraseña_classroom')->nullable();
            $table->string('usuario_moodle')->nullable();
            $table->string('contraseña_moodle')->nullable();
            $table->string('usuario_mathletics')->nullable();
            $table->string('contraseña_mathletics')->nullable();
            $table->string('usuario_hmh')->nullable();
            $table->string('contraseña_hmh')->nullable();
            $table->string('usuario_progrentis')->nullable();
            $table->string('contraseña_progrentis')->nullable();

            // Relación con niveles_educativos (no cambia)
            $table->foreignId('nivel_educativo_id')->nullable()->constrained('nivel_educativo')->onDelete('set null');

            // Relación con grados
            $table->foreignId('grado_id')->constrained('grados')->onDelete('cascade');

            // Relación con plataformas
            $table->foreignId('plataforma_id')->nullable()->constrained('plataformas')->onDelete('set null');

            $table->string('seccion')->nullable();
            $table->date('fecha_inscripcion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alumnos');
    }
}

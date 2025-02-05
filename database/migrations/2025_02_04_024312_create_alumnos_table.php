<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('matricula')->unique();
            $table->string('nombre');
            $table->string('apellidopaterno');
            $table->string('apellidomaterno');
            $table->string('correo')->nullable(); // Correo de Classroom, asignado después por admin
            $table->string('contacto1nombre');
            $table->string('telefono1');
            $table->string('correo_familia');
            $table->string('contacto2nombre')->nullable(); // Puede ser null si no hay segundo contacto
            $table->string('telefono2')->nullable(); // Puede ser null si no hay segundo teléfono
            $table->string('usuario_classroom')->nullable(); // Generado automáticamente como 'Csl$(primer nombre)(año)'
            $table->string('contraseña_classroom')->nullable(); // Contraseña de classroom: 'matricula'
            $table->string('usuario_moodle')->nullable(); // Generado automáticamente como 'Csl-(matricula)'
            $table->string('contraseña_moodle')->nullable(); // Contraseña de Moodle: 'matricula'
            $table->string('usuario_mathletics')->nullable(); // Campo vacío, llenado por el admin después
            $table->string('contraseña_mathletics')->nullable(); // Campo vacío, llenado por el admin después
            $table->string('usuario_hmh')->nullable(); // Campo vacío, llenado por el admin después
            $table->string('contraseña_hmh')->nullable(); // Campo vacío, llenado por el admin después
            $table->string('usuario_progrentis')->nullable(); // Campo vacío, llenado por el admin después
            $table->string('contraseña_progrentis')->nullable(); // Campo vacío, llenado por el admin después
            $table->string('nivel_educativo');
            $table->string('grado');
            $table->string('seccion')->nullable(); // Rellenado por el admin después
            $table->date('fecha_inscripcion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnoContactoTable extends Migration
{
    public function up()
    {
        Schema::create('alumno_contacto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained()->onDelete('cascade'); // Referencia a la tabla alumnos
            $table->foreignId('contacto_id')->constrained()->onDelete('cascade'); // Referencia a la tabla contactos
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alumno_contacto');
    }
}

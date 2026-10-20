<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hermanos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumno_id'); // Relacionado con el alumno
            $table->string('nombre'); // Nombre del hermano
            $table->integer('edad'); // Edad del hermano
            $table->timestamps();

            // Relación con la tabla alumnos
            $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hermanos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bajas_alumnos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumno_id');
            $table->text('motivo');
            $table->timestamp('fecha_baja')->default(now());
            $table->timestamps();

            $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
        });

        Schema::create('reactivaciones_alumnos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumno_id');
            $table->text('motivo');
            $table->timestamp('fecha_reactivacion')->default(now());
            $table->timestamps();

            $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reactivaciones_alumnos');
        Schema::dropIfExists('bajas_alumnos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_alumnos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumno_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('accion', 50)->index();
            $table->text('motivo')->nullable();
            $table->json('cambios')->nullable();
            $table->timestamp('fecha')->useCurrent()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_alumnos');
    }
};

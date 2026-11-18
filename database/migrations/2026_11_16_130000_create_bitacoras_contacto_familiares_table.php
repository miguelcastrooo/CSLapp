<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacoras_contacto_familiares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('tipo', 30);
            $table->text('resumen');
            $table->timestamp('fecha_contacto');
            $table->timestamps();

            $table->index(['alumno_id', 'fecha_contacto']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacoras_contacto_familiares');
    }
};

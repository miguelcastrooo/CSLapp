<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('calificaciones')) {
            return;
        }

        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('plataforma_id')->constrained('plataformas')->restrictOnDelete();
            $table->string('periodo', 30);
            $table->decimal('calificacion', 5, 2);
            $table->foreignId('registrada_por')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['alumno_id', 'plataforma_id', 'periodo']);
        });
    }

    public function down(): void { Schema::dropIfExists('calificaciones'); }
};

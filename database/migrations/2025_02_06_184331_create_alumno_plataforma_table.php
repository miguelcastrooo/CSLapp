<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alumno_plataforma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->foreignId('plataforma_id')->constrained('plataformas')->onDelete('cascade');
            $table->string('usuario');
            $table->string('contraseña');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno_plataforma');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumno_campos_confirmados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->string('campo', 80);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmado_en');
            $table->timestamps();
            $table->unique(['alumno_id', 'campo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno_campos_confirmados');
    }
};

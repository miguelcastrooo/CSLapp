<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('alumno_ediciones')) {
            return;
        }

        Schema::create('alumno_ediciones', function (Blueprint $table) {
            $table->id();
            // Se usan índices simples para que funcione tanto en MySQL local como en Plesk.
            // Si un alumno o usuario se elimina, la reserva expira por sí sola en 15 minutos.
            $table->unsignedBigInteger('alumno_id')->unique();
            $table->unsignedBigInteger('user_id')->index();
            // dateTime evita el valor implícito incompatible con versiones antiguas de MySQL.
            $table->dateTime('iniciado_en');
            $table->dateTime('expira_en')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno_ediciones');
    }
};

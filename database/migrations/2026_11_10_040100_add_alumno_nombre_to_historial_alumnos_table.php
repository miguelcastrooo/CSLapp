<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('historial_alumnos', function (Blueprint $table) {
            $table->string('alumno_nombre')->nullable()->after('alumno_id');
        });
    }

    public function down(): void
    {
        Schema::table('historial_alumnos', function (Blueprint $table) {
            $table->dropColumn('alumno_nombre');
        });
    }
};

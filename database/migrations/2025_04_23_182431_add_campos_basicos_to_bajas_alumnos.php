<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bajas_alumnos', function (Blueprint $table) {
            if (!Schema::hasColumn('bajas_alumnos', 'matricula')) {
                $table->string('matricula')->nullable();
            }
            if (!Schema::hasColumn('bajas_alumnos', 'nombre_completo')) {
                $table->string('nombre_completo')->nullable();
            }
            if (!Schema::hasColumn('bajas_alumnos', 'nivel_educativo_id')) {
                $table->unsignedBigInteger('nivel_educativo_id')->nullable();
            }
            if (!Schema::hasColumn('bajas_alumnos', 'grado_id')) {
                $table->unsignedBigInteger('grado_id')->nullable();
            }
            if (!Schema::hasColumn('bajas_alumnos', 'seccion')) {
                $table->char('seccion', 1)->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};
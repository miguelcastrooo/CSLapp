<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bajas_alumnos', function (Blueprint $table) {
            $table->string('matricula')->nullable();
            $table->string('nombre_completo');
            $table->foreignId('nivel_educativo_id')->nullable()->constrained('niveles_educativos');
            $table->foreignId('grado_id')->nullable()->constrained('grados');
            $table->char('seccion', 1)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bajas_alumnos', function (Blueprint $table) {
            $table->dropColumn([
                'matricula',
                'nombre_completo',
                'nivel_educativo_id',
                'grado_id',
                'seccion',
            ]);
        });
    }
};

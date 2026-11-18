<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->string('color_texto', 7)->default('#183444')->after('color_fondo');
            $table->string('color_texto_secundario', 7)->default('#526474')->after('color_texto');
            $table->string('color_carrusel', 7)->default('#173D5B')->after('color_texto_secundario');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->dropColumn(['color_texto', 'color_texto_secundario', 'color_carrusel']);
        });
    }
};

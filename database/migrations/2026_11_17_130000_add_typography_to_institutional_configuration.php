<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->string('fuente', 40)->default('Inter')->after('color_carrusel');
            $table->unsignedTinyInteger('tamano_texto')->default(16)->after('fuente');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->dropColumn(['fuente', 'tamano_texto']);
        });
    }
};

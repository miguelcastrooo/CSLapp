<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['nivel_educativo', 'grados', 'secciones', 'plataformas'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->boolean('activo')->default(true)->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['nivel_educativo', 'grados', 'secciones', 'plataformas'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropIndex(['activo']);
                $blueprint->dropColumn('activo');
            });
        }
    }
};

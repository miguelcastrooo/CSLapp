<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('alumnos_archivados', 'tipo_archivo')) {
            Schema::table('alumnos_archivados', function (Blueprint $table) {
                $table->string('tipo_archivo', 20)->default('baja')->after('status')->index();
            });
        }

        if (! Schema::hasColumn('alumnos_archivados', 'fecha_egreso')) {
            Schema::table('alumnos_archivados', function (Blueprint $table) {
                $table->timestamp('fecha_egreso')->nullable()->after('fecha_archivo');
            });
        }
    }

    public function down(): void
    {
        Schema::table('alumnos_archivados', function (Blueprint $table) {
            $table->dropIndex(['tipo_archivo']);
            $table->dropColumn(['tipo_archivo', 'fecha_egreso']);
        });
    }
};

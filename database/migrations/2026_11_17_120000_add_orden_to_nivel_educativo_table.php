<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nivel_educativo', function (Blueprint $table) {
            $table->unsignedInteger('orden')->nullable()->after('nombre');
        });

        $niveles = DB::table('nivel_educativo')
            ->orderByRaw("CASE nombre WHEN 'Preescolar' THEN 1 WHEN 'Primaria Baja' THEN 2 WHEN 'Primaria Alta' THEN 3 WHEN 'Secundaria' THEN 4 ELSE 99 END")
            ->orderBy('nombre')
            ->get(['id']);

        foreach ($niveles as $indice => $nivel) {
            DB::table('nivel_educativo')->where('id', $nivel->id)->update(['orden' => $indice + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('nivel_educativo', function (Blueprint $table) {
            $table->dropColumn('orden');
        });
    }
};

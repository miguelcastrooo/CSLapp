<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store the school progression independently from database record IDs.
     */
    public function up(): void
    {
        Schema::table('grados', function (Blueprint $table) {
            $table->unsignedSmallInteger('orden_promocion')->nullable()->after('nivel_educativo_id');
        });

        DB::table('grados')->orderBy('id')->pluck('id')->each(function ($id, $index) {
            DB::table('grados')->where('id', $id)->update(['orden_promocion' => $index + 1]);
        });
    }

    public function down(): void
    {
        Schema::table('grados', function (Blueprint $table) {
            $table->dropColumn('orden_promocion');
        });
    }
};

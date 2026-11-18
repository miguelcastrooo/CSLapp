<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A bulk import creates the academic record first. Personal, health and
     * address information is completed later from the student file.
     */
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->string('lugar_nacimiento')->nullable()->change();
            $table->integer('edad_anios')->nullable()->change();
            $table->integer('edad_meses')->nullable()->change();
            $table->string('domicilio')->nullable()->change();
            $table->string('cp')->nullable()->change();
            $table->string('ciudad')->nullable()->change();
            $table->string('estado')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Existing imported records may intentionally omit these values.
    }
};

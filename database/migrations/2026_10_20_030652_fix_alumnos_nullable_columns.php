<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            // Ya no los envía el formulario
            $table->string('contacto1nombre')->nullable()->change();
            $table->string('telefono1')->nullable()->change();
            $table->string('correo_familia')->nullable()->change();

            // Opcionales en el formulario
            $table->string('cerrada')->nullable()->change();
            $table->string('colonia')->nullable()->change();
        });
    }

    public function down(): void
    {
        // No se revierte a NOT NULL para no romper datos existentes
    }
};
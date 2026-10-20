<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApellidosToHermanosTable extends Migration
{
    public function up()
    {
        Schema::table('hermanos', function (Blueprint $table) {
            // Agregar las columnas para los apellidos
            $table->string('apellido_paterno')->nullable()->after('nombre');
            $table->string('apellido_materno')->nullable()->after('apellido_paterno');
        });
    }

    public function down()
    {
        Schema::table('hermanos', function (Blueprint $table) {
            // Eliminar las columnas si la migración se revierte
            $table->dropColumn(['apellido_paterno', 'apellido_materno']);
        });
    }
}

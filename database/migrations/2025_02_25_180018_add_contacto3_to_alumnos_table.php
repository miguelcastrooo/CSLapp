<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->string('contacto3nombre')->nullable();
            $table->string('telefono3')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn(['contacto3nombre', 'telefono3']);
        });
    }
    
};

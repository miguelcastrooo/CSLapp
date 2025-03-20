<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('padres_tutores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->enum('tipo', ['padre', 'madre', 'tutor']);
            $table->string('nombre', 100);
            $table->date('fecha_nacimiento');
            $table->string('estado_civil', 50);
            $table->text('domicilio');
            $table->string('telefono_fijo', 15)->nullable();
            $table->string('telefono_celular', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('profesion', 100)->nullable();
            $table->string('ocupacion', 100)->nullable();
            $table->string('empresa_nombre', 100)->nullable();
            $table->string('empresa_telefono', 15)->nullable();
            $table->text('empresa_domicilio')->nullable();
            $table->string('empresa_ciudad', 100)->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('padres_tutores');
    }
    
};

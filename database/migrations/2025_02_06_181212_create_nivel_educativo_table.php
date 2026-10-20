<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNivelEducativoTable extends Migration
{
    public function up()
    {
        Schema::create('nivel_educativo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Preescolar, Primaria Baja, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nivel_educativo');
    }
}

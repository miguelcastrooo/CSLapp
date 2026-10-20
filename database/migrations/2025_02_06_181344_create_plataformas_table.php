<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlataformasTable extends Migration
{
    public function up()
    {
        Schema::create('plataformas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre de la plataforma (por ejemplo: "Classroom", "Moodle", etc.)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plataformas');
    }
}

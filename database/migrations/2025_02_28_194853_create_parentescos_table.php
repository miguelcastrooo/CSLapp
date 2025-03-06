<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentescosTable extends Migration
{
    public function up()
    {
        Schema::create('parentescos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // Nombre del tipo de parentesco, por ejemplo: Madre, Padre, Tutor, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parentescos');
    }
}

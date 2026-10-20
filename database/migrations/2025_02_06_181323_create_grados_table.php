<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradosTable extends Migration
{
    public function up()
    {
        Schema::create('grados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('nivel_educativo_id');
            $table->foreign('nivel_educativo_id')->references('id')->on('nivel_educativo')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('grados');
    }
}

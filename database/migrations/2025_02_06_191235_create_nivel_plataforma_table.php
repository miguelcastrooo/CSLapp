<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNivelPlataformaTable extends Migration
{
    public function up()
    {
        Schema::create('nivel_plataforma', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nivel_educativo_id'); // Clave foránea hacia 'nivel_educativo'
            $table->unsignedBigInteger('plataforma_id'); // Clave foránea hacia 'plataformas'
            $table->timestamps();

            // Asegúrate de que la referencia apunta a la tabla 'nivel_educativo' en singular
            $table->foreign('nivel_educativo_id')->references('id')->on('nivel_educativo')->onDelete('cascade');
            $table->foreign('plataforma_id')->references('id')->on('plataformas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nivel_plataforma');
    }
}

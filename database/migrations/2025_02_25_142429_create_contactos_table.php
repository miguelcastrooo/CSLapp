<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id(); // Campo de identificación único
            $table->string('nombre'); // Nombre del contacto
            $table->string('telefono')->nullable(); // Teléfono del contacto
            $table->string('correo')->nullable(); // Correo electrónico del contacto
            $table->timestamps(); // Tiempos de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contactos'); // Eliminar la tabla 'contactos'
    }
}

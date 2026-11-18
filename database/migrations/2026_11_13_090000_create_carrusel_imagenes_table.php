<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrusel_imagenes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 120);
            $table->string('descripcion', 255)->nullable();
            $table->string('imagen_path');
            $table->unsignedInteger('orden')->default(1);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('carrusel_imagenes'); }
};

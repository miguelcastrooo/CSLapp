<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('configuracion_institucional')) {
            return;
        }

        Schema::create('configuracion_institucional', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->default('Instituto Horizonte');
            $table->string('subtitulo')->nullable()->default('Portal académico');
            $table->string('logo_path')->nullable();
            $table->string('color_primario', 7)->default('#24536D');
            $table->string('color_sidebar', 7)->default('#24536D');
            $table->string('color_acento', 7)->default('#176B62');
            $table->string('color_fondo', 7)->default('#F3F6FA');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_institucional');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('nivel_educativo_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('nivel_educativo_id')->constrained('nivel_educativo')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nivel_educativo_role');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('plataformas', function (Blueprint $table) {
            $table->unsignedBigInteger('nivel_educativo_id')->nullable();
            $table->foreign('nivel_educativo_id')->references('id')->on('nivel_educativo')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('plataformas', function (Blueprint $table) {
            $table->dropForeign(['nivel_educativo_id']);
            $table->dropColumn('nivel_educativo_id');
        });
    }
    
};

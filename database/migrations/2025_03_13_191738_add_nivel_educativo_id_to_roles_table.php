<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->foreignId('nivel_educativo_id')->nullable()->constrained('nivel_educativo')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['nivel_educativo_id']);
            $table->dropColumn('nivel_educativo_id');
        });
    }
};

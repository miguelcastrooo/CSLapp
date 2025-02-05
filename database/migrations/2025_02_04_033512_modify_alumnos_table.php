<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAlumnosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos', function (Blueprint $table) {
            // Modificar el campo 'nivel_educativo' para que tenga los valores más específicos
            $table->enum('nivel_educativo', ['preescolar', 'primaria_baja', 'primaria_alta', 'secundaria'])
                  ->default('preescolar')
                  ->change();

            // Modificar el campo 'grado' para reflejar los diferentes grados
            $table->enum('grado', [
                'BabiesRoom', 'Primero de Kinder', 'Segundo de Kinder', 'Tercero de Kinder', // Preescolar
                1, 2, 3, 4, 5, 6, // Primaria baja y alta
                'Secundaria 1', 'Secundaria 2', 'Secundaria 3' // Secundaria
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumnos', function (Blueprint $table) {
            // Revertir a la versión anterior (opcional)
            $table->enum('nivel_educativo', ['Preescolar', 'Primaria', 'Secundaria'])
                  ->default('Preescolar')
                  ->change();

            $table->integer('grado')->change(); // Si el tipo de dato original era 'integer'
        });
    }
}

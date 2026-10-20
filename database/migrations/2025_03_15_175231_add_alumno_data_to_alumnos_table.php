<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAlumnoDataToAlumnosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('alumnos', function (Blueprint $table) {
        $table->string('lugar_nacimiento'); // Lugar de nacimiento
        $table->date('fecha_nacimiento')->nullable(); // Fecha de nacimiento, nullable temporalmente
        $table->integer('edad_anios'); // Edad en años (obligatorio)
        $table->integer('edad_meses'); // Edad en meses (obligatorio)
        $table->enum('sexo', ['Masculino', 'Femenino', 'Sin Definir'])->default('Sin Definir'); // Sexo
        $table->string('domicilio'); // Domicilio
        $table->string('cp'); // C.P. (obligatorio)
        $table->string('cerrada'); // Cerrada
        $table->string('colonia'); // Colonia
        $table->string('ciudad'); // Ciudad (obligatorio)
        $table->string('estado'); // Estado (obligatorio)
        $table->string('hermano_nombre')->nullable(); // Nombre del hermano
        $table->integer('hermano_edad')->nullable(); // Edad del hermano
        $table->text('enfermedades_alergias')->nullable(); // Enfermedades o alergias
        $table->string('pediatra_nombre')->nullable(); // Nombre del pediatra
        $table->string('pediatra_telefono')->nullable(); // Teléfono del pediatra
    });
}

public function down()
{
    Schema::table('alumnos', function (Blueprint $table) {
        $table->dropColumn([
            'lugar_nacimiento',
            'fecha_nacimiento',
            'edad_anios',
            'edad_meses',
            'sexo',
            'domicilio',
            'cp',
            'cerrada',
            'colonia',
            'ciudad',
            'estado',
            'hermano_nombre',
            'hermano_edad',
            'enfermedades_alergias',
            'pediatra_nombre',
            'pediatra_telefono'
        ]);
    });
}

}

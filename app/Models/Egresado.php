<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Egresado extends Model
{
    use HasFactory;

    protected $table = 'egresados'; // Nombre de la tabla en la BD

    protected $fillable = [
        'matricula',
        'nombre',
        'apellidopaterno',
        'apellidomaterno',
        'correo',
        'contacto1nombre',
        'telefono1',
        'correo_familia',
        'contacto2nombre',
        'telefono2',
        'usuario_classroom',
        'contraseña_classroom',
        'usuario_moodle',
        'contraseña_moodle',
        'usuario_mathletics',
        'contraseña_mathletics',
        'usuario_hmh',
        'contraseña_hmh',
        'usuario_progrentis',
        'contraseña_progrentis',
        'nivel_educativo_id',
        'grado_id',
        'plataforma_id',
        'seccion',
        'fecha_inscripcion',
        'motivo_baja'
    ];
}

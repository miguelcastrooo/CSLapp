<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla si es diferente a 'alumnos' (en este caso no es necesario porque Laravel usa plural por defecto)
    protected $table = 'alumnos';

    // Los atributos que son asignables de forma masiva
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
        'nivel_educativo',
        'grado',
        'seccion',
        'fecha_inscripcion',
    ];

    // Los atributos que no deben ser asignados de forma masiva
    protected $guarded = [];

    // Indicar los tipos de datos para la fecha de creación y actualización (si es necesario)
    protected $dates = [
        'fecha_inscripcion',
        'created_at',
        'updated_at',
    ];
    
    // Definir relaciones si existen (por ejemplo, si hay relación con otros modelos como 'Contacto', etc.)
}

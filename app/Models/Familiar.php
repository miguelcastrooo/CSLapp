<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Familiar extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'familiares';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'alumno_id',
        'nombre',
        'apellido_paterno', 
        'apellido_materno',
        'fecha_nacimiento',
        'estado_civil',
        'domicilio',
        'no_domicilio',
        'cp',
        'colonia',
        'ciudad',
        'estado',
        'telefono_fijo',
        'celular',
        'correo',
        'profesion',
        'ocupacion',
        'empresa_nombre',
        'empresa_telefono',
        'empresa_domicilio',
        'empresa_ciudad',
        'tipo_familiar', // Padre, Madre, Tutor, Otro
    ];

    // Relación con el modelo Alumno (un familiar pertenece a un alumno)
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }
}

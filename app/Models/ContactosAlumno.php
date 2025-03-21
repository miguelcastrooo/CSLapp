<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactosAlumnno extends Model
{
    use HasFactory;

    // Definir la tabla si el nombre no sigue la convención de Laravel
    protected $table = 'contactos_alumno';

    // Definir los campos que se pueden llenar (fillable)
    protected $fillable = [
        'alumno_id',
        'padre_nombre',
        'padre_fecha_nacimiento',
        'padre_estado_civil',
        'padre_domicilio',
        'padre_no',
        'padre_cp',
        'padre_colonia',
        'padre_ciudad',
        'padre_estado',
        'padre_telefono_fijo',
        'padre_celular',
        'padre_correo',
        'padre_profesion',
        'padre_ocupacion',
        'padre_empresa_nombre',
        'padre_empresa_telefono',
        'padre_empresa_domicilio',
        'padre_empresa_ciudad',
        'madre_nombre',
        'madre_fecha_nacimiento',
        'madre_estado_civil',
        'madre_domicilio',
        'madre_no',
        'madre_cp',
        'madre_colonia',
        'madre_ciudad',
        'madre_estado',
        'madre_telefono_fijo',
        'madre_celular',
        'madre_correo',
        'madre_profesion',
        'madre_ocupacion',
        'madre_empresa_nombre',
        'madre_empresa_telefono',
        'madre_empresa_domicilio',
        'madre_empresa_ciudad',
        'tutor_nombre',
        'tutor_fecha_nacimiento',
        'tutor_estado_civil',
        'tutor_domicilio',
        'tutor_no',
        'tutor_cp',
        'tutor_colonia',
        'tutor_ciudad',
        'tutor_estado',
        'tutor_telefono_fijo',
        'tutor_celular',
        'tutor_correo',
        'tutor_profesion',
        'tutor_ocupacion',
        'tutor_empresa_nombre',
        'tutor_empresa_telefono',
        'tutor_empresa_domicilio',
        'tutor_empresa_ciudad',
    ];

    public function alumno()
    {
        return $this->hasMany(Alumno::class);
    }
}

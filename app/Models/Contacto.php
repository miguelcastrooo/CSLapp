<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;

    // Definir los campos que son asignables masivamente
    protected $fillable = [
        'nombre',
        'telefono',
        'correo',
        'alumno_id',
        'tipo_contacto'
    ];

    /**
     * Relación de muchos a muchos con el modelo Alumno
     */
    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumno_contacto', 'contacto_id', 'alumno_id')
                    ->withTimestamps();
    }

    public function parentesco()
    {
        return $this->belongsTo(Parentesco::class, 'parentesco_id');
    }
}

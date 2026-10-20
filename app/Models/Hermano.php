<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hermano extends Model
{
    use HasFactory;

    // Definir la tabla si el nombre no sigue la convención plural
    protected $table = 'hermanos';

    // Los campos que se pueden llenar masivamente
    protected $fillable = [
        'alumno_id',
        'nombre',
        'apellido_paterno',
        'apellido_materno', 
        'edad',
    ];

    // Relación con el modelo Alumno (si un hermano pertenece a un alumno)
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}

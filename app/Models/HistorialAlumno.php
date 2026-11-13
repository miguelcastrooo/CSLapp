<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialAlumno extends Model
{
    use HasFactory;

    protected $table = 'historial_alumnos';

    protected $fillable = [
        'alumno_id',
        'alumno_nombre',
        'user_id',
        'accion',
        'motivo',
        'cambios',
        'fecha',
    ];

    protected function casts(): array
    {
        return [
            'cambios' => 'array',
            'fecha' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

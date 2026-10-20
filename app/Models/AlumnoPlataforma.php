<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumnoPlataforma extends Model
{
    use HasFactory;

    protected $table = 'alumno_plataforma';

    protected $fillable = ['alumno_id', 'plataforma_id', 'usuario', 'contraseña'];

    public $timestamps = true;

    // Relación con Alumno
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    // Relación con Plataforma
    public function plataforma()
    {
        return $this->belongsTo(Plataforma::class, 'plataforma_id');
    }
}

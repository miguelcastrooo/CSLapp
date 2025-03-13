<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plataforma extends Model
{
    use HasFactory;

    protected $table = 'plataformas'; 

    protected $fillable = ['nombre'];

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumno_plataforma', 'plataforma_id', 'alumno_id')
                    ->withPivot('usuario', 'contraseña') // Incluye credenciales
                    ->withTimestamps();
    }

    public function niveles()
    {
        return $this->belongsToMany(NivelEducativo::class, 'nivel_plataforma', 'plataforma_id', 'nivel_educativo_id')
                    ->withTimestamps();
    }
}

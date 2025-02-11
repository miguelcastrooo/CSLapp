<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plataforma extends Model
{
    use HasFactory;

    protected $table = 'plataformas'; 

    protected $fillable = ['nombre'];

    // Relación muchos a muchos con Alumno
    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumno_plataforma')
                    ->withPivot('usuario', 'contraseña')
                    ->withTimestamps();
    }

    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class);
    }
}

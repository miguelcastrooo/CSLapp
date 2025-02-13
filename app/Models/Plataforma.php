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
  // En Plataforma.php
public function alumnos()
{
    return $this->belongsToMany(Alumno::class, 'alumno_plataforma', 'plataforma_id', 'alumno_id')
                ->withPivot('nivel_educativo_id');  // Si necesitas almacenar el nivel_educativo_id en la tabla pivot
}


    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class);
    }
}

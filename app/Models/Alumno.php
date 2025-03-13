<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricula', 
        'nombre', 
        'apellidopaterno', 
        'apellidomaterno', 
        'correo', 
        'nivel_educativo_id', 
        'grado_id', 
        'seccion', 
        'fecha_inscripcion', 
        'status', 
        'fecha_inicio'
    ];

    public $timestamps = true; 

    // Relación con las plataformas a través de la tabla intermedia alumno_plataforma
    public function plataformas()
    {
        return $this->belongsToMany(Plataforma::class, 'alumno_plataforma')
                    ->withPivot('usuario', 'contraseña')
                    ->withTimestamps();
    }

    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class, 'nivel_educativo_id');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function contactos()
    {
        return $this->belongsToMany(Contacto::class, 'alumno_contacto')
                    ->withTimestamps();
    }

    
    // Relación con el modelo AlumnoPlataforma
    public function alumnoPlataforma()
    {
        return $this->hasMany(AlumnoPlataforma::class);
    }

}

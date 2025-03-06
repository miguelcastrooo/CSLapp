<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Alumno extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricula', 
        'nombre', 
        'apellidopaterno', 
        'apellidomaterno', 
        'correo', 
        'usuario_classroom', 
        'contraseña_classroom', 
        'usuario_moodle', 
        'contraseña_moodle', 
        'usuario_mathletics', 
        'contraseña_mathletics', 
        'usuario_hmh', 
        'contraseña_hmh', 
        'usuario_progrentis', 
        'contraseña_progrentis', 
        'nivel_educativo_id', 
        'grado_id', 
        'seccion', 
        'fecha_inscripcion', 
        'status', 
        'fecha_inicio'
    ];

    public $timestamps = true; 

    public function plataformas()
    {
        return $this->belongsToMany(Plataforma::class, 'alumno_plataforma', 'alumno_id', 'plataforma_id');
    }

    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class, 'nivel_educativo_id'); // Ajusta el nombre de la clave foránea
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id'); // Ajusta el nombre de la clave foránea
    }

    public function contactos()
    {
        return $this->belongsToMany(Contacto::class, 'alumno_contacto', 'alumno_id', 'contacto_id')
                    ->withTimestamps();
    }
}

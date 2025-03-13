<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelEducativo extends Model
{
    use HasFactory;

    protected $table = 'nivel_educativo';

    // Los atributos que son asignables de forma masiva
    protected $fillable = [
        'nombre', // Por ejemplo, 'Primaria Baja', 'Secundaria Alta', etc.
    ];

    // Los atributos que no deben ser asignados de forma masiva
    protected $guarded = [];

    // Relación uno a muchos (un nivel educativo puede tener muchos alumnos)
    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }

    public function plataformas()
    {
        return $this->belongsToMany(Plataforma::class, 'nivel_plataforma', 'nivel_educativo_id', 'plataforma_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

   
    // Relación uno a muchos con roles
    public function roles()
    {
        return $this->hasMany(Role::class);
    }
    
}

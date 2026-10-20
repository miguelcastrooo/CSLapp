<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    // Especificar la tabla si el nombre no sigue la convención plural de Laravel
    // protected $table = 'grupos';

    // Especificar los campos que son asignables
    protected $fillable = ['nombre', 'grado_id', 'nivel_educativo_id'];

    // Relación con el modelo Grado
    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    // Relación con el modelo Nivel
    public function nivel()
    {
        return $this->belongsTo(NivelEducativo::class);
    }
}
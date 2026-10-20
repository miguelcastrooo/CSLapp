<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumnoArchivado extends Model
{
    use HasFactory;

    // Especifica la tabla si el nombre no sigue la convención plural
    protected $table = 'alumnos_archivados';

    // Relación con NivelEducativo
    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class, 'nivel_educativo_id');
    }

    // Relación con Grado
    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }
}

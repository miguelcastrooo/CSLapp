<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole; // Extiende la clase de Spatie
use App\Models\NivelEducativo;  // Asegúrate de importar el modelo NivelEducativo

class Role extends SpatieRole
{
    use HasFactory;

    // Los atributos que son asignables de forma masiva
    protected $fillable = ['name', 'nivel_educativo_id'];

    // Relación inversa con el nivel educativo
    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class, 'nivel_educativo_id');
    }

    // Relación con usuarios (si es necesario, Spatie lo maneja automáticamente)
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

}

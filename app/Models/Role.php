<?php


namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Los atributos que son asignables de forma masiva
    protected $fillable = ['name', 'nivel_educativo_id'];

    // Relación inversa con el nivel educativo
    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class);
    }

    // Relación con usuarios (si es necesario, Spatie lo maneja automáticamente)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grado extends Model
{
    use HasFactory;

    // Si el nombre de la tabla es diferente de la pluralización de la clase, define el nombre de la tabla
    protected $table = 'grados';

    // Si deseas definir qué atributos pueden ser asignados en masa
    protected $fillable = ['name', 'nivel_educativo_id'];

    // Relación con Plataforma
    public function plataformas()
    {
        return $this->hasMany(Plataforma::class);
    }
}

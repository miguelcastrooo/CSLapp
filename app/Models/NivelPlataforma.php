<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelEducativo extends Model
{
    use HasFactory;

    protected $table = 'nivel_educativo';

    public function plataformas()
    {
        return $this->belongsToMany(Plataforma::class, 'nivel_plataforma', 'nivel_educativo_id', 'plataforma_id');
    }
}

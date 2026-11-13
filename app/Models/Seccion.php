<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'secciones';

    protected $fillable = ['nombre', 'grado_id', 'activo'];

    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnoCampoConfirmado extends Model
{
    protected $table = 'alumno_campos_confirmados';

    protected $fillable = ['alumno_id', 'campo', 'user_id', 'confirmado_en'];

    protected $casts = ['confirmado_en' => 'datetime'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumnoPlataforma extends Model
{
    use HasFactory;

    protected $table = 'alumno_plataforma';

    protected $fillable = ['alumno_id', 'plataforma_id'];
}
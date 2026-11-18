<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';

    protected $fillable = ['alumno_id', 'plataforma_id', 'periodo', 'calificacion', 'registrada_por'];

    protected function casts(): array
    {
        return ['calificacion' => 'decimal:2'];
    }

    public function alumno() { return $this->belongsTo(Alumno::class); }
    public function plataforma() { return $this->belongsTo(Plataforma::class); }
    public function registradaPor() { return $this->belongsTo(User::class, 'registrada_por'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BitacoraContactoFamiliar extends Model
{
    use HasFactory;

    protected $table = 'bitacoras_contacto_familiares';

    protected $fillable = ['alumno_id', 'user_id', 'tipo', 'resumen', 'fecha_contacto'];

    protected function casts(): array
    {
        return ['fecha_contacto' => 'datetime'];
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumnoEdicion extends Model
{
    use HasFactory;

    protected $table = 'alumno_ediciones';

    protected $fillable = ['alumno_id', 'user_id', 'iniciado_en', 'expira_en'];

    protected function casts(): array
    {
        return [
            'iniciado_en' => 'datetime',
            'expira_en' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

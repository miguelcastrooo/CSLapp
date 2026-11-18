<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/** Permite que las credenciales se descifren también al consultarlas desde PDFs. */
class AlumnoPlataformaPivot extends Pivot
{
    protected $table = 'alumno_plataforma';

    protected function casts(): array
    {
        return [
            'contraseña' => 'encrypted',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parentesco extends Model
{
    use HasFactory;

    // Definir el nombre de la tabla si es necesario
    protected $table = 'parentescos';

    // Definir los campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre', // Aquí puedes agregar otros campos si los tienes
    ];

    /**
     * Relación con el modelo AlumnoContacto
     * Un parentesco puede tener muchos contactos de alumno.
     */
    public function alumnoContactos()
    {
        return $this->hasMany(AlumnoContacto::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricula', 'nombre', 'apellidopaterno', 'apellidomaterno', 'nivel_educativo_id',
        'grado_id', 'seccion', 'fecha_inscripcion', 'fecha_inicio',
        'contacto1nombre', 'telefono1', 'correo_familia',
        'lugar_nacimiento', 'fecha_nacimiento', 'edad_anios', 'edad_meses', 'sexo',
        'domicilio', 'cp', 'cerrada', 'colonia', 'ciudad', 'estado',
        'hermano_nombre', 'hermano_edad', 'enfermedades_alergias', 'pediatra_nombre', 'pediatra_telefono','no_domicilio'
    ];

    public $timestamps = true; 

    public function plataformas()
    {
        return $this->belongsToMany(Plataforma::class, 'alumno_plataforma')
                    ->using(AlumnoPlataformaPivot::class)
                    ->withPivot('usuario', 'contraseña')
                    ->withTimestamps();
    }

    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class, 'nivel_educativo_id');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function contactos()
    {
        return $this->hasMany(Contacto::class, 'alumno_id');
    }    

    public function alumnoPlataforma()
    {
        return $this->hasMany(AlumnoPlataforma::class);
    }

    public function hermanos()
    {
        return $this->hasMany(Hermano::class);
    }

    public function familiares()
    {
        return $this->hasMany(Familiar::class);
    }

    public function confirmacionesOpcionales()
    {
        return $this->hasMany(AlumnoCampoConfirmado::class);
    }

    public function bitacorasContacto()
    {
        return $this->hasMany(BitacoraContactoFamiliar::class)->latest('fecha_contacto');
    }

    protected function casts(): array
    {
        return [
            // Campos heredados que siguen existiendo por compatibilidad.
            'contraseña_classroom' => 'encrypted',
            'contraseña_moodle' => 'encrypted',
            'contraseña_mathletics' => 'encrypted',
            'contraseña_hmh' => 'encrypted',
            'contraseña_progrentis' => 'encrypted',
        ];
    }

    /**
     * Indica los datos que aún deben revisarse antes de considerar terminado
     * el expediente. Esta regla se comparte entre el expediente y el panel.
     */
    public function estadoExpediente(): array
    {
        $this->loadMissing(['contactos', 'familiares', 'confirmacionesOpcionales']);

        $vacio = static fn ($valor): bool => $valor === null || trim((string) $valor) === '';
        $importantes = [];
        $opcionales = [];
        $confirmados = $this->confirmacionesOpcionales->pluck('campo')->all();

        foreach ([
            'Matrícula' => $this->matricula,
            'Nombre' => $this->nombre,
            'Apellido paterno' => $this->apellidopaterno,
            'Apellido materno' => $this->apellidomaterno,
            'Fecha de nacimiento' => $this->fecha_nacimiento,
            'Sexo' => $this->sexo,
            'Lugar de nacimiento' => $this->lugar_nacimiento,
            'Nivel educativo' => $this->nivel_educativo_id,
            'Grado' => $this->grado_id,
            'Sección' => $this->seccion,
            'Fecha de inscripción' => $this->fecha_inscripcion,
            'Fecha de inicio' => $this->fecha_inicio,
            'Domicilio' => $this->domicilio,
            'Código postal' => $this->cp,
            'Ciudad' => $this->ciudad,
            'Estado' => $this->estado,
        ] as $etiqueta => $valor) {
            if ($vacio($valor)) {
                $importantes[] = $etiqueta;
            }
        }

        if (! $this->contactos->contains(fn ($contacto) => ! $vacio($contacto->nombre) && ! $vacio($contacto->telefono) && ! $vacio($contacto->tipo_contacto) && ! $vacio($contacto->correo))) {
            $importantes[] = 'Contacto de emergencia completo';
        }

        if (! $this->familiares->contains(fn ($familiar) => ! $vacio($familiar->nombre) && ! $vacio($familiar->apellido_paterno) && ! $vacio($familiar->tipo_familiar) && ! $vacio($familiar->correo))) {
            $importantes[] = 'Familiar responsable completo';
        }

        if ($this->contactos->count() < 2) {
            $opcionales['contacto_alterno'] = 'Contacto alterno';
        } elseif ($this->contactos->count() < 3) {
            $opcionales['tercer_contacto'] = 'Tercer contacto de emergencia';
        }

        if (! $this->familiares->contains(fn ($familiar) => ! $vacio($familiar->telefono_fijo))) {
            $opcionales['telefono_fijo_familiar'] = 'Teléfono fijo de familiar';
        }

        if ($vacio($this->enfermedades_alergias)) {
            $opcionales['alergias_padecimientos'] = 'Alergias o padecimientos';
        }

        $totalPlataformas = array_key_exists('alumno_plataforma_count', $this->getAttributes())
            ? $this->alumno_plataforma_count
            : $this->alumnoPlataforma()->count();

        if ($totalPlataformas < 1) {
            $opcionales['plataforma_asignada'] = 'Plataforma asignada';
        }

        return [
            'importantes' => $importantes,
            'opcionales' => array_diff_key($opcionales, array_flip($confirmados)),
        ];
    }
}

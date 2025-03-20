<?php

namespace App\Mail;

use App\Models\Alumno;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlumnoRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $alumno;
    public $contactos;

    /**
     * Crea una nueva instancia de mensaje.
     *
     * @param  \App\Models\Alumno  $alumno
     * @param  array  $contactos
     * @return void
     */
    public function __construct(Alumno $alumno, $contactos, $hermanos)
    {
        $this->alumno = $alumno;
        $this->contactos = $contactos;
        $this->hermanos = $hermanos;
        

    }

    /**
     * Construir el mensaje del correo.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Alumno Registrado')
                    ->view('emails.alumno_registered')
                    ->with([
                        'alumno' => $this->alumno,
                        'contactos' => $this->contactos,  // Pasar los contactos a la vista
                        'hermanos' => $this->hermanos,    // Pasar los hermanos a la vista
                    ]);
    }
}

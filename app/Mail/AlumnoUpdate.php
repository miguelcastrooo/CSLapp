<?php

namespace App\Mail;

use App\Models\Alumno;
use App\Models\Contacto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlumnoUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $alumno;
    public $contactos;

    /**
     * Create a new message instance.
     *
     * @param Alumno $alumno
     * @param \Illuminate\Database\Eloquent\Collection $contactos
     */
    public function __construct(Alumno $alumno, $contactos)
    {
        $this->alumno = $alumno;
        $this->contactos = $contactos;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Alumno Actualizado',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.alumno_updated', // La vista donde mostrarás los datos
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NotificacionInternaEscolar extends Mailable implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $titulo,
        public readonly string $mensaje,
        public readonly array $detalles,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->titulo);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notificacion-interna-escolar');
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AvisoEscolar extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $titulo,
        private readonly string $mensaje,
        private readonly array $detalles = [],
        private readonly string $tipo = 'informacion',
        private readonly ?array $adjunto = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Keep the live notification payload identical to the one persisted in
     * the notification center. This makes the bell usable even before a
     * page refresh.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => $this->titulo,
            'mensaje' => $this->mensaje,
            'detalles' => $this->detalles,
            'tipo' => $this->tipo,
            'enlace' => route('avisos.index'),
            'adjunto' => $this->adjunto,
        ];
    }
}

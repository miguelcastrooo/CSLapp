<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Barryvdh\DomPDF\PDF;
use Symfony\Component\Mime\Email;

class EnviarPdfConMensaje extends Mailable
{
    use Queueable;

    public $mensaje;
    public $alumno;
    public $pdf;
    public $asunto;
    public $familiar;  // Variable añadida

    /**
     * Crear una nueva instancia del mensaje.
     *
     * @param  string  $mensaje
     * @param  \App\Models\Alumno  $alumno
     * @param  \Barryvdh\DomPDF\PDF  $pdf
     * @param  string  $asunto
     * @param  \App\Models\Familiar  $familiar
     * @return void
     */
    public function __construct($mensaje, $alumno, PDF $pdf, $asunto, $familiar)
    {
        $this->mensaje = $mensaje;
        $this->alumno = $alumno;
        $this->pdf = $pdf;
        $this->asunto = $asunto;
        $this->familiar = $familiar;  // Asignar $familiar
    }

    /**
     * Construir el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        $logoPath = public_path('img/csl.png'); // Ruta absoluta al logo

        return $this->view('emails.pdfConMensaje')
                    ->subject($this->asunto)
                    ->from('control.escolar@colegiosanluis.com.mx')
                    ->with([
                        'mensaje' => $this->mensaje,
                        'alumno' => $this->alumno,
                        'familiar' => $this->familiar,
                    ])
                    // Adjuntar el archivo PDF
                    ->attachData($this->pdf->output(), 'credenciales_alumno.pdf', [
                        'mime' => 'application/pdf',
                        'disposition' => 'attachment',
                    ])
                    // Incrustar el logo como CID mediante Symfony Mailer.
                    ->withSymfonyMessage(function (Email $message) use ($logoPath) {
                        $message->embedFromPath($logoPath, 'logo-csl');
                    });
    }
}


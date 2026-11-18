<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Barryvdh\DomPDF\PDF;
use App\Models\ConfiguracionInstitucional;
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
        $configuracion = ConfiguracionInstitucional::firstOrCreate(
            ['id' => 1],
            ConfiguracionInstitucional::valoresPredeterminados()
        );
        $logoPath = $configuracion->logo_path
            ? public_path('storage/' . $configuracion->logo_path)
            : public_path('img/demo-school.svg');
        $tieneLogotipo = is_file($logoPath);

        return $this->view('emails.pdfConMensaje')
                    ->subject($this->asunto)
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->with([
                        'mensaje' => $this->mensaje,
                        'alumno' => $this->alumno,
                        'familiar' => $this->familiar,
                        'configuracionInstitucional' => $configuracion,
                        'tieneLogotipo' => $tieneLogotipo,
                    ])
                    // Adjuntar el archivo PDF
                    ->attachData($this->pdf->output(), 'credenciales_alumno.pdf', [
                        'mime' => 'application/pdf',
                        'disposition' => 'attachment',
                    ])
                    // Incrustar el logo como CID mediante Symfony Mailer.
                    ->withSymfonyMessage(function (Email $message) use ($logoPath, $tieneLogotipo) {
                        if ($tieneLogotipo) {
                            $message->embedFromPath($logoPath, 'logo-institucional');
                        }
                    });
    }
}


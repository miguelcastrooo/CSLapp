<?php

namespace App\Services;

use App\Mail\NotificacionInternaEscolar;
use App\Models\Alumno;
use App\Models\User;
use App\Notifications\AvisoEscolar;
use App\Support\NivelAccess;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class NotificacionesEscolares
{
    public function notificarAlumno(Alumno $alumno, string $evento, ?string $motivo = null): void
    {
        $alumno->loadMissing(['nivelEducativo', 'grado']);

        $detalles = [
            'Alumno' => trim("{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}"),
            'Matrícula' => $alumno->matricula,
            'Nivel educativo' => $alumno->nivelEducativo?->nombre ?? 'No registrado',
            'Grado y sección' => trim(($alumno->grado?->nombre ?? 'Sin grado') . ' ' . ($alumno->seccion ?? '')),
            'Registrado por' => auth()->user()?->name ?? 'Sistema',
        ];

        if ($motivo) {
            $detalles['Motivo'] = $motivo;
        }

        $this->enviar($alumno->nivel_educativo_id, $evento, $detalles);
    }

    public function notificarGrupo(int $nivelEducativoId, string $evento, array $detalles): void
    {
        $detalles = [
            ...$detalles,
            'Registrado por' => auth()->user()?->name ?? 'Sistema',
        ];

        $this->enviar($nivelEducativoId, $evento, $detalles);
    }

    private function enviar(int $nivelEducativoId, string $evento, array $detalles): void
    {
        $destinatarios = $this->destinatariosParaNivel($nivelEducativoId);

        if ($destinatarios->isEmpty()) {
            return;
        }

        $etiquetas = [
            'alta' => ['Nuevo alumno registrado', 'Se registró un nuevo alumno en el sistema.'],
            'baja' => ['Alumno dado de baja', 'Se registró una baja de alumno.'],
            'reactivacion' => ['Alumno reactivado', 'Se reactivó un expediente de alumno.'],
            'promocion' => ['Grupo promovido', 'Se confirmó la promoción de un grupo escolar.'],
            'egreso' => ['Grupo egresado', 'Se registró el egreso de un grupo que concluyó el último grado.'],
        ];

        [$titulo, $mensaje] = $etiquetas[$evento] ?? ['Movimiento escolar registrado', 'Se realizó un movimiento escolar.'];

        try {
            $destinatarios->each->notify(new AvisoEscolar($titulo, $mensaje, $detalles, $evento));
            Mail::to($destinatarios->pluck('email')->all())->queue(new NotificacionInternaEscolar($titulo, $mensaje, $detalles));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function destinatariosParaNivel(int $nivelEducativoId): Collection
    {
        return User::query()
            ->where('activo', true)
            ->with('roles:name')
            ->get()
            ->filter(function (User $usuario) use ($nivelEducativoId): bool {
                if ($usuario->id === auth()->id()) {
                    return false;
                }

                if ($usuario->hasAnyRole(['SuperAdmin', 'ControlEscolar'])) {
                    return true;
                }

                return in_array($nivelEducativoId, NivelAccess::allowedIds($usuario), true);
            })
            ->filter(fn (User $usuario) => filter_var($usuario->email, FILTER_VALIDATE_EMAIL) !== false)
            ->unique()
            ->values();
    }
}

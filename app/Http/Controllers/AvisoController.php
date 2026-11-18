<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AvisoEscolar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvisoController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        return view('avisos.index', [
            'avisos' => $usuario->notifications()->latest()->paginate(20),
            'puedeEnviar' => $usuario->hasAnyRole(['SuperAdmin', 'ControlEscolar', 'CoordinacionPreescolar', 'CoordinacionPrimaria', 'CoordinacionSecundaria']),
            'rolesDisponibles' => $this->rolesDisponibles($usuario),
            'usuariosDisponibles' => $usuario->hasRole('SuperAdmin')
                ? User::query()->where('activo', true)->where('id', '!=', $usuario->id)->whereDoesntHave('roles', fn ($query) => $query->where('name', 'SuperAdmin'))->orderBy('name')->get(['id', 'name', 'email'])
                : collect(),
        ]);
    }

    public function marcarLeido(string $id)
    {
        $aviso = auth()->user()->notifications()->whereKey($id)->firstOrFail();
        $aviso->markAsRead();

        return back();
    }

    public function marcarTodosLeidos()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todos los avisos se marcaron como leídos.');
    }

    public function borrar(string $id)
    {
        $aviso = auth()->user()->notifications()->whereKey($id)->firstOrFail();
        $aviso->delete();

        return back()->with('success', 'El aviso se eliminó de tu historial.');
    }

    public function borrarTodos()
    {
        $cantidad = auth()->user()->notifications()->count();
        auth()->user()->notifications()->delete();

        return back()->with('success', $cantidad
            ? 'Se eliminó todo tu historial de avisos.'
            : 'No había avisos para eliminar.'
        );
    }

    public function descargarAdjunto(string $id)
    {
        $adjunto = $this->adjuntoAutorizado($id);

        return Storage::disk('local')->download($adjunto['ruta'], $adjunto['nombre'] ?? 'adjunto');
    }

    public function verAdjunto(string $id)
    {
        $adjunto = $this->adjuntoAutorizado($id);
        $tipo = $adjunto['tipo'] ?? '';

        abort_unless(str_starts_with($tipo, 'image/') || $tipo === 'application/pdf', 404);

        return response()->file(Storage::disk('local')->path($adjunto['ruta']), [
            'Content-Type' => $tipo,
            'Content-Disposition' => 'inline; filename="' . addslashes($adjunto['nombre'] ?? 'adjunto') . '"',
        ]);
    }

    public function enviar(Request $request)
    {
        $emisor = auth()->user();
        abort_unless($emisor->hasAnyRole(['SuperAdmin', 'ControlEscolar', 'CoordinacionPreescolar', 'CoordinacionPrimaria', 'CoordinacionSecundaria']), 403);

        $data = $request->validate([
            'titulo' => 'required|string|max:120',
            'mensaje' => 'required|string|max:1000',
            'tipo_destino' => 'required|in:rol,usuario',
            'roles' => 'nullable|array',
            'roles.*' => 'required|string|in:SuperAdmin,ControlEscolar,CoordinacionPreescolar,CoordinacionPrimaria,CoordinacionSecundaria',
            'usuario_id' => 'required_if:tipo_destino,usuario|nullable|integer|exists:users,id',
            'archivo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:10240',
        ]);

        $adjunto = $this->guardarAdjunto($request);

        $esCoordinacion = ! $emisor->hasAnyRole(['SuperAdmin', 'ControlEscolar']);

        if ($esCoordinacion) {
            abort_unless($data['tipo_destino'] === 'rol', 403);
            $roles = ['SuperAdmin', 'ControlEscolar'];
            $destinatarios = User::query()
                ->where('activo', true)
                ->where('id', '!=', $emisor->id)
                ->role($roles)
                ->get();
        } elseif ($data['tipo_destino'] === 'usuario') {
            abort_unless($emisor->hasRole('SuperAdmin'), 403);
            $request->validate(['usuario_id' => 'required|integer|exists:users,id']);
            $destinatarios = User::query()
                ->where('activo', true)
                ->where('id', '!=', $emisor->id)
                ->whereDoesntHave('roles', fn ($query) => $query->where('name', 'SuperAdmin'))
                ->whereKey($data['usuario_id'])
                ->get();
        } else {
            $roles = $data['roles'] ?? [];
            $request->validate(['roles' => 'required|array|min:1']);
            $rolesPermitidos = $this->rolesDisponibles($emisor);
            abort_unless(collect($roles)->every(fn (string $rol) => array_key_exists($rol, $rolesPermitidos)), 403);

            $destinatarios = User::query()
                ->where('activo', true)
                ->where('id', '!=', $emisor->id)
                ->role($roles)
                ->get();
        }

        Notification::send($destinatarios, new AvisoEscolar(
            $data['titulo'],
            $data['mensaje'],
            ['Enviado por' => $emisor->name, 'remitente_id' => $emisor->id],
            'aviso',
            $adjunto,
        ));

        return back()->with('success', $destinatarios->isEmpty()
            ? 'No había cuentas activas con los roles seleccionados.'
            : "Aviso enviado a {$destinatarios->count()} cuenta(s) activa(s)."
        );
    }

    public function responder(Request $request, string $id)
    {
        $respondedor = auth()->user();
        abort_unless($respondedor->hasAnyRole(['SuperAdmin', 'ControlEscolar']), 403);

        $aviso = $respondedor->notifications()->whereKey($id)->firstOrFail();
        $data = $request->validate([
            'mensaje' => 'required|string|max:1000',
            'archivo' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:10240',
        ]);
        $adjunto = $this->guardarAdjunto($request);
        $remitenteId = $aviso->data['detalles']['remitente_id'] ?? null;

        $destinatario = User::query()
            ->where('activo', true)
            ->where('id', '!=', $respondedor->id)
            ->find($remitenteId);

        if (! $destinatario) {
            return back()->with('error', 'No es posible responder este aviso porque la cuenta que lo envió ya no está disponible.');
        }

        $destinatario->notify(new AvisoEscolar(
            'Respuesta: ' . str($aviso->data['titulo'] ?? 'Aviso interno')->limit(90),
            $data['mensaje'],
            [
                'Enviado por' => $respondedor->name,
                'remitente_id' => $respondedor->id,
                'En respuesta a' => $aviso->data['titulo'] ?? 'Aviso interno',
            ],
            'aviso',
            $adjunto,
        ));

        $aviso->markAsRead();

        return back()->with('success', "Respuesta enviada a {$destinatario->name}.");
    }

    private function rolesDisponibles(User $usuario): array
    {
        $roles = [
            'ControlEscolar' => 'Control Escolar',
            'CoordinacionPreescolar' => 'Coordinación Preescolar',
            'CoordinacionPrimaria' => 'Coordinación Primaria',
            'CoordinacionSecundaria' => 'Coordinación Secundaria',
        ];

        if ($usuario->hasRole('SuperAdmin')) {
            return $roles;
        }

        if ($usuario->hasRole('ControlEscolar')) {
            return ['SuperAdmin' => 'Admin', ...$roles];
        }

        return [];
    }

    /** Guarda los adjuntos fuera de la carpeta pública para que sólo se accedan desde el aviso autorizado. */
    private function guardarAdjunto(Request $request): ?array
    {
        if (! $request->hasFile('archivo')) {
            return null;
        }

        $archivo = $request->file('archivo');
        $nombre = Str::limit(basename($archivo->getClientOriginalName()), 150, '');
        $ruta = $archivo->store('avisos/' . now()->format('Y/m'), 'local');

        return [
            'nombre' => $nombre ?: 'adjunto',
            'ruta' => $ruta,
            'tipo' => $archivo->getMimeType(),
            'tamano' => $archivo->getSize(),
        ];
    }

    /** Obtiene un adjunto únicamente desde una notificación que pertenece al usuario actual. */
    private function adjuntoAutorizado(string $id): array
    {
        $aviso = auth()->user()->notifications()->whereKey($id)->firstOrFail();
        $adjunto = $aviso->data['adjunto'] ?? null;
        $ruta = $adjunto['ruta'] ?? null;

        abort_unless(is_array($adjunto) && is_string($ruta) && str_starts_with($ruta, 'avisos/') && Storage::disk('local')->exists($ruta), 404);

        return $adjunto;
    }
}

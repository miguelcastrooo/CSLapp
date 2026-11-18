@extends('layouts.app')

@section('title', 'Avisos | ' . $configuracionInstitucional->nombre)

@section('content')
<div class="container-xl py-4">
    <div class="csl-module-header d-flex flex-column flex-md-row justify-content-between gap-3">
        <div><h1>Avisos</h1><p>Consulta movimientos escolares y comunicados internos del equipo.</p></div>
        <div class="d-flex flex-wrap gap-2">
            @if($avisos->contains(fn ($aviso) => $aviso->unread()))
                <form method="POST" action="{{ route('avisos.leer-todos') }}">@csrf<button class="btn btn-outline-primary"><i class="fas fa-check-double me-2"></i>Marcar todo como leído</button></form>
            @endif
        </div>
    </div>

    @if($puedeEnviar)
        <div class="card csl-data-card mb-4">
            <div class="card-header"><div><h2 class="card-title mb-1"><i class="fas fa-bullhorn me-2"></i>{{ auth()->user()->hasRole('SuperAdmin') ? 'Enviar aviso interno' : 'Enviar consulta a administración' }}</h2><p class="text-secondary small mb-0">{{ auth()->user()->hasRole('SuperAdmin') ? 'Elige un rol o una persona específica; el aviso aparecerá en su campana.' : (auth()->user()->hasRole('ControlEscolar') ? 'Elige los roles que deben recibir el aviso.' : 'Tu consulta llegará automáticamente a Admin y Control Escolar.') }}</p></div></div>
            <div class="card-body">
                <form method="POST" action="{{ route('avisos.enviar') }}" class="row g-3" enctype="multipart/form-data">
                    @csrf
                    <div class="col-md-5"><label class="form-label" for="aviso-titulo">Asunto</label><input id="aviso-titulo" name="titulo" class="form-control" maxlength="120" required placeholder="Ej. Reunión de seguimiento"></div>
                    <div class="col-md-7"><label class="form-label" for="aviso-mensaje">Mensaje</label><textarea id="aviso-mensaje" name="mensaje" class="form-control" rows="2" maxlength="1000" required placeholder="Escribe la información que debe conocer el equipo."></textarea></div>
                    <div class="col-12">
                        @if(auth()->user()->hasAnyRole(['SuperAdmin', 'ControlEscolar']))<label class="form-label d-block">Enviar a</label>@endif
                        @if(auth()->user()->hasRole('SuperAdmin'))
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <label class="form-check"><input class="form-check-input aviso-destino" type="radio" name="tipo_destino" value="rol" @checked(old('tipo_destino', 'rol') === 'rol')><span class="form-check-label">Uno o varios roles</span></label>
                                <label class="form-check"><input class="form-check-input aviso-destino" type="radio" name="tipo_destino" value="usuario" @checked(old('tipo_destino') === 'usuario')><span class="form-check-label">Una persona específica</span></label>
                            </div>
                        @else
                            <input type="hidden" name="tipo_destino" value="rol">
                        @endif
                        @if(auth()->user()->hasAnyRole(['SuperAdmin', 'ControlEscolar']))
                            <div id="aviso-roles"><div class="d-flex flex-wrap gap-3">@foreach($rolesDisponibles as $rol => $etiqueta)<label class="form-check"><input class="form-check-input" type="checkbox" name="roles[]" value="{{ $rol }}" @checked(in_array($rol, old('roles', [])))><span class="form-check-label">{{ $etiqueta }}</span></label>@endforeach</div></div>
                        @else
                            <div class="alert alert-info mb-0"><i class="fas fa-circle-info me-2"></i>Esta consulta se enviará a <strong>Admin</strong> y <strong>Control Escolar</strong>.</div>
                        @endif
                        @if(auth()->user()->hasRole('SuperAdmin'))
                            <div id="aviso-usuario" class="d-none"><label class="form-label" for="aviso-usuario-id">Persona</label><select id="aviso-usuario-id" name="usuario_id" class="form-select"><option value="">Selecciona una cuenta activa</option>@foreach($usuariosDisponibles as $usuario)<option value="{{ $usuario->id }}" @selected((string) old('usuario_id') === (string) $usuario->id)>{{ $usuario->name }} — {{ $usuario->email }}</option>@endforeach</select><div id="aviso-destino-error" class="invalid-feedback d-none">Selecciona una persona antes de enviar el aviso.</div></div>
                        @endif
                        <div id="aviso-roles-error" class="text-danger small mt-2 d-none">Selecciona al menos un rol antes de enviar el aviso.</div>
                        @error('roles')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        @error('usuario_id')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12"><label for="aviso-archivo" class="form-label"><i class="fas fa-paperclip me-1"></i>Adjunto <span class="text-secondary">(opcional)</span></label><input id="aviso-archivo" type="file" name="archivo" class="form-control csl-adjunto-input" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" data-preview="aviso-adjunto-preview"><div id="aviso-adjunto-preview" class="csl-adjunto-preview d-none mt-2"></div><div class="form-text">Puedes adjuntar una imagen, PDF o Word de hasta 10 MB.</div>@error('archivo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
                    <div class="col-12"><button class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Enviar aviso</button></div>
                </form>
            </div>
        </div>
    @endif

    <div class="card csl-data-card">
        <div class="card-header d-flex justify-content-between align-items-center gap-3 flex-wrap"><h2 class="card-title mb-0">Mi historial de avisos</h2>@if($avisos->isNotEmpty())<form method="POST" action="{{ route('avisos.borrar-todos') }}" data-confirm="¿Deseas eliminar todos los avisos de tu historial? Esta acción no afecta los historiales de cambios ni de seguridad." data-confirm-title="Borrar historial de avisos" data-confirm-button="Sí, borrar todos">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash-can me-2"></i>Borrar todos</button></form>@endif</div>
        <div class="list-group list-group-flush">
            @forelse($avisos as $aviso)
                <div class="list-group-item {{ $aviso->unread() ? 'bg-primary-lt' : '' }}">
                    <div class="d-flex gap-3 align-items-start"><span class="csl-management-icon"><i class="fas {{ ($aviso->data['tipo'] ?? '') === 'aviso' ? 'fa-bullhorn' : 'fa-bell' }}"></i></span><div class="flex-fill"><div class="d-flex justify-content-between gap-3"><strong>{{ $aviso->data['titulo'] ?? 'Aviso del sistema' }}</strong><small class="text-secondary text-nowrap">{{ $aviso->created_at->diffForHumans() }}</small></div><p class="mb-2 mt-1">{{ $aviso->data['mensaje'] ?? '' }}</p>@if(!empty($aviso->data['adjunto']))@php($tipoAdjunto = $aviso->data['adjunto']['tipo'] ?? '')<div class="csl-adjunto-recibido mb-2">@if(str_starts_with($tipoAdjunto, 'image/'))<a href="{{ route('avisos.adjunto.vista-previa', $aviso->id) }}" target="_blank" rel="noopener"><img src="{{ route('avisos.adjunto.vista-previa', $aviso->id) }}" alt="Vista previa del adjunto" class="img-fluid rounded border mb-2" style="max-height: 220px;"></a>@elseif($tipoAdjunto === 'application/pdf')<a class="btn btn-sm btn-outline-primary mb-2" href="{{ route('avisos.adjunto.vista-previa', $aviso->id) }}" target="_blank" rel="noopener"><i class="fas fa-file-pdf me-1"></i>Ver PDF</a>@endif <a class="btn btn-sm btn-outline-secondary mb-2" href="{{ route('avisos.adjunto', $aviso->id) }}"><i class="fas fa-download me-1"></i>Descargar: {{ $aviso->data['adjunto']['nombre'] ?? 'archivo adjunto' }}</a></div>@endif @if(!empty($aviso->data['detalles']))<div class="small text-secondary">@foreach($aviso->data['detalles'] as $etiqueta => $valor)@continue($etiqueta === 'remitente_id')<span class="me-3"><strong>{{ $etiqueta }}:</strong> {{ $valor }}</span>@endforeach</div>@endif
                        @if(auth()->user()->hasAnyRole(['SuperAdmin', 'ControlEscolar']) && !empty($aviso->data['detalles']['remitente_id']))
                            <details class="mt-3 csl-reply-box"><summary class="btn btn-sm btn-outline-primary"><i class="fas fa-reply me-1"></i>Responder</summary><form method="POST" action="{{ route('avisos.responder', $aviso->id) }}" class="pt-2" enctype="multipart/form-data"><label class="form-label small" for="respuesta-{{ $aviso->id }}">Respuesta para quien envió el aviso</label><textarea id="respuesta-{{ $aviso->id }}" name="mensaje" class="form-control mb-2" rows="2" maxlength="1000" required placeholder="Escribe una respuesta clara y breve."></textarea><label class="form-label small" for="respuesta-archivo-{{ $aviso->id }}">Adjunto opcional</label><div class="d-flex gap-2"><input id="respuesta-archivo-{{ $aviso->id }}" type="file" name="archivo" class="form-control csl-adjunto-input" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" data-preview="respuesta-adjunto-preview-{{ $aviso->id }}"><button class="btn btn-primary align-self-end"><i class="fas fa-paper-plane"></i><span class="visually-hidden">Enviar respuesta</span></button></div><div id="respuesta-adjunto-preview-{{ $aviso->id }}" class="csl-adjunto-preview d-none mt-2"></div><small class="text-secondary">Imagen, PDF o Word; máximo 10 MB.</small>@csrf</form></details>
                        @endif
                    </div><div class="d-flex flex-column gap-2 align-items-end">@if($aviso->unread())<form method="POST" action="{{ route('avisos.leer', $aviso->id) }}">@csrf<button class="btn btn-sm btn-outline-primary">Leído</button></form>@endif<form method="POST" action="{{ route('avisos.borrar', $aviso->id) }}" data-confirm="¿Eliminar este aviso de tu historial?" data-confirm-title="Eliminar aviso" data-confirm-button="Sí, eliminar">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" aria-label="Eliminar aviso"><i class="fas fa-trash-can me-1"></i>Eliminar</button></form></div></div>
                </div>
            @empty
                <div class="list-group-item"><x-empty-state icon="fa-bell-slash" title="Aún no tienes avisos" description="Los comunicados y movimientos que te correspondan aparecerán aquí." /></div>
            @endforelse
        </div>
        @if($avisos->hasPages())<div class="card-footer">{{ $avisos->links() }}</div>@endif
    </div>
</div>
@endsection

@section('scripts')
<script>
const avisoForm = document.querySelector('form[action="{{ route('avisos.enviar') }}"]');
const actualizarDestino = () => {
    const porUsuario = document.querySelector('.aviso-destino:checked')?.value === 'usuario';
    document.getElementById('aviso-roles')?.classList.toggle('d-none', porUsuario);
    document.getElementById('aviso-usuario')?.classList.toggle('d-none', !porUsuario);
    document.getElementById('aviso-usuario-id')?.toggleAttribute('required', porUsuario);
};
document.querySelectorAll('.aviso-destino').forEach((input) => input.addEventListener('change', actualizarDestino));
actualizarDestino();

avisoForm?.addEventListener('submit', (event) => {
    const porUsuario = document.querySelector('.aviso-destino:checked')?.value === 'usuario';
    const errorRoles = document.getElementById('aviso-roles-error');
    const roles = [...document.querySelectorAll('#aviso-roles input[type="checkbox"]')];

    errorRoles?.classList.add('d-none');
    if (!porUsuario && roles.length && !roles.some((rol) => rol.checked)) {
        event.preventDefault();
        errorRoles?.classList.remove('d-none');
        roles[0].focus();
    }
});

document.querySelectorAll('.csl-adjunto-input').forEach((input) => input.addEventListener('change', () => {
    const preview = document.getElementById(input.dataset.preview);
    const archivo = input.files?.[0];
    if (!preview) return;
    preview.replaceChildren();
    preview.classList.toggle('d-none', !archivo);
    if (!archivo) return;

    const texto = document.createElement('div');
    texto.className = 'small text-secondary mb-2';
    texto.textContent = `Archivo seleccionado: ${archivo.name}`;
    preview.appendChild(texto);
    if (archivo.type.startsWith('image/')) {
        const imagen = document.createElement('img');
        imagen.className = 'img-fluid rounded border';
        imagen.style.maxHeight = '180px';
        imagen.alt = 'Vista previa del archivo seleccionado';
        imagen.src = URL.createObjectURL(archivo);
        preview.appendChild(imagen);
    } else if (archivo.type === 'application/pdf') {
        const nota = document.createElement('div');
        nota.className = 'alert alert-info py-2 mb-0';
        nota.innerHTML = '<i class="fas fa-file-pdf me-1"></i>PDF listo para enviar. Podrás abrirlo desde el aviso.';
        preview.appendChild(nota);
    }
}));
</script>
@endsection

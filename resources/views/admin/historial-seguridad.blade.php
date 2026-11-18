@extends('layouts.app')

@section('title', 'Historial de seguridad | Instituto Horizonte')

@section('content')
<div class="csl-module-header">
    <h1>Historial de seguridad</h1>
    <p>Consulta accesos y cambios administrativos relevantes. Las contraseñas nunca se guardan aquí.</p>
</div>
<x-task-guide title="Cómo interpretar los eventos" description="Aquí se registran accesos y acciones administrativas relevantes." :steps="['Revisa primero la fecha y el responsable.', 'Consulta la descripción del evento.', 'Reporta cualquier actividad que no reconozcas.']" />

<div class="card csl-data-card">
    <div class="card-header">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label small" for="seguridad-buscar">Buscar</label><input id="seguridad-buscar" name="buscar" class="form-control" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Evento, detalle o dirección"></div>
            <div class="col-md-2"><label class="form-label small" for="seguridad-evento">Evento</label><select id="seguridad-evento" name="evento" class="form-select"><option value="">Todos</option>@foreach($tiposEvento as $tipoEvento)<option value="{{ $tipoEvento }}" @selected(($filtros['evento'] ?? '') === $tipoEvento)>{{ str_replace('_', ' ', ucfirst($tipoEvento)) }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label small" for="seguridad-usuario">Responsable</label><select id="seguridad-usuario" name="usuario_id" class="form-select"><option value="">Todos</option>@foreach($usuariosHistorial as $usuario)<option value="{{ $usuario->id }}" @selected((string) ($filtros['usuario_id'] ?? '') === (string) $usuario->id)>{{ $usuario->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label small" for="seguridad-desde">Desde</label><input id="seguridad-desde" type="date" name="desde" class="form-control" value="{{ $filtros['desde'] ?? '' }}"></div>
            <div class="col-md-2"><label class="form-label small" for="seguridad-hasta">Hasta</label><input id="seguridad-hasta" type="date" name="hasta" class="form-control" value="{{ $filtros['hasta'] ?? '' }}"></div>
            <div class="col-md-1 d-flex gap-1"><button type="submit" class="btn btn-primary text-nowrap" aria-label="Aplicar filtros"><i class="fas fa-filter me-1"></i>Filtrar</button><a href="{{ route('admin.historialSeguridad') }}" class="btn btn-outline-secondary" aria-label="Limpiar filtros"><i class="fas fa-rotate-left"></i></a></div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover">
            <thead><tr><th>Fecha</th><th>Evento</th><th>Responsable</th><th>Descripción</th><th>Origen</th></tr></thead>
            <tbody>
                @forelse($eventos as $evento)
                    <tr>
                        <td class="text-nowrap">{{ $evento->fecha->format('d/m/Y H:i') }}</td>
                        <td><span class="badge {{ str_contains($evento->evento, 'fallido') ? 'bg-red-lt' : 'bg-azure-lt' }}">{{ str_replace('_', ' ', ucfirst($evento->evento)) }}</span></td>
                        <td>{{ $evento->usuario?->name ?? 'No identificado' }}</td>
                        <td>{{ $evento->descripcion }}</td>
                        <td class="small text-secondary">{{ $evento->ip_address ?? 'No disponible' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">Aún no hay eventos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($eventos->hasPages())<div class="card-footer">{{ $eventos->links() }}</div>@endif
</div>
@endsection

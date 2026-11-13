@extends('layouts.app')

@section('title', 'Historial de seguridad | Instituto Horizonte')

@section('content')
<div class="csl-module-header">
    <h1>Historial de seguridad</h1>
    <p>Consulta accesos y cambios administrativos relevantes. Las contraseñas nunca se guardan aquí.</p>
</div>
<x-task-guide title="Cómo interpretar los eventos" description="Aquí se registran accesos y acciones administrativas relevantes." :steps="['Revisa primero la fecha y el responsable.', 'Consulta la descripción del evento.', 'Reporta cualquier actividad que no reconozcas.']" />

<div class="card csl-data-card">
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

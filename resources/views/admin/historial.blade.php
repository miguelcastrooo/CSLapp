@extends('layouts.app')

@section('title', 'Historial de cambios | Instituto Horizonte')

@section('content')
<div class="csl-module-header">
    <h1>Historial de cambios</h1>
    <p>Registro de bajas, reactivaciones y actualizaciones realizadas en los expedientes.</p>
</div>
<x-task-guide title="Cómo usar este historial" description="Este registro es de consulta: muestra quién realizó cada cambio y cuándo." :steps="['Ubica al alumno o la fecha.', 'Abre “Ver cambios” para conocer el detalle.', 'No se puede modificar el historial desde esta pantalla.']" />

<div class="card csl-data-card">
    <div class="card-header"><form method="GET" class="row g-2 align-items-end"><div class="col-md-3"><label class="form-label small" for="historial-buscar">Alumno</label><input id="historial-buscar" name="buscar" class="form-control" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Nombre del alumno"></div><div class="col-md-2"><label class="form-label small" for="historial-accion">Acción</label><select id="historial-accion" name="accion" class="form-select"><option value="">Todas</option>@foreach($acciones as $accion)<option value="{{ $accion }}" @selected(($filtros['accion'] ?? '') === $accion)>{{ str_replace('_', ' ', ucfirst($accion)) }}</option>@endforeach</select></div><div class="col-md-2"><label class="form-label small" for="historial-usuario">Responsable</label><select id="historial-usuario" name="usuario_id" class="form-select"><option value="">Todos</option>@foreach($usuariosHistorial as $usuario)<option value="{{ $usuario->id }}" @selected((string) ($filtros['usuario_id'] ?? '') === (string) $usuario->id)>{{ $usuario->name }}</option>@endforeach</select></div><div class="col-md-2"><label class="form-label small" for="historial-desde">Desde</label><input id="historial-desde" type="date" name="desde" class="form-control" value="{{ $filtros['desde'] ?? '' }}"></div><div class="col-md-2"><label class="form-label small" for="historial-hasta">Hasta</label><input id="historial-hasta" type="date" name="hasta" class="form-control" value="{{ $filtros['hasta'] ?? '' }}"></div><div class="col-md-1 d-flex gap-1"><button type="submit" class="btn btn-primary text-nowrap" aria-label="Aplicar filtros"><i class="fas fa-filter me-1"></i>Filtrar</button><a href="{{ route('admin.historial') }}" class="btn btn-outline-secondary" aria-label="Limpiar filtros"><i class="fas fa-rotate-left"></i></a></div></form></div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover">
            <thead><tr><th>Fecha</th><th>Alumno</th><th>Acción</th><th>Responsable</th><th>Detalle</th></tr></thead>
            <tbody>
                @forelse($historial as $evento)
                    @php
                        $etiquetas = ['matricula' => 'Matrícula', 'nombre' => 'Nombre', 'apellidopaterno' => 'Apellido paterno', 'apellidomaterno' => 'Apellido materno', 'nivel_educativo_id' => 'Nivel educativo', 'grado_id' => 'Grado', 'seccion' => 'Sección', 'fecha_inscripcion' => 'Fecha de inscripción', 'fecha_inicio' => 'Fecha de inicio', 'domicilio' => 'Domicilio', 'ciudad' => 'Ciudad', 'estado' => 'Estado', 'telefono' => 'Teléfono', 'contactos' => 'Contactos', 'familiares' => 'Familiares', 'hermanos' => 'Hermanos', 'plataformas' => 'Plataformas'];
                    @endphp
                    <tr>
                        <td class="text-nowrap">{{ $evento->fecha->format('d/m/Y H:i') }}</td>
                        <td>{{ $evento->alumno_nombre ?? '#' . $evento->alumno_id }}</td>
                        <td><span class="badge {{ $evento->accion === 'baja' ? 'bg-red-lt' : ($evento->accion === 'reactivacion' ? 'bg-green-lt' : 'bg-azure-lt') }}">{{ str_replace('_', ' ', ucfirst($evento->accion)) }}</span></td>
                        <td>{{ $evento->usuario?->name ?? 'Sin registro anterior' }}</td>
                        <td>
                            @if($evento->motivo)
                                <strong>Motivo:</strong> {{ $evento->motivo }}
                            @elseif($evento->cambios)
                                <details>
                                    <summary>Ver cambios ({{ count($evento->cambios) }})</summary>
                                    <ul class="mb-0 mt-2 ps-3">
                                        @foreach($evento->cambios as $campo => $valores)
                                            <li>
                                                <strong>{{ $etiquetas[$campo] ?? str_replace('_', ' ', ucfirst($campo)) }}:</strong>
                                                @if(is_array($valores['antes'] ?? null) || is_array($valores['despues'] ?? null))
                                                    {{ count($valores['antes'] ?? []) }} registro(s) → {{ count($valores['despues'] ?? []) }} registro(s)
                                                    <details class="mt-1">
                                                        <summary>Ver detalle</summary>
                                                        <div class="small text-secondary mt-1"><strong>Antes:</strong></div>
                                                        <ul class="small mb-1">@forelse($valores['antes'] ?? [] as $registro)<li>{{ collect($registro)->except('id')->filter()->implode(' · ') }}</li>@empty<li>Sin registros</li>@endforelse</ul>
                                                        <div class="small text-secondary"><strong>Después:</strong></div>
                                                        <ul class="small mb-0">@forelse($valores['despues'] ?? [] as $registro)<li>{{ collect($registro)->except('id')->filter()->implode(' · ') }}</li>@empty<li>Sin registros</li>@endforelse</ul>
                                                    </details>
                                                @else
                                                    “{{ $valores['antes'] ?? '—' }}” → “{{ $valores['despues'] ?? '—' }}”
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            @else
                                <span class="text-secondary">Sin detalle adicional</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">Aún no hay cambios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($historial->hasPages())<div class="card-footer">{{ $historial->links() }}</div>@endif
</div>
@endsection

@extends('layouts.app')

@section('title', 'Cierre escolar | Instituto Horizonte')

@section('content')
<div class="container-xl py-4">
    <div class="csl-module-header"><h1>Cierre escolar</h1><p>Revisa los grupos antes de realizar promociones o registrar egresos.</p></div>
    <x-task-guide title="Trabaja con calma" description="Este panel no realiza cambios por sí solo. Primero verifica los grupos y después abre la herramienta de promoción." :steps="['Revisa cantidad, grado y sección.', 'Confirma que el grupo esté completo.', 'Promueve un grupo a la vez y revisa el resultado.']" />

    <div class="card csl-data-card">
        <div class="card-header d-flex justify-content-between align-items-center gap-3 flex-wrap"><div><h2 class="card-title mb-1">Grupos activos</h2><span class="text-secondary">Los grupos sin siguiente grado se registrarán como egresados al confirmar el movimiento completo.</span></div><a href="{{ route('admin.grupos.form') }}" class="btn btn-primary"><i class="fas fa-people-arrows me-2"></i>Ir a promover grupos</a></div>
        <div class="table-responsive"><table class="table table-vcenter table-hover mb-0"><thead><tr><th>Nivel</th><th>Grupo</th><th>Alumnos</th><th>Siguiente paso</th></tr></thead><tbody>
            @forelse($grupos as $grupo)
                <tr><td>{{ $grupo->nivelEducativo?->nombre ?? 'Sin nivel' }}</td><td class="fw-semibold">{{ $grupo->grado?->nombre ?? 'Sin grado' }} · Sección {{ $grupo->seccion ?? 'Sin sección' }}</td><td><span class="badge bg-azure-lt">{{ $grupo->total_alumnos }}</span></td><td>@if($grupo->grado_destino)<span class="text-success"><i class="fas fa-arrow-right me-1"></i>Promover a {{ $grupo->grado_destino->nombre }}</span>@else<span class="text-primary"><i class="fas fa-graduation-cap me-1"></i>Preparar egreso</span>@endif</td></tr>
            @empty
                <tr><td colspan="4" class="text-center text-secondary py-5">No hay grupos activos para revisar.</td></tr>
            @endforelse
        </tbody></table></div>
    </div>
</div>
@endsection

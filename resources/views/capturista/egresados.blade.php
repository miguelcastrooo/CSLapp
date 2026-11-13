@extends('layouts.app')

@section('title', 'Egresados | Instituto Horizonte')

@section('content')
<div class="container-xl py-4">
    <div class="csl-module-header"><h1>Egresados</h1><p>Expedientes históricos de alumnos que concluyeron 3.º de secundaria.</p></div>
    <x-task-guide title="Consulta de egresados" description="Estos alumnos ya no aparecen en listados activos ni se consideran bajas." :steps="['Localiza al alumno por nombre o matrícula.', 'Consulta su último grado y fecha de egreso.', 'El expediente se conserva solo para consulta histórica.']" />
    <div class="card csl-data-card"><div class="table-responsive"><table class="table table-vcenter table-hover mb-0"><thead><tr><th>Alumno</th><th>Matrícula</th><th>Último grado</th><th>Sección</th><th>Fecha de egreso</th></tr></thead><tbody>
    @forelse($egresados as $alumno)<tr><td class="fw-semibold">{{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</td><td>{{ $alumno->matricula }}</td><td>{{ $alumno->grado?->nombre ?? 'Sin grado' }}</td><td>{{ $alumno->seccion ?? 'Sin sección' }}</td><td>{{ $alumno->fecha_egreso?->format('d/m/Y') ?? 'Sin fecha' }}</td></tr>
    @empty<tr><td colspan="5" class="text-center text-secondary py-5">Aún no hay egresados registrados.</td></tr>@endforelse
    </tbody></table></div></div>
</div>
@endsection

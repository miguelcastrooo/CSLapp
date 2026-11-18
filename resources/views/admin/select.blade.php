@extends('layouts.app')

@section('title', 'Plataformas por nivel | Instituto Horizonte')

@section('content')
<div class="csl-module-header">
    <h1>Plataformas por nivel</h1>
    <p>Selecciona un nivel para consultar los accesos de sus alumnos.</p>
</div>

<div class="csl-level-selector">
    @forelse($niveles as $nivel)
        @php
            $iconos = ['Preescolar' => 'fa-child-reaching', 'Primaria Baja' => 'fa-book-open', 'Primaria Alta' => 'fa-graduation-cap', 'Secundaria' => 'fa-school'];
            $icono = $iconos[$nivel->nombre] ?? 'fa-layer-group';
        @endphp
        <a href="{{ route('niveles.show', ['nivelId' => $nivel->id]) }}" class="card csl-choice-card csl-level-selector-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="csl-choice-icon"><i class="fas {{ $icono }}"></i></span>
                    <div><h2 class="h3 mb-1">{{ $nivel->nombre }}</h2><p class="text-secondary mb-0 small">Ver plataformas y alumnos</p></div>
                </div>
        </a>
    @empty
        <x-empty-state icon="fa-layer-group" title="No hay niveles asignados" description="Cuando Administración asigne un nivel a tu cuenta, podrás consultar sus plataformas desde aquí." />
    @endforelse
</div>
@endsection

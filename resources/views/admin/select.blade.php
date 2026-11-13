@extends('layouts.app')

@section('title', 'Plataformas por nivel | Instituto Horizonte')

@section('content')
<div class="csl-module-header">
    <h1>Plataformas por nivel</h1>
    <p>Selecciona un nivel para consultar los accesos de sus alumnos.</p>
</div>

<div class="row row-cards">
    @forelse($niveles as $nivel)
        @php
            $iconos = ['Preescolar' => 'fa-child-reaching', 'Primaria Baja' => 'fa-book-open', 'Primaria Alta' => 'fa-graduation-cap', 'Secundaria' => 'fa-school'];
            $icono = $iconos[$nivel->nombre] ?? 'fa-layer-group';
        @endphp
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('niveles.show', ['nivelId' => $nivel->id]) }}" class="card csl-choice-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="csl-choice-icon"><i class="fas {{ $icono }}"></i></span>
                    <div><h2 class="h3 mb-1">{{ $nivel->nombre }}</h2><p class="text-secondary mb-0 small">Ver plataformas y alumnos</p></div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-info mb-0">No hay niveles asignados a tu cuenta.</div></div>
    @endforelse
</div>
@endsection

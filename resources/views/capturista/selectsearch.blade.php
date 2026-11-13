@extends('layouts.app')

@section('title', 'Alumnos | Instituto Horizonte')

@section('content')
    <div class="csl-module-header"><h1>Consulta de alumnos</h1><p>Selecciona un nivel o busca por nombre y matrícula.</p></div>
    <x-task-guide title="Encuentra un expediente" description="Puedes entrar por nivel o usar la búsqueda general." :steps="['Selecciona un nivel para ver su lista.', 'O escribe un nombre o matrícula.', 'Abre el expediente que necesites consultar.']" />

    <div class="row row-cards mb-4">
        @foreach ($niveles as $nivel)
            @php
                $iconos = ['Preescolar' => 'fa-child-reaching', 'Primaria Baja' => 'fa-book-open', 'Primaria Alta' => 'fa-graduation-cap', 'Secundaria' => 'fa-school'];
                $icono = $iconos[$nivel->nombre] ?? 'fa-users';
            @endphp
            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('capturista.index', ['nivel' => $nivel->id]) }}" class="card csl-choice-card">
                    <div class="card-body d-flex align-items-center gap-3"><span class="csl-choice-icon"><i class="fas {{ $icono }}"></i></span><div><h2 class="h3 mb-1">{{ $nivel->nombre }}</h2><p class="text-secondary mb-0 small">Ver alumnos del nivel</p></div></div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card csl-data-card">
        <div class="card-header"><div><h2 class="card-title mb-1">Búsqueda general</h2><div class="text-secondary">Filtra la lista sin salir de esta pantalla.</div></div></div>
        <div class="card-body"><x-search :grados="$grados" :secciones="$secciones" /></div>
    </div>
@endsection

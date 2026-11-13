@extends('layouts.app')

@section('title', 'Registrar alumno | Instituto Horizonte')

@section('content')
    <div class="csl-module-header"><h1>Registrar alumno</h1><p>Selecciona el nivel educativo para iniciar el registro.</p></div>
    <x-task-guide title="Antes de comenzar" description="Elige el nivel al que ingresará el alumno." :steps="['Selecciona el nivel educativo.', 'Completa los datos obligatorios del expediente.', 'Revisa la información antes de guardar.']" />

    <div class="row row-cards">
        @foreach ($niveles as $nivel)
            @php
                $iconos = ['Preescolar' => 'fa-child-reaching', 'Primaria Baja' => 'fa-book-open', 'Primaria Alta' => 'fa-graduation-cap', 'Secundaria' => 'fa-school'];
                $icono = $iconos[$nivel->nombre] ?? 'fa-users';
            @endphp
            <div class="col-sm-6 col-xl-3">
                <a href="{{ route('alumnos.create', ['nivel' => $nivel->id]) }}" class="card csl-choice-card">
                    <div class="card-body"><span class="csl-choice-icon"><i class="fas {{ $icono }}"></i></span><h2 class="h3 mt-4 mb-1">{{ $nivel->nombre }}</h2><p class="text-secondary mb-0">Crear un nuevo expediente de alumno.</p></div>
                    <div class="card-footer bg-transparent border-top-0 pt-0"><span class="text-success fw-semibold">Seleccionar <i class="fas fa-arrow-right ms-1"></i></span></div>
                </a>
            </div>
        @endforeach
    </div>
@endsection

@extends('layouts.app')

@section('content')

@if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

<br>
    <div class="container">
        <h1 class="text-center mb-4">Selecciona el Nivel Educativo</h1>
        <div class="row justify-content-center">
            @foreach ($niveles as $nivel)
                @php
                    // Asignar un color oscuro si no está en la base de datos
                    $colores = [
                        'Preescolar' => '#FF5733',   
                        'Primaria Inferior' => '#FFC300',  
                        'Primaria Superior' => '#28B463',  
                        'Secundaria' => '#1976D2'     
                    ];
                    $color = $nivel->color ?? ($colores[$nivel->nombre] ?? '#007bff');

                    // Asignar iconos para cada nivel
                    $iconos = [
                        'Preescolar' => 'fas fa-child',
                        'Primaria Inferior' => 'fas fa-book',
                        'Primaria Superior' => 'fas fa-graduation-cap',
                        'Secundaria' => 'fas fa-school'
                    ];
                    $icono = $iconos[$nivel->nombre] ?? 'fas fa-users';
                @endphp

                    <div class="col-12 col-sm-6 col-md-3 d-flex align-items-stretch">
    <div class="card text-white mb-4 shadow-lg w-100" style="background-color: {{ $color }}; border-radius: 15px;">
        <div class="card-body text-center d-flex flex-column">
            <i class="{{ $icono }} fa-3x mb-3"></i>
            <h3 class="card-title text-truncate" style="font-size: 1.2rem; max-width: 100%;">{{ $nivel->nombre }}</h3>
            <p class="card-text flex-grow-1">Selecciona este nivel para ver más detalles</p>
            <a href="{{ route('admin.showNivelAlumnos', ['nivelId' => $nivel->id]) }}" class="btn btn-light mt-auto">Seleccionar</a>
        </div>
    </div>
</div>
            @endforeach
        </div>
    </div>

@endsection

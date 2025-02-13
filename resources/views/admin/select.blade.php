@extends('layouts.app')

@section('content')
    <br><br><br><br>
    <div class="container">
        <h1 class="text-center mb-4">Selecciona el Nivel Educativo</h1>
        <div class="row justify-content-center">
            @foreach ($niveles as $nivel)
                @php
                    // Asignar un color si no está en la base de datos
                    $colores = [
                        'Preescolar' => '#FF5733',
                        'Primaria Baja' => '#FFC300',
                        'Primaria Alta' => '#28B463',
                        'Secundaria' => '#1F618D'
                    ];
                    $color = $nivel->color ?? ($colores[$nivel->nombre] ?? '#007bff');

                    // Asignar iconos para cada nivel
                    $iconos = [
                        'Preescolar' => 'fas fa-child',
                        'Primaria Baja' => 'fas fa-book',
                        'Primaria Alta' => 'fas fa-graduation-cap',
                        'Secundaria' => 'fas fa-school'
                    ];
                    $icono = $iconos[$nivel->nombre] ?? 'fas fa-users';
                @endphp

                <div class="col-12 col-sm-6 col-md-3 d-flex align-items-stretch">
                    <div class="card text-white mb-4 shadow-lg w-100" style="background-color: {{ $color }}; border-radius: 15px;">
                        <div class="card-body text-center">
                            <i class="{{ $icono }} fa-3x mb-3"></i>
                            <h3 class="card-title">{{ $nivel->nombre }}</h3>
                            <p class="card-text">Selecciona este nivel para ver más detalles</p>
                            <a href="{{ route('niveles.show', ['nivelId' => $nivel->id]) }}" class="btn btn-light">Seleccionar</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

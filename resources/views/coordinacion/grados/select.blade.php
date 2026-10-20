@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">Selecciona el Grado</h1>
        <div class="row justify-content-center">
            @foreach ($grados as $grado)
                @if (in_array($grado->nivel_educativo_id, [2, 3])) <!-- Verifica si es Primaria (ID 2 o 3) -->
                    <!-- Si es Primaria, lo ponemos en una fila con 2 columnas por fila -->
                    <div class="col-12 col-sm-6 col-md-6 d-flex align-items-stretch mb-4">
                        <div class="card text-white shadow-lg w-100" style="background-color: #007bff; border-radius: 15px;">
                            <div class="card-body text-center">
                                <h3 class="card-title">{{ $grado->nombre }}</h3>
                                <p class="card-text">Selecciona este grado para ver más detalles</p>
                                <a href="{{ route('coordinacion.grados.show', ['gradoId' => $grado->id]) }}" class="btn btn-light">Seleccionar</a>
                                </div>
                        </div>
                    </div>
                @else
                    <!-- Si no es Primaria, lo mostramos como en tu código original -->
                    <div class="col-12 col-sm-6 col-md-3 d-flex align-items-stretch mb-4">
                        <div class="card text-white shadow-lg w-100" style="background-color: #007bff; border-radius: 15px;">
                            <div class="card-body text-center">
                                <h3 class="card-title">{{ $grado->nombre }}</h3>
                                <p class="card-text">Selecciona este grado para ver más detalles</p>
                                <a href="{{ route('coordinacion.grados.show', ['gradoId' => $grado->id]) }}" class="btn btn-light">Seleccionar</a>
                                </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection

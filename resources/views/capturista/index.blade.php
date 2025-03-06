@extends('layouts.app')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        // Obtener el nivel desde la URL para mostrar el título correcto
        $nivel = request('nivel');
        $nivelEducativo = null;

        if ($nivel) {
            $nivelEducativo = App\Models\NivelEducativo::find($nivel);
        }
    @endphp

    <!-- Título con solo el botón de volver -->
    <div class="d-flex justify-content-between align-items-center mb-4 pt-4 ps-3 pe-3">
        <h1 class="me-4">Lista de Alumnos de @if($nivelEducativo) {{ $nivelEducativo->nombre }} @else Todos los Niveles @endif</h1>

        <!-- Solo el ícono de volver -->
        <a href="javascript:history.back()" class="btn btn-danger btn-lg ms-4">
            <i class="fas fa-arrow-left fa-2x"></i>
        </a>
    </div>

    <!-- Componente de búsqueda -->
    @component('components.search', ['nivel' => $nivel])
    @endcomponent
@endsection

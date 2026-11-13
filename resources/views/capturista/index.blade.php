@extends('layouts.app')

@section('content')
    @php
        // Obtener el nivel desde la URL para mostrar el título correcto
        $nivel = request('nivel');
        $nivelEducativo = null;

        if ($nivel) {
            $nivelEducativo = App\Models\NivelEducativo::find($nivel);
        }
    @endphp

    <div class="csl-module-header d-flex justify-content-between align-items-center gap-3 flex-wrap">
        <div>
            <h1>Alumnos@if($nivelEducativo) de {{ $nivelEducativo->nombre }}@endif</h1>
            <p>Busca por nombre, apellidos o matrícula.</p>
        </div>
        <a href="{{ route('capturista.selectsearch') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver a niveles</a>
    </div>

    <div class="card csl-data-card"><div class="card-body p-3 p-md-4">
        @component('components.search', ['nivel' => $nivel, 'grados' => $grados, 'secciones' => $secciones])
        @endcomponent
    </div></div>
@endsection

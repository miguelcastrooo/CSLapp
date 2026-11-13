@extends('layouts.app')

@section('title', 'Acceso no disponible | Instituto Horizonte')

@section('content')
<div class="row justify-content-center py-4 py-md-5">
    <div class="col-md-9 col-lg-7 col-xl-6">
        <div class="card csl-data-card text-center">
            <div class="card-body p-4 p-md-5">
                <span class="csl-choice-icon mb-3"><i class="fas fa-lock"></i></span>
                <h1 class="h2 mb-2">Esta sección no está disponible para tu cuenta</h1>
                <p class="text-secondary mb-4">Solo puedes consultar y modificar la información de los niveles educativos que tienes asignados.</p>
                <div class="d-flex justify-content-center flex-wrap gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary"><i class="fas fa-house me-2"></i>Ir al panel principal</a>
                    <a href="{{ route('capturista.selectsearch') }}" class="btn btn-outline-secondary"><i class="fas fa-users me-2"></i>Ver mis alumnos</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

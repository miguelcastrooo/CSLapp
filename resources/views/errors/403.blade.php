@extends('layouts.app')

@section('title', 'Acceso denegado')

@section('content')
<div class="container text-center">
    <h1 class="display-4 text-danger">403</h1>
    <h2 class="text-secondary">Acceso denegado</h2>
    <p class="lead">No tienes permisos para acceder a esta página.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary">Volver al inicio</a>
</div>
@endsection

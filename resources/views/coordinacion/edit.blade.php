@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Grado</h1>

    <form action="{{ route('coordinacion.update', $grado->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre del Grado</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ $grado->nombre }}" required>
        </div>

        <div class="form-group">
            <label for="nivel">Nivel</label>
            <input type="text" name="nivel" id="nivel" class="form-control" value="{{ $grado->nivel }}" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
    </form>
</div>
@endsection

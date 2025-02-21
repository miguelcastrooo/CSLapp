@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Gestión de Grados</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Nombre del Grado</th>
                <th>Nivel</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grados as $grado)
                <tr>
                    <td>{{ $grado->nombre }}</td>
                    <td>{{ $grado->nivel }}</td>
                    <td>
                        <a href="{{ route('coordinacion.edit', $grado->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

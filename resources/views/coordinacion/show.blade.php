@extends('layouts.app')

@section('content')
    <h2>Detalles del Alumno: {{ $alumno->nombre }} {{ $alumno->apellidopaterno }}</h2>

    <table class="table table-bordered">
        <tr>
            <th>Nombre</th>
            <td>{{ $alumno->nombre }} {{ $alumno->apellidopaterno }}</td>
        </tr>
        <tr>
            <th>Matrícula</th>
            <td>{{ $alumno->matricula }}</td>
        </tr>
        <tr>
            <th>Grado</th>
            <td>{{ $alumno->grado }}</td>
        </tr>
        <tr>
            <th>Sección</th>
            <td>{{ $alumno->seccion }}</td>
        </tr>
        <tr>
            <th>Usuario Classroom</th>
            <td>{{ $alumno->usuario_classroom }}</td>
        </tr>
        <tr>
            <th>Contraseña Classroom</th>
            <td>{{ $alumno->contraseña_classroom }}</td>
        </tr>
        <!-- Agregar más campos si es necesario -->
    </table>
@endsection

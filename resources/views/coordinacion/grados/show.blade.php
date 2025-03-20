@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Detalles del Alumno: {{ $alumnos->nombre }} {{ $alumnos->apellido }}</h1>
        
        <table class="table table-bordered">
            <tr>
                <th>Nombre</th>
                <td>{{ $alumno->nombre }}</td>
            </tr>
            <tr>
                <th>Apellido</th>
                <td>{{ $alumno->apellidopaterno }}</td>
            </tr>
            <tr>
                <th>Apellido</th>
                <td>{{ $alumno->apellidomaterno }}</td>
            </tr>
            <tr>
                <th>Grado</th>
                <td>{{ $grado->nombre }}</td>
            </tr>
        
            <!-- Agregar más campos que necesites mostrar -->
        </table>
    </div>
@endsection

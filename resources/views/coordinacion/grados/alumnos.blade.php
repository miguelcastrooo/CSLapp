@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">Alumnos del Grado: {{ $grado->nombre }}</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos as $alumno)
                        <tr>
                            <td>{{ $alumno->id }}</td>
                            <td>{{ $alumno->nombre }}</td>
                            <td>{{ $alumno->apellido }}</td>
                            <td>
                                <!-- Aquí puedes agregar más acciones, por ejemplo, ver detalles o editar -->
                                <a href="{{ route('coordinacion.grados.show', ['id' => $alumno->id]) }}" class="btn btn-info btn-sm">Ver Detalles</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Paginación -->
            {{ $alumnos->links() }}
        </div>
    </div>
@endsection

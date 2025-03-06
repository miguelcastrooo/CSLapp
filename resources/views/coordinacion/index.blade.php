@extends('layouts.app')

@section('content')
    @if (isset($nivelEducativo) && $nivelEducativo)
        <h2>Información de {{ $nivelEducativo->nombre }}</h2>
    @else
        <p>No se encontró el nivel educativo.</p>
    @endif

    @if (isset($alumnos) && $alumnos->isNotEmpty())
        <h3>Lista de Alumnos</h3>
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario Classroom</th>
                        <th>Contraseña Classroom</th>
                        <th>Usuario Moodle</th>
                        <th>Contraseña Moodle</th>
                        @if (strpos($nivelEducativo->nombre, 'Primaria') !== false)
                            <th>Usuario HMH</th>
                            <th>Contraseña HMH</th>
                        @endif
                        @if (strpos($nivelEducativo->nombre, 'Secundaria') !== false)
                            <th>Usuario Mathletics</th>
                            <th>Contraseña Mathletics</th>
                        @endif
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos as $alumno)
                        <tr>
                            <td>{{ $alumno->nombre }} {{ $alumno->apellidopaterno }}</td>
                            <td>{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                            <td>{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                            <td>{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                            <td>{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>
                            @if (strpos($nivelEducativo->nombre, 'Primaria') !== false)
                                <td>{{ $alumno->usuario_hmh ?? 'N/A' }}</td>
                                <td>{{ $alumno->contraseña_hmh ?? 'N/A' }}</td>
                            @endif
                            @if (strpos($nivelEducativo->nombre, 'Secundaria') !== false)
                                <td>{{ $alumno->usuario_mathletics ?? 'N/A' }}</td>
                                <td>{{ $alumno->contraseña_mathletics ?? 'N/A' }}</td>
                            @endif
                            <td class="text-center">
                                <a href="{{ route('admin.alumnos.pdf.individual', ['nivel' => $nivelEducativo->nombre, 'id' => $alumno->id]) }}" 
                                   class="btn btn-danger btn-sm">Generar PDF</a>
                                <a href="#" class="btn btn-info btn-sm">Enviar Correo</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $alumnos->links('pagination::bootstrap-4') }}
        </div>
    @else
        <p>No hay alumnos disponibles para este nivel.</p>
    @endif
@endsection

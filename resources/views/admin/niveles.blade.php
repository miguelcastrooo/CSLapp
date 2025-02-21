@extends('layouts.app')

@section('content')
    <!-- Mostrar la información del nivel seleccionado y los alumnos -->
    @isset($nivelEducativo)
        <h1>Información de {{ $nivelEducativo->nombre }}</h1>

        <!-- Mostrar los alumnos solo si hay alumnos disponibles -->
        @if (isset($alumnos) && $alumnos->count() > 0)
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Usuario Classroom</th>
                            <th>Contraseña Classroom</th>
                            <th>Usuario Moodle</th>
                            <th>Contraseña Moodle</th>
                            
                            @if (in_array($nivelEducativo->nombre, ['Primaria Baja', 'Primaria Alta']))
                                <th>Usuario HMH</th>
                                <th>Contraseña HMH</th>
                            @endif

                            @if (in_array($nivelEducativo->nombre, ['Primaria Alta', 'Secundaria']))
                                <th>Usuario Mathletics</th>
                                <th>Contraseña Mathletics</th>
                            @endif

                            @if ($nivelEducativo->nombre == 'Primaria Alta')
                                <th>Usuario Progrentis</th>
                                <th>Contraseña Progrentis</th>
                            @endif

                            <th>Acciones</th> <!-- Nueva columna para acciones -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alumnos as $alumno)
                            <!-- Verificar si todos los campos están completos antes de mostrar el alumno -->
                            @if (
                                $alumno->usuario_classroom && 
                                $alumno->contraseña_classroom && 
                                $alumno->usuario_moodle && 
                                $alumno->contraseña_moodle && 
                                (
                                    !in_array($nivelEducativo->nombre, ['Primaria Baja', 'Primaria Alta']) || 
                                    ($alumno->usuario_hmh && $alumno->contraseña_hmh)
                                ) && 
                                (
                                    !in_array($nivelEducativo->nombre, ['Primaria Alta', 'Secundaria']) || 
                                    ($alumno->usuario_mathletics && $alumno->contraseña_mathletics)
                                ) && 
                                (
                                    $nivelEducativo->nombre != 'Primaria Alta' || 
                                    ($alumno->usuario_progrentis && $alumno->contraseña_progrentis)
                                )
                            )
                                <tr>
                                    <td>{{ $alumno->nombre }} {{ $alumno->apellidopaterno }}</td>
                                    <td>{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                                    <td>{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                                    <td>{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                                    <td>{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>

                                    @if (in_array($nivelEducativo->nombre, ['Primaria Baja', 'Primaria Alta']))
                                        <td>{{ $alumno->usuario_hmh ?? 'N/A' }}</td>
                                        <td>{{ $alumno->contraseña_hmh ?? 'N/A' }}</td>
                                    @endif

                                    @if (in_array($nivelEducativo->nombre, ['Primaria Alta', 'Secundaria']))
                                        <td>{{ $alumno->usuario_mathletics ?? 'N/A' }}</td>
                                        <td>{{ $alumno->contraseña_mathletics ?? 'N/A' }}</td>
                                    @endif

                                    @if ($nivelEducativo->nombre == 'Primaria Alta')
                                        <td>{{ $alumno->usuario_progrentis ?? 'N/A' }}</td>
                                        <td>{{ $alumno->contraseña_progrentis ?? 'N/A' }}</td>
                                    @endif

                                    <!-- Columna de acciones -->
                                    <td class="text-center">
                                        <a href="{{ route('admin.alumnos.pdf.individual', ['nivel' => $alumno->nivelEducativo->nombre, 'id' => $alumno->id]) }}" 
                                        class="btn btn-danger btn-sm">Generar PDF</a>
                                        <a href="#" class="btn btn-info btn-sm">Enviar Correo</a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $alumnos->links('pagination::bootstrap-4') }}
            </div>
        @else
            <p>No hay alumnos disponibles para este nivel.</p>
        @endif

        <a href="{{ route('admin.select') }}" class="btn btn-primary mt-3">Volver</a>
    @endisset
@endsection

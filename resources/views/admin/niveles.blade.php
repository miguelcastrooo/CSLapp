@extends('layouts.app')

@section('content')
    <!-- Mostrar los botones de los niveles solo si no hay un nivel seleccionado -->
    @empty($nivelEducativo)
        <h1>Selecciona un Nivel Educativo</h1>

        <!-- Botones de los niveles -->
        <div class="btn-group mb-4" id="niveles-container">
            @foreach ($niveles as $nivel)
                <a href="{{ route('niveles.show', ['nivel' => $nivel->id]) }}" class="btn btn-info btn-sm">
                    {{ $nivel->nombre }}
                </a>
            @endforeach
        </div>
    @endempty

    <!-- Mostrar información del nivel y alumnos solo si hay un nivel seleccionado -->
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

                            @if (in_array($nivelEducativo->nombre, ['Primaria Baja', 'Primaria Alta', 'Preescolar']))
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
                            <tr>
                                <td>{{ $alumno->nombre }} {{ $alumno->apellidopaterno }}</td>
                                <td>{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                                <td>{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                                <td>{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                                <td>{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>

                                @if (in_array($nivelEducativo->nombre, ['Primaria Baja', 'Primaria Alta', 'Preescolar']))
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

        <a href="{{ route('admin.index') }}" class="btn btn-primary mt-3">Volver</a>
    @endisset
@endsection

@section('scripts')
    <script>
        // Mantener los botones de los niveles visibles aunque se seleccione uno
        document.addEventListener("DOMContentLoaded", function() {
            const nivelesContainer = document.getElementById('niveles-container');
            // Verifica si ya hay un nivel seleccionado para mantener los botones visibles
            if (nivelesContainer) {
                nivelesContainer.style.display = 'block'; // Asegurarse que los botones de niveles se muestren
            }
        });
    </script>
@endsection

@extends('layouts.app')

@section('content')
    @isset($nivelEducativo)
        <h1 class="text-center mb-4">{{ $nivelEducativo->nombre }} - Información de Alumnos</h1>

        @if (isset($alumnos) && $alumnos->count() > 0)
            <div class="table-responsive mt-4">
                <table class="table table-sm" style="background-color: white; border-collapse: collapse;">
                    <thead class="thead-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Plataforma</th>
                            <th>Usuario</th>
                            <th>Contraseña</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($alumnos as $alumno)
                        @foreach ($alumno->alumnoPlataforma ?? [] as $index => $plataforma)
                            <tr>
                                @if ($loop->first) <!-- Mostrar solo la primera vez -->
                                    <td rowspan="{{ count($alumno->alumnoPlataforma) }}" class="align-middle">
                                        {{ $alumno->nombre }} {{ $alumno->apellidopaterno }}
                                    </td>
                                @endif

                                <!-- Mostrar la plataforma, usuario y contraseña de manera dinámica -->
                                <td>{{ $plataforma->nombre_plataforma }}</td>
                                <td>{{ $plataforma->usuario ?? 'N/A' }}</td>
                                <td>{{ $plataforma->contraseña ?? 'N/A' }}</td>

                                <!-- Acción de generar PDF y correo -->
                                @if ($loop->last) <!-- Solo al final de cada conjunto de plataformas -->
                                    <td class="text-center">
                                        <a href="{{ route('admin.alumnos.pdf.individual', ['nivel' => $nivelEducativo->nombre, 'id' => $alumno->id]) }}" 
                                           class="btn btn-danger btn-sm mb-2">Generar PDF</a>
                                        <a href="#" class="btn btn-info btn-sm">Enviar Correo</a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $alumnos->links('pagination::bootstrap-4') }}
            </div>
        @else
            <p class="text-center mt-4">No hay alumnos disponibles para este nivel.</p>
        @endif

        <div class="text-center mt-4">
            <a href="{{ route('admin.select') }}" class="btn btn-primary">Volver</a>
        </div>
    @endisset
@endsection

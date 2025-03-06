@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h1 class="mb-4">Alumnos del Nivel: {{ $nivel->nombre }}</h1>

    <!-- Contenedor de la tabla con scroll horizontal -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="text-center">
                <tr>
                    <th class="p-3 text-nowrap">ID</th>
                    <th class="p-3 text-nowrap">Matrícula</th>
                    <th class="p-3 text-nowrap">Nombre</th>
                    <th class="p-3 text-nowrap">Apellido Paterno</th>
                    <th class="p-3 text-nowrap">Apellido Materno</th>
                    
                    <!-- Condiciones para mostrar plataformas según el nivel -->
                    @switch($nivel->nombre)
                        @case('Preescolar')
                            <th class="p-3 text-nowrap">Usuario Classroom</th>
                            <th class="p-3 text-nowrap">Contraseña Classroom</th>
                            <th class="p-3 text-nowrap">Usuario Moodle</th>
                            <th class="p-3 text-nowrap">Contraseña Moodle</th>
                            @break

                        @case('Primaria Baja')
                            <th class="p-3 text-nowrap">Usuario Classroom</th>
                            <th class="p-3 text-nowrap">Contraseña Classroom</th>
                            <th class="p-3 text-nowrap">Usuario Moodle</th>
                            <th class="p-3 text-nowrap">Contraseña Moodle</th>
                            <th class="p-3 text-nowrap">Usuario HMH</th>
                            <th class="p-3 text-nowrap">Contraseña HMH</th>
                            @break

                        @case('Primaria Alta')
                            <th class="p-3 text-nowrap">Usuario Classroom</th>
                            <th class="p-3 text-nowrap">Contraseña Classroom</th>
                            <th class="p-3 text-nowrap">Usuario Moodle</th>
                            <th class="p-3 text-nowrap">Contraseña Moodle</th>
                            <th class="p-3 text-nowrap">Usuario HMH</th>
                            <th class="p-3 text-nowrap">Contraseña HMH</th>
                            <th class="p-3 text-nowrap">Usuario Mathletics</th>
                            <th class="p-3 text-nowrap">Contraseña Mathletics</th>
                            <th class="p-3 text-nowrap">Usuario Progrentis</th>
                            <th class="p-3 text-nowrap">Contraseña Progrentis</th>
                            @break

                        @case('Secundaria')
                            <th class="p-3 text-nowrap">Usuario Classroom</th>
                            <th class="p-3 text-nowrap">Contraseña Classroom</th>
                            <th class="p-3 text-nowrap">Usuario Moodle</th>
                            <th class="p-3 text-nowrap">Contraseña Moodle</th>
                            <th class="p-3 text-nowrap">Usuario Mathletics</th>
                            <th class="p-3 text-nowrap">Contraseña Mathletics</th>
                            @break
                    @endswitch

                    <th class="p-3 text-nowrap">Grado</th>
                    <th class="p-3 text-nowrap">Sección</th>
                    <th class="p-3 text-nowrap">Fecha de Inscripción</th>
                    <th class="p-3 text-nowrap">Fecha de Inicio</th>
                    <th class="p-3 text-nowrap text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumnos as $alumno)
                    <tr>
                        <td class="p-3">{{ $alumno->id }}</td>
                        <td class="p-3">{{ $alumno->matricula }}</td>
                        <td class="p-3">{{ $alumno->nombre }}</td>
                        <td class="p-3">{{ $alumno->apellidopaterno }}</td>
                        <td class="p-3">{{ $alumno->apellidomaterno }}</td>

                        <!-- Mostrar solo las plataformas correspondientes -->
                        @switch($nivel->nombre)
                            @case('Preescolar')
                                <td class="p-3">{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>
                                @break

                            @case('Primaria Baja')
                                <td class="p-3">{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->usuario_hmh ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_hmh ?? 'N/A' }}</td>
                                @break

                            @case('Primaria Alta')
                                <td class="p-3">{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->usuario_hmh ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_hmh ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->usuario_mathletics ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_mathletics ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->usuario_progrentis ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_progrentis ?? 'N/A' }}</td>
                                @break

                            @case('Secundaria')
                                <td class="p-3">{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->usuario_mathletics ?? 'N/A' }}</td>
                                <td class="p-3">{{ $alumno->contraseña_mathletics ?? 'N/A' }}</td>
                                @break
                        @endswitch

                        <td class="p-3">{{ $alumno->grado->nombre ?? 'N/A' }}</td>
                        <td class="p-3">{{ $alumno->seccion ?? 'N/A' }}</td>
                        <td class="p-3">{{ $alumno->fecha_inscripcion }}</td>
                        <td class="p-3">{{ $alumno->fecha_inicio }}</td>
                        <td class="p-3">
                            <a href="{{ route('admin.adminedit', $alumno->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.selectadmin') }}" class="btn btn-primary mt-3">Volver</a>
</div>

@endsection

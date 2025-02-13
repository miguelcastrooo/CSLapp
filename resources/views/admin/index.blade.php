@extends('layouts.app')

@section('content')
    <h1>Gestión de Alumnos</h1>

    <!-- Tabla de alumnos -->
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Matrícula</th>
                    <th>Nombre</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Correo</th>
                    <th>Contacto 1 (Nombre)</th>
                    <th>Teléfono 1</th>
                    <th>Correo Familiar</th>
                    <th>Contacto 2 (Nombre)</th>
                    <th>Teléfono 2</th>
                    <th>Usuario Classroom</th>
                    <th>Contraseña Classroom</th>
                    <th>Usuario Moodle</th>
                    <th>Contraseña Moodle</th>
                    <th>Usuario Mathletics</th>
                    <th>Contraseña Mathletics</th>
                    <th>Usuario HMH</th>
                    <th>Contraseña HMH</th>
                    <th>Usuario Progrentis</th>
                    <th>Contraseña Progrentis</th>
                    <th>Nivel Educativo</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Fecha de Inscripción</th>
                    <th class="text-center" style="min-width: 180px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumnos as $alumno)
                    <tr>
                        <td>{{ $alumno->id }}</td>
                        <td>{{ $alumno->matricula }}</td>
                        <td>{{ $alumno->nombre }}</td>
                        <td>{{ $alumno->apellidopaterno }}</td>
                        <td>{{ $alumno->apellidomaterno }}</td>
                        <td>{{ $alumno->correo ?? 'N/A' }}</td>
                        <td>{{ $alumno->contacto1nombre ?? 'N/A' }}</td>
                        <td>{{ $alumno->telefono1 ?? 'N/A' }}</td>
                        <td>{{ $alumno->correo_familia ?? 'N/A' }}</td>
                        <td>{{ $alumno->contacto2nombre ?? 'N/A' }}</td>
                        <td>{{ $alumno->telefono2 ?? 'N/A' }}</td>
                        <td>{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                        <td>{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>
                        <td>{{ $alumno->usuario_mathletics ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_mathletics ?? 'N/A' }}</td>
                        <td>{{ $alumno->usuario_hmh ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_hmh ?? 'N/A' }}</td>
                        <td>{{ $alumno->usuario_progrentis ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_progrentis ?? 'N/A' }}</td>
                        <td>{{ $alumno->nivelEducativo->nombre ?? 'N/A' }}</td>
                        <td>{{ $alumno->grado->nombre ?? 'N/A' }}</td>
                        <td>{{ $alumno->seccion ?? 'N/A' }}</td>
                        <td>{{ $alumno->fecha_inscripcion }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.adminedit', $alumno->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalBaja" data-id="{{ $alumno->id }}">Dar Baja</button>
                            </div>
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

  <!-- Modal de Confirmación -->
<div class="modal fade" id="modalBaja" tabindex="-1" role="dialog" aria-labelledby="modalBajaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBajaLabel">Confirmar Baja</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="formBaja" action="{{ route('admin.confirmarBaja', $alumno->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="motivo_baja">Motivo de la Baja:</label>
                    <textarea id="motivo_baja" name="motivo_baja" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-danger">Confirmar Baja</button>
            </form>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#modalBaja').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // El botón que abrió el modal
            var id = button.data('id'); // El ID del alumno
            var form = $('#formBaja'); // El formulario del modal
            form.attr('action', '/admin/alumnos/confirmarBaja/' + id); // Asignar la URL correcta con el ID
        });
    });
</script>
@endsection

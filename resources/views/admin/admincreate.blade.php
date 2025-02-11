@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Alumno: {{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</h1>
    <p>Actualiza los datos del alumno a continuación:</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('alumnos.update', $alumno->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Campos existentes como matrícula, nombre, apellidos, etc. -->
            <div class="col-md-6 mb-3">
                <label for="matricula">Matrícula</label>
                <input type="number" class="form-control" id="matricula" name="matricula" value="{{ old('matricula', $alumno->matricula) }}" required min="1">
            </div>

            <div class="col-md-6 mb-3">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $alumno->nombre) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo letras y espacios">
            </div>

            <div class="col-md-6 mb-3">
                <label for="apellidopaterno">Apellido Paterno</label>
                <input type="text" class="form-control" id="apellidopaterno" name="apellidopaterno" value="{{ old('apellidopaterno', $alumno->apellidopaterno) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+">
            </div>

            <div class="col-md-6 mb-3">
                <label for="apellidomaterno">Apellido Materno</label>
                <input type="text" class="form-control" id="apellidomaterno" name="apellidomaterno" value="{{ old('apellidomaterno', $alumno->apellidomaterno) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+">
            </div>

            <div class="col-md-6 mb-3">
                <label for="contacto1nombre">Nombre del Primer Contacto</label>
                <input type="text" class="form-control" id="contacto1nombre" name="contacto1nombre" value="{{ old('contacto1nombre', $alumno->contacto1nombre) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+">
            </div>

            <div class="col-md-6 mb-3">
                <label for="telefono1">Teléfono del Primer Contacto</label>
                <input type="text" class="form-control" id="telefono1" name="telefono1" value="{{ old('telefono1', $alumno->telefono1) }}" maxlength="10" required pattern="\d{10}" title="Debe ser un número de 10 dígitos">
            </div>

            <div class="col-md-6 mb-3">
                <label for="correo_familia">Correo del Familiar</label>
                <input type="email" class="form-control" id="correo_familia" name="correo_familia" value="{{ old('correo_familia', $alumno->correo_familia) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="contacto2nombre">Nombre del Segundo Contacto (Opcional)</label>
                <input type="text" class="form-control" id="contacto2nombre" name="contacto2nombre" value="{{ old('contacto2nombre', $alumno->contacto2nombre) }}" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+">
            </div>

            <div class="col-md-6 mb-3">
                <label for="telefono2">Teléfono del Segundo Contacto (Opcional)</label>
                <input type="text" class="form-control" id="telefono2" name="telefono2" value="{{ old('telefono2', $alumno->telefono2) }}" maxlength="10" pattern="\d{10}">
            </div>

            <!-- Campos adicionales para los usuarios y contraseñas de las plataformas -->
            <div class="col-md-6 mb-3">
                <label for="usuario_classroom">Usuario Classroom</label>
                <input type="text" class="form-control" id="usuario_classroom" name="usuario_classroom" value="{{ old('usuario_classroom', $alumno->usuario_classroom) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="contraseña_classroom">Contraseña Classroom</label>
                <input type="password" class="form-control" id="contraseña_classroom" name="contraseña_classroom" value="{{ old('contraseña_classroom', $alumno->contraseña_classroom) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="usuario_moodle">Usuario Moodle</label>
                <input type="text" class="form-control" id="usuario_moodle" name="usuario_moodle" value="{{ old('usuario_moodle', $alumno->usuario_moodle) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="contraseña_moodle">Contraseña Moodle</label>
                <input type="password" class="form-control" id="contraseña_moodle" name="contraseña_moodle" value="{{ old('contraseña_moodle', $alumno->contraseña_moodle) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="usuario_mathletics">Usuario Mathletics</label>
                <input type="text" class="form-control" id="usuario_mathletics" name="usuario_mathletics" value="{{ old('usuario_mathletics', $alumno->usuario_mathletics) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="contraseña_mathletics">Contraseña Mathletics</label>
                <input type="password" class="form-control" id="contraseña_mathletics" name="contraseña_mathletics" value="{{ old('contraseña_mathletics', $alumno->contraseña_mathletics) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="seccion">Sección</label>
                <input type="text" class="form-control" id="seccion" name="seccion" value="{{ old('seccion', $alumno->seccion) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="fecha_inscripcion">Fecha de Inscripción</label>
                <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', $alumno->fecha_inscripcion) }}" required>
            </div>

            <div class="col-md-12 mb-3">
                <button type="submit" class="btn btn-success">Actualizar Alumno</button>
            </div>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ isset($alumno) ? 'Editar Alumno' : 'Agregar Alumno' }}</h1>
    <p>Ingresa los datos del alumno a continuación:</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Si estamos editando, la acción del formulario será a la ruta de actualización -->
    <form action="{{ isset($alumno) ? route('alumnos.update', $alumno->id) : route('alumnos.store') }}" method="POST">
        @csrf
        @if (isset($alumno))
            @method('PUT') <!-- Indica que es una actualización -->
        @endif

        <div class="row">
            <!-- Los campos del formulario se mantienen iguales, pero con valores prellenados si estamos editando -->
            <div class="col-md-6 mb-3">
                <label for="matricula">Matrícula</label>
                <input type="number" class="form-control" id="matricula" name="matricula" value="{{ old('matricula', isset($alumno) ? $alumno->matricula : '') }}" required min="1">
            </div>

            <div class="col-md-6 mb-3">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', isset($alumno) ? $alumno->nombre : '') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo letras y espacios">
            </div>

            <div class="col-md-6 mb-3">
                <label for="apellidopaterno">Apellido Paterno</label>
                <input type="text" class="form-control" id="apellidopaterno" name="apellidopaterno" value="{{ old('apellidopaterno', isset($alumno) ? $alumno->apellidopaterno : '') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+">
            </div>

            <div class="col-md-6 mb-3">
                <label for="apellidomaterno">Apellido Materno</label>
                <input type="text" class="form-control" id="apellidomaterno" name="apellidomaterno" value="{{ old('apellidomaterno', isset($alumno) ? $alumno->apellidomaterno : '') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+">
            </div>

            <div class="col-md-6 mb-3">
                <label for="contacto1nombre">Nombre del Primer Contacto</label>
                <input type="text" class="form-control" id="contacto1nombre" name="contacto1nombre" value="{{ old('contacto1nombre', isset($alumno) ? $alumno->contacto1nombre : '') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+">
            </div>

            <div class="col-md-6 mb-3">
                <label for="telefono1">Teléfono del Primer Contacto</label>
                <input type="text" class="form-control" id="telefono1" name="telefono1" value="{{ old('telefono1', isset($alumno) ? $alumno->telefono1 : '') }}" maxlength="10" required pattern="\d{10}" title="Debe ser un número de 10 dígitos">
            </div>

            <div class="col-md-6 mb-3">
                <label for="correo_familia">Correo del Familiar</label>
                <input type="email" class="form-control" id="correo_familia" name="correo_familia" value="{{ old('correo_familia', isset($alumno) ? $alumno->correo_familia : '') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="contacto2nombre">Nombre del Segundo Contacto (Opcional)</label>
                <input type="text" class="form-control" id="contacto2nombre" name="contacto2nombre" value="{{ old('contacto2nombre', isset($alumno) ? $alumno->contacto2nombre : '') }}" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+">
            </div>

            <div class="col-md-6 mb-3">
                <label for="telefono2">Teléfono del Segundo Contacto (Opcional)</label>
                <input type="text" class="form-control" id="telefono2" name="telefono2" value="{{ old('telefono2', isset($alumno) ? $alumno->telefono2 : '') }}" maxlength="10" pattern="\d{10}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="nivel_educativo_id" class="form-label">Nivel Educativo</label>
                <select class="form-control" id="nivel_educativo_id" name="nivel_educativo_id" onchange="actualizarGrados()" required>
                    <option value="">Selecciona un nivel educativo</option>
                    @foreach ($niveles as $nivel)
                        <option value="{{ $nivel->id }}" {{ old('nivel_educativo_id', isset($alumno) ? $alumno->nivel_educativo_id : '') == $nivel->id ? 'selected' : '' }}>
                            {{ $nivel->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="grado_id" class="form-label">Grado</label>
                <select class="form-control" id="grado_id" name="grado_id" required>
                    <option value="">Selecciona un grado</option>
                    @foreach ($grados as $grado)
                        <option value="{{ $grado->id }}" {{ old('grado_id', isset($alumno) ? $alumno->grado_id : '') == $grado->id ? 'selected' : '' }}>
                            {{ $grado->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="fecha_inscripcion">Fecha de Inscripción</label>
                <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', isset($alumno) ? $alumno->fecha_inscripcion : '') }}" required>
            </div>

            <div class="col-md-12 mb-3">
                <button type="submit" class="btn btn-primary">{{ isset($alumno) ? 'Actualizar Alumno' : 'Registrar Alumno' }}</button>
            </div>
        </div>
    </form>
</div>

<script>
function actualizarGrados() {
    var nivel = document.getElementById('nivel_educativo_id').value;
    var gradoSelect = document.getElementById('grado_id');
    var grados = {
        '1': [
            { id: 1, nombre: 'BabiesRoom' },
            { id: 2, nombre: 'Primero de Kinder' },
            { id: 3, nombre: 'Segundo de Kinder' },
            { id: 4, nombre: 'Tercero de Kinder' }
        ],
        '2': [
            { id: 5, nombre: '1° Primaria' },
            { id: 6, nombre: '2° Primaria' },
            { id: 7, nombre: '3° Primaria' }
        ],
        '3': [
            { id: 8, nombre: '4° Primaria' },
            { id: 9, nombre: '5° Primaria' },
            { id: 10, nombre: '6° Primaria' }
        ],
        '4': [
            { id: 11, nombre: '1° Secundaria' },
            { id: 12, nombre: '2° Secundaria' },
            { id: 13, nombre: '3° Secundaria' }
        ]
    };

    gradoSelect.innerHTML = '<option value="">Selecciona un grado</option>'; // Reset opciones

    if (nivel in grados) {
        grados[nivel].forEach(function(grado) {
            var option = document.createElement('option');
            option.value = grado.id;  // Usar el ID del grado
            option.text = grado.nombre;
            if ("{{ old('grado_id', isset($alumno) ? $alumno->grado_id : '') }}" == grado.id) {
                option.selected = true;
            }
            gradoSelect.appendChild(option);
        });
    }
}
</script>

@endsection

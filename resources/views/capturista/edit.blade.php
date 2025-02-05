@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Alumno</h1>
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
            <div class="col-md-6 mb-3">
                <label for="matricula">Matrícula</label>
                <input type="number" class="form-control" id="matricula" name="matricula" value="{{ old('matricula', $alumno->matricula) }}" maxlength="10" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $alumno->nombre) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="apellidopaterno">Apellido Paterno</label>
                <input type="text" class="form-control" id="apellidopaterno" name="apellidopaterno" value="{{ old('apellidopaterno', $alumno->apellidopaterno) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="apellidomaterno">Apellido Materno</label>
                <input type="text" class="form-control" id="apellidomaterno" name="apellidomaterno" value="{{ old('apellidomaterno', $alumno->apellidomaterno) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="contacto1nombre">Nombre del Primer Contacto</label>
                <input type="text" class="form-control" id="contacto1nombre" name="contacto1nombre" value="{{ old('contacto1nombre', $alumno->contacto1nombre) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="telefono1">Teléfono del Primer Contacto</label>
                <input type="number" class="form-control" id="telefono1" name="telefono1" value="{{ old('telefono1', $alumno->telefono1) }}" maxlength="10" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="correo_familia">Correo del Familiar</label>
                <input type="email" class="form-control" id="correo_familia" name="correo_familia" value="{{ old('correo_familia', $alumno->correo_familia) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="contacto2nombre">Nombre del Segundo Contacto (Opcional)</label>
                <input type="text" class="form-control" id="contacto2nombre" name="contacto2nombre" value="{{ old('contacto2nombre', $alumno->contacto2nombre) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="telefono2">Teléfono del Segundo Contacto (Opcional)</label>
                <input type="number" class="form-control" id="telefono2" name="telefono2" value="{{ old('telefono2', $alumno->telefono2) }}" maxlength="10">
            </div>

            <div class="col-md-6 mb-3">
                <label for="nivel_educativo" class="form-label">Nivel Educativo</label>
                <select class="form-control" id="nivel_educativo" name="nivel_educativo" required>
                    <option value="">Selecciona un nivel educativo</option>
                    <option value="Preescolar" {{ old('nivel_educativo', $alumno->nivel_educativo) == 'Preescolar' ? 'selected' : '' }}>Preescolar</option>
                    <option value="Primaria Baja" {{ old('nivel_educativo', $alumno->nivel_educativo) == 'Primaria Baja' ? 'selected' : '' }}>Primaria Baja</option>
                    <option value="Primaria Alta" {{ old('nivel_educativo', $alumno->nivel_educativo) == 'Primaria Alta' ? 'selected' : '' }}>Primaria Alta</option>
                    <option value="Secundaria" {{ old('nivel_educativo', $alumno->nivel_educativo) == 'Secundaria' ? 'selected' : '' }}>Secundaria</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="grado" class="form-label">Grado</label>
                <select class="form-control" id="grado" name="grado" required>
                    <option value="">Selecciona un grado</option>
                    @foreach($gradosPorNivel[old('nivel_educativo', $alumno->nivel_educativo)] ?? [] as $gradoItem)
                        <option value="{{ $gradoItem }}" {{ old('grado', $alumno->grado) == $gradoItem ? 'selected' : '' }}>{{ $gradoItem }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="fecha_inscripcion">Fecha de Inscripción</label>
                <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', $alumno->fecha_inscripcion) }}" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Actualizar Alumno</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nivelEducativo = document.getElementById('nivel_educativo');
    const grado = document.getElementById('grado');

    const gradosPorNivel = {
        'Preescolar': ['Babies Room', '1° Kínder', '2° Kínder', '3° Kínder'],
        'Primaria Baja': ['1°', '2°', '3°'],
        'Primaria Alta': ['4°', '5°', '6°'],
        'Secundaria': ['1°', '2°', '3°']
    };

    const oldGrado = "{{ old('grado', $alumno->grado) }}";

    nivelEducativo.addEventListener('change', function() {
        const nivelSeleccionado = nivelEducativo.value;
        grado.innerHTML = '<option value="">Selecciona un grado</option>';

        if (gradosPorNivel[nivelSeleccionado]) {
            gradosPorNivel[nivelSeleccionado].forEach(function(gradoItem) {
                const option = document.createElement('option');
                option.value = gradoItem;
                option.textContent = gradoItem;
                if (gradoItem === oldGrado) {
                    option.selected = true;
                }
                grado.appendChild(option);
            });
        }
    });

    if (nivelEducativo.value) {
        nivelEducativo.dispatchEvent(new Event('change'));
    }

    const limitarLongitud = (campo) => {
        campo.addEventListener('input', function() {
            if (campo.value.length > 10) {
                campo.value = campo.value.slice(0, 10);
            }
        });
    };

    limitarLongitud(document.getElementById('matricula'));
    limitarLongitud(document.getElementById('telefono1'));
    limitarLongitud(document.getElementById('telefono2'));
});
</script>
@endsection

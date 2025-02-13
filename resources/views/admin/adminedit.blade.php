@extends('layouts.app')

@section('content')
    <h1>Editar Alumno</h1>

    <form action="{{ route('admin.adminupdate', $alumno->id) }}" method="POST" class="p-3 border rounded shadow-sm">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-4">
            <label class="form-label">Matrícula</label>
            <input type="text" name="matricula" value="{{ $alumno->matricula }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" value="{{ $alumno->nombre }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" value="{{ $alumno->correo }}" class="form-control">
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label class="form-label">Apellido Paterno</label>
            <input type="text" name="apellidopaterno" value="{{ $alumno->apellidopaterno }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Apellido Materno</label>
            <input type="text" name="apellidomaterno" value="{{ $alumno->apellidomaterno }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Correo Familiar</label>
            <input type="email" name="correo_familia" value="{{ $alumno->correo_familia }}" class="form-control">
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label class="form-label">Contacto 1</label>
            <input type="text" name="contacto1nombre" value="{{ $alumno->contacto1nombre }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Teléfono 1</label>
            <input type="text" name="telefono1" value="{{ $alumno->telefono1 }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Contacto 2</label>
            <input type="text" name="contacto2nombre" value="{{ $alumno->contacto2nombre }}" class="form-control">
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label class="form-label">Teléfono 2</label>
            <input type="text" name="telefono2" value="{{ $alumno->telefono2 }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Usuario Classroom</label>
            <input type="text" name="usuario_classroom" value="{{ $alumno->usuario_classroom }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Contraseña Classroom</label>
            <input type="text" name="contraseña_classroom" value="{{ $alumno->contraseña_classroom }}" class="form-control">
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label class="form-label">Usuario Moodle</label>
            <input type="text" name="usuario_moodle" value="{{ $alumno->usuario_moodle }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Contraseña Moodle</label>
            <input type="text" name="contraseña_moodle" value="{{ $alumno->contraseña_moodle }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Usuario Mathletics</label>
            <input type="text" name="usuario_mathletics" value="{{ $alumno->usuario_mathletics }}" class="form-control">
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label class="form-label">Contraseña Mathletics</label>
            <input type="text" name="contraseña_mathletics" value="{{ $alumno->contraseña_mathletics }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Usuario HMH</label>
            <input type="text" name="usuario_hmh" value="{{ $alumno->usuario_hmh }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Contraseña HMH</label>
            <input type="text" name="contraseña_hmh" value="{{ $alumno->contraseña_hmh }}" class="form-control">
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label class="form-label">Usuario Progrentis</label>
            <input type="text" name="usuario_progrentis" value="{{ $alumno->usuario_progrentis }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Contraseña Progrentis</label>
            <input type="text" name="contraseña_progrentis" value="{{ $alumno->contraseña_progrentis }}" class="form-control">
        </div>
         <!-- Select dinámico para nivel educativo -->
    <div class="row mt-2">
        <div class="col-md-4">
            <label class="form-label">Nivel Educativo</label>
            <select class="form-control" id="nivel_educativo_id" name="nivel_educativo_id" onchange="actualizarGrados()" required>
                <option value="">Selecciona un nivel educativo</option>
                @foreach ($niveles as $nivel)
                    <option value="{{ $nivel->id }}" {{ old('nivel_educativo_id', $alumno->nivel_educativo_id) == $nivel->id ? 'selected' : '' }}>
                        {{ $nivel->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Select dinámico para grado -->
        <div class="col-md-4">
            <label class="form-label">Grado</label>
            <select class="form-control" id="grado_id" name="grado_id" required>
                <option value="">Selecciona un grado</option>
                @foreach ($grados as $grado)
                    <option value="{{ $grado->id }}" {{ old('grado_id', $alumno->grado_id) == $grado->id ? 'selected' : '' }}>
                        {{ $grado->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

        
        <div class="col-md-4">
            <label class="form-label">Sección</label>
            <input type="text" name="seccion" value="{{ $alumno->seccion }}" class="form-control">
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label class="form-label">Fecha Inscripción</label>
            <input type="date" name="fecha_inscripcion" value="{{ $alumno->fecha_inscripcion }}" class="form-control">
        </div>
    </div>

    <div class="text-center mt-3">
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
<script>
    // Actualizar grados basado en el nivel educativo seleccionado
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

                // Seleccionar el grado que fue seleccionado previamente (si existe)
                var gradoSeleccionado = "{{ old('grado_id', $alumno->grado_id) }}";
                if (grado.id == gradoSeleccionado) {
                    option.selected = true;
                }

                gradoSelect.appendChild(option);
            });
        }
    }

    // Ejecutar la función cuando la página cargue si ya hay un nivel seleccionado
    window.onload = function() {
        if ("{{ old('nivel_educativo_id', $alumno->nivel_educativo_id) }}") {
            actualizarGrados();
        }
    };
</script>


@endsection

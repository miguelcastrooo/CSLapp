@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="text-center mb-4">
        <h1 class="display-4 text-primary">Ver Alumno</h1>
        <p class="lead">Consulta los datos del alumno a continuación:</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

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

            <!-- Botones de acción -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary" id="updateBtn" style="display:none;"><i class="fas fa-save"></i> Actualizar Alumno</button>
                <a href="{{ route(auth()->user()->hasRole('ControlEscolar') ? 'capturista.selectsearch' : 'admin.selectadmin') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>

                <button type="button" class="btn btn-warning" id="editBtn"><i class="fas fa-pencil-alt"></i> Editar</button>
            </div>

            <!-- Información del Alumno en columnas -->
<div class="col-md-12">
<br><br>
    <div class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
        <div class="card-body">
            <h5 class="card-title text-primary">Datos del Alumno</h5>

            <!-- Los campos en una sola fila -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="matricula" class="form-label">Matrícula</label>
                    <input type="number" class="form-control" id="matricula" name="matricula" value="{{ old('matricula', $alumno->matricula) }}" required min="1" readonly>
                </div>

                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $alumno->nombre) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo letras y espacios" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="apellidopaterno" class="form-label">Apellido Paterno</label>
                    <input type="text" class="form-control" id="apellidopaterno" name="apellidopaterno" value="{{ old('apellidopaterno', $alumno->apellidopaterno) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" readonly>
                </div>

                <div class="col-md-6">
                    <label for="apellidomaterno" class="form-label">Apellido Materno</label>
                    <input type="text" class="form-control" id="apellidomaterno" name="apellidomaterno" value="{{ old('apellidomaterno', $alumno->apellidomaterno) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" readonly>
                </div>
            </div>

            <div class="row mb-3">

            @if(Auth::user()->hasRole('SuperAdmin'))
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nivel_educativo_id" class="form-label">Nivel Educativo</label>
                        <select class="form-select" id="nivel_educativo_id" name="nivel_educativo_id" required onchange="cargarGrados(this.value)">
                            <option value="">Selecciona un nivel educativo</option>
                            @foreach($nivel_id as $nivel)
                                <option value="{{ $nivel->id }}" 
                                    {{ old('nivel_educativo_id', $alumno->nivel_educativo_id) == $nivel->id ? 'selected' : '' }}>
                                    {{ $nivel->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Campo de sección -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="seccion" class="form-label">Sección</label>
                        <select class="form-select" id="seccion" name="seccion" required>
                            <option value="">Selecciona una sección</option>
                            <option value="A" {{ old('seccion', $alumno->seccion) == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('seccion', $alumno->seccion) == 'B' ? 'selected' : '' }}>B</option>
                        </select>
                    </div>
                </div>
            @endif

            <!-- Campo de grado -->
            <div class="col-md-6">
                <label for="grado_id" class="form-label">Grado</label>
                <select class="form-select" id="grado_id" name="grado_id" required disabled>
                    <option value="">Selecciona un grado</option>
                    @if($grados && $grados->isNotEmpty())
                        @foreach ($grados as $grado)
                            <option value="{{ $grado->id }}" {{ old('grado_id', $alumno->grado_id) == $grado->id ? 'selected' : '' }}>
                                {{ $grado->nombre }}
                            </option>
                        @endforeach
                    @else
                        <option value="">No hay grados disponibles</option>
                    @endif
                </select>
            </div>

            <div class="row mb-3">
                <!-- Fechas de inscripción e inicio, en la misma fila -->
                <div class="col-md-6">
                    <label for="fecha_inscripcion" class="form-label">Fecha de Inscripción</label>
                    <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', $alumno->fecha_inscripcion) }}" required readonly>
                </div>

                <div class="col-md-6">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $alumno->fecha_inicio) }}" required readonly>
                </div>
            </div>

        </div>
    </div>
</div>



                <!-- Información de Contactos dentro de una tarjeta -->
                <div class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Información de Contactos</h5>

                        @foreach ($contactos as $index => $contacto)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="contacto{{ $index + 1 }}nombre" class="form-label">Nombre del Contacto {{ $index + 1 }}</label>
                                    <input type="text" class="form-control" id="contacto{{ $index + 1 }}nombre" name="contacto{{ $index + 1 }}nombre" value="{{ old('contacto'.$index.'nombre', $contacto->nombre) }}" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label for="telefono{{ $index + 1 }}" class="form-label">Teléfono del Contacto {{ $index + 1 }}</label>
                                    <input type="number" class="form-control" id="telefono{{ $index + 1 }}" name="telefono{{ $index + 1 }}" value="{{ old('telefono'.$index, $contacto->telefono) }}" maxlength="12" pattern="\d{12}" title="Debe ser un número de 12 dígitos" readonly>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="correo{{ $index + 1 }}" class="form-label">Correo del Contacto {{ $index + 1 }}</label>
                                    <input type="email" class="form-control" id="correo{{ $index + 1 }}" name="correo{{ $index + 1 }}" value="{{ old('correo'.$index, $contacto->correo) }}" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label for="contacto{{ $index + 1 }}tipo" class="form-label">Tipo de Contacto</label>
                                    <select class="form-select" id="contacto{{ $index + 1 }}tipo" name="contacto{{ $index + 1 }}tipo" disabled>
                                        <option value="">Selecciona tipo</option>
                                        <option value="madre" {{ old('contacto'.($index+1).'tipo', $contacto->tipo_contacto) == 'Madre' ? 'selected' : '' }}>Madre</option>
                                        <option value="padre" {{ old('contacto'.($index+1).'tipo', $contacto->tipo_contacto) == 'Padre' ? 'selected' : '' }}>Padre</option>
                                        <option value="tutor" {{ old('contacto'.($index+1).'tipo', $contacto->tipo_contacto) == 'Tutor' ? 'selected' : '' }}>Tutor</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if(Auth::user()->hasRole('SuperAdmin'))
    <div class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
        <div class="card-body">
            <h5 class="card-title text-primary">Datos de Usuario</h5>

            <!-- Campos para el usuario y contraseñas -->

            @if($alumno->nivel_educativo_id == 1) <!-- Preescolar -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_classroom" class="form-label">Usuario Classroom</label>
                        <input type="text" class="form-control" id="usuario_classroom" name="usuario_classroom" value="{{ old('usuario_classroom', $alumno->usuario_classroom) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_classroom" class="form-label">Contraseña Classroom</label>
                        <input type="text" class="form-control" id="contraseña_classroom" name="contraseña_classroom" value="{{ old('contraseña_classroom', $alumno->contraseña_classroom) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_moodle" class="form-label">Usuario Moodle</label>
                        <input type="text" class="form-control" id="usuario_moodle" name="usuario_moodle" value="{{ old('usuario_moodle', $alumno->usuario_moodle) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_moodle" class="form-label">Contraseña Moodle</label>
                        <input type="text" class="form-control" id="contraseña_moodle" name="contraseña_moodle" value="{{ old('contraseña_moodle', $alumno->contraseña_moodle) }}">
                    </div>
                </div>
            @elseif($alumno->nivel_educativo_id == 2) <!-- Primaria Baja -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_classroom" class="form-label">Usuario Classroom</label>
                        <input type="text" class="form-control" id="usuario_classroom" name="usuario_classroom" value="{{ old('usuario_classroom', $alumno->usuario_classroom) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_classroom" class="form-label">Contraseña Classroom</label>
                        <input type="text" class="form-control" id="contraseña_classroom" name="contraseña_classroom" value="{{ old('contraseña_classroom', $alumno->contraseña_classroom) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_moodle" class="form-label">Usuario Moodle</label>
                        <input type="text" class="form-control" id="usuario_moodle" name="usuario_moodle" value="{{ old('usuario_moodle', $alumno->usuario_moodle) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_moodle" class="form-label">Contraseña Moodle</label>
                        <input type="text" class="form-control" id="contraseña_moodle" name="contraseña_moodle" value="{{ old('contraseña_moodle', $alumno->contraseña_moodle) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_hmh" class="form-label">Usuario HMH</label>
                        <input type="text" class="form-control" id="usuario_hmh" name="usuario_hmh" value="{{ old('usuario_hmh', $alumno->usuario_hmh) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_hmh" class="form-label">Contraseña HMH</label>
                        <input type="text" class="form-control" id="contraseña_hmh" name="contraseña_hmh" value="{{ old('contraseña_hmh', $alumno->contraseña_hmh) }}" >
                    </div>
                </div>
            @elseif($alumno->nivel_educativo_id == 3) <!-- Primaria Alta -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_classroom" class="form-label">Usuario Classroom</label>
                        <input type="text" class="form-control" id="usuario_classroom" name="usuario_classroom" value="{{ old('usuario_classroom', $alumno->usuario_classroom) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_classroom" class="form-label">Contraseña Classroom</label>
                        <input type="text" class="form-control" id="contraseña_classroom" name="contraseña_classroom" value="{{ old('contraseña_classroom', $alumno->contraseña_classroom) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_moodle" class="form-label">Usuario Moodle</label>
                        <input type="text" class="form-control" id="usuario_moodle" name="usuario_moodle" value="{{ old('usuario_moodle', $alumno->usuario_moodle) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_moodle" class="form-label">Contraseña Moodle</label>
                        <input type="text" class="form-control" id="contraseña_moodle" name="contraseña_moodle" value="{{ old('contraseña_moodle', $alumno->contraseña_moodle) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_hmh" class="form-label">Usuario HMH</label>
                        <input type="text" class="form-control" id="usuario_hmh" name="usuario_hmh" value="{{ old('usuario_hmh', $alumno->usuario_hmh) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_hmh" class="form-label">Contraseña HMH</label>
                        <input type="text" class="form-control" id="contraseña_hmh" name="contraseña_hmh" value="{{ old('contraseña_hmh', $alumno->contraseña_hmh) }}" >
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_mathletics" class="form-label">Usuario Mathletics</label>
                        <input type="text" class="form-control" id="usuario_mathletics" name="usuario_mathletics" value="{{ old('usuario_mathletics', $alumno->usuario_mathletics) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_mathletics" class="form-label">Contraseña Mathletics</label>
                        <input type="text" class="form-control" id="contraseña_mathletics" name="contraseña_mathletics" value="{{ old('contraseña_mathletics', $alumno->contraseña_mathletics) }}" >
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_progrentis" class="form-label">Usuario Progrentis</label>
                        <input type="text" class="form-control" id="usuario_progrentis" name="usuario_progrentis" value="{{ old('usuario_progrentis', $alumno->usuario_progrentis) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_progrentis" class="form-label">Contraseña Progrentis</label>
                        <input type="text" class="form-control" id="contraseña_progrentis" name="contraseña_progrentis" value="{{ old('contraseña_progrentis', $alumno->contraseña_progrentis) }}" >
                    </div>
                </div>
            @elseif($alumno->nivel_educativo_id == 4) <!-- Secundaria -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_classroom" class="form-label">Usuario Classroom</label>
                        <input type="text" class="form-control" id="usuario_classroom" name="usuario_classroom" value="{{ old('usuario_classroom', $alumno->usuario_classroom) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_classroom" class="form-label">Contraseña Classroom</label>
                        <input type="text" class="form-control" id="contraseña_classroom" name="contraseña_classroom" value="{{ old('contraseña_classroom', $alumno->contraseña_classroom) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_moodle" class="form-label">Usuario Moodle</label>
                        <input type="text" class="form-control" id="usuario_moodle" name="usuario_moodle" value="{{ old('usuario_moodle', $alumno->usuario_moodle) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_moodle" class="form-label">Contraseña Moodle</label>
                        <input type="text" class="form-control" id="contraseña_moodle" name="contraseña_moodle" value="{{ old('contraseña_moodle', $alumno->contraseña_moodle) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="usuario_mathletics" class="form-label">Usuario Mathletics</label>
                        <input type="text" class="form-control" id="usuario_mathletics" name="usuario_mathletics" value="{{ old('usuario_mathletics', $alumno->usuario_mathletics) }}" >
                    </div>

                    <div class="col-md-6">
                        <label for="contraseña_mathletics" class="form-label">Contraseña Mathletics</label>
                        <input type="text" class="form-control" id="contraseña_mathletics" name="contraseña_mathletics" value="{{ old('contraseña_mathletics', $alumno->contraseña_mathletics) }}" >
                    </div>
                </div>
            @endif

        </div>
    </div>
@endif




       
    </form>
</div>
<br><br>
<!-- Script para habilitar los campos y mostrar los botones correctos -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Funcionalidad de habilitar campos cuando el SuperAdmin hace click en "Editar"
    document.getElementById('editBtn').addEventListener('click', function() {
        // Hacer los campos input editables
        let fields = document.querySelectorAll('input');
        fields.forEach(field => field.removeAttribute('readonly'));

        // Hacer los campos select habilitables
        let selects = document.querySelectorAll('select');
        selects.forEach(select => select.removeAttribute('disabled'));

        // Mostrar el botón de actualizar
        document.getElementById('updateBtn').style.display = 'inline-block';
        this.style.display = 'none'; // Ocultar el botón de editar
    });

    // Bloquear campos por defecto si el usuario es SuperAdmin
    @if(Auth::user()->hasRole('SuperAdmin'))
        document.addEventListener('DOMContentLoaded', function() {
            // Bloquear todos los campos input y select
            let fields = document.querySelectorAll('input');
            fields.forEach(field => field.setAttribute('readonly', true));

            let selects = document.querySelectorAll('select');
            selects.forEach(select => select.setAttribute('disabled', true));

            // Mostrar el botón de editar
            document.getElementById('editBtn').style.display = 'inline-block';
        });
    @endif

   // Función para cargar los grados dependiendo del nivel educativo seleccionado
function cargarGrados(nivelEducativoId) {
    let gradoSelect = document.getElementById('grado_id');

    // Limpiar las opciones previas
    gradoSelect.innerHTML = '<option value="" disabled selected>Seleccione un Grado</option>';

    // Verifica si se ha seleccionado un nivel educativo
    if (nivelEducativoId) {
        // Hacer una llamada AJAX para obtener los grados del nivel educativo seleccionado
        fetch(`/grados/${nivelEducativoId}`)
            .then(response => response.json())
            .then(data => {
                // Habilitar el select de grados
                gradoSelect.removeAttribute('disabled');

                // Si se obtienen grados, agregar las opciones al select
                if (data && data.length > 0) {
                    data.forEach(grado => {
                        let option = document.createElement('option');
                        option.value = grado.id;
                        option.textContent = grado.nombre;
                        gradoSelect.appendChild(option);
                    });
                } else {
                    // Si no se encuentran grados, agregar un mensaje
                    let option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No se encontraron grados';
                    gradoSelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Error cargando los grados:', error);
            });
    } else {
        // Si no se selecciona un nivel, deshabilitar el select de grados y limpiarlo
        gradoSelect.setAttribute('disabled', true);
        gradoSelect.innerHTML = '<option value="" disabled selected>Seleccione un Grado</option>';
    }
}
</script>
@endsection

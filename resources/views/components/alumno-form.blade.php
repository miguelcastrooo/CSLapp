@extends('layouts.app')

@section('content')


<div class="container">
    <h1>{{ isset($alumno) ? 'Editar Alumno' : 'Agregar Alumno' }}</h1>
    <p>Ingresa los datos del alumno a continuación:</p>

    <div class="mb-4">
            <input type="text" class="form-control" id="searchInput" name="search" placeholder="Buscar por matrícula, nombre..." value="{{ request('search') }}" onkeyup="searchAlumnos()">
    </div>

    <!-- Sidebar con iconos -->
    <div class="d-flex">
        <div class="sidebar p-3" style="width: 200px; background-color: #f8f9fa; border-right: 1px solid #ddd;">
            <h5 class="mb-4">Opciones</h5>
            <ul class="list-unstyled">
                <!-- Botón de Editar -->
                <li class="mb-3">
                    <button id="editarBtn" class="btn btn-warning w-100" onclick="habilitarCampos()">
                        <i class="bi bi-pencil-square"></i> Editar
                    </button>
                </li>
                <!-- Botón de Bloquear Campos -->
                <li class="mb-3">
                    <button id="bloquearBtn" class="btn btn-danger w-100" onclick="bloquearCampos()">
                        <i class="bi bi-lock"></i> Bloquear Campos
                    </button>
                </li>
            </ul>
        </div>

        <!-- Formulario -->
        <div class="form-container ms-3" style="flex-grow: 1;">
            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <form id="formulario" action="{{ isset($alumno) ? route('alumnos.update', $alumno->id) : '#' }}" method="POST">
            @csrf
                @if (isset($alumno))
                    @method('PUT')
                @endif

                <!-- Campos del formulario -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="matricula">Matrícula</label>
                        <input type="number" class="form-control" id="matricula" name="matricula" value="{{ old('matricula', isset($alumno) ? $alumno->matricula : '') }}" required min="1" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', isset($alumno) ? $alumno->nombre : '') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo letras y espacios" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="apellidopaterno">Apellido Paterno</label>
                        <input type="text" class="form-control" id="apellidopaterno" name="apellidopaterno" value="{{ old('apellidopaterno', isset($alumno) ? $alumno->apellidopaterno : '') }}" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="apellidomaterno">Apellido Materno</label>
                        <input type="text" class="form-control" id="apellidomaterno" name="apellidomaterno" value="{{ old('apellidomaterno', isset($alumno) ? $alumno->apellidomaterno : '') }}" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="contacto1nombre">Nombre del Contacto 1</label>
                        <input type="text" class="form-control" id="contacto1nombre" name="contacto1nombre" value="{{ old('contacto1nombre', isset($alumno) ? $alumno->contacto1nombre : '') }}" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="telefono1">Teléfono 1</label>
                        <input type="tel" class="form-control" id="telefono1" name="telefono1" value="{{ old('telefono1', isset($alumno) ? $alumno->telefono1 : '') }}" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="correo_familia">Correo Familiar</label>
                        <input type="email" class="form-control" id="correo_familia" name="correo_familia" value="{{ old('correo_familia', isset($alumno) ? $alumno->correo_familia : '') }}" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="contacto2nombre">Nombre del Contacto 2</label>
                        <input type="text" class="form-control" id="contacto2nombre" name="contacto2nombre" value="{{ old('contacto2nombre', isset($alumno) ? $alumno->contacto2nombre : '') }}" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="telefono2">Teléfono 2</label>
                        <input type="tel" class="form-control" id="telefono2" name="telefono2" value="{{ old('telefono2', isset($alumno) ? $alumno->telefono2 : '') }}" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nivel_educativo">Nivel Educativo</label>
                        <input type="text" class="form-control" id="nivel_educativo" name="nivel_educativo" value="{{ old('nivel_educativo', isset($alumno) ? $alumno->nivel_educativo : '') }}" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="grado">Grado</label>
                        <input type="text" class="form-control" id="grado" name="grado" value="{{ old('grado', isset($alumno) ? $alumno->grado : '') }}" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="fecha_inscripcion">Fecha de Inscripción</label>
                        <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', isset($alumno) ? $alumno->fecha_inscripcion : '') }}" disabled>
                    </div>

                </div>
                
                <!-- Botón de Guardar -->
                <div class="row mb-3">
                    <div class="col">
                        <button type="submit" id="guardarBtn" class="btn btn-success w-100" disabled>
                            <i class="bi bi-save"></i> Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Función para habilitar los campos
    function habilitarCampos() {
        let fields = document.querySelectorAll('input, select'); // Seleccionamos todos los campos del formulario
        fields.forEach(function(field) {
            field.disabled = false; // Habilitamos los campos
        });

        document.getElementById('guardarBtn').disabled = false; // Habilitamos el botón de guardar
        document.getElementById('editarBtn').disabled = true; // Deshabilitamos el botón de editar después de presionarlo
    }

    // Función para bloquear los campos nuevamente
    function bloquearCampos() {
        let fields = document.querySelectorAll('input, select');
        fields.forEach(function(field) {
            field.disabled = true; // Bloqueamos los campos
        });

        document.getElementById('guardarBtn').disabled = true; // Deshabilitamos el botón de guardar
        document.getElementById('editarBtn').disabled = false; // Habilitamos el botón de editar
    }

    // Función para realizar la búsqueda mientras se escribe
    function searchAlumnos() {
        let searchQuery = document.getElementById('searchInput').value; // Obtener la consulta de búsqueda
        if (searchQuery === "") {
            limpiarCampos(); // Limpiar campos si no hay búsqueda
        } else {
            fetch("{{ route('alumnos.search') }}?search=" + searchQuery)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        // Llenar los campos con los datos encontrados si existen
                        document.getElementById('matricula').value = data.matricula || '';
                        document.getElementById('nombre').value = data.nombre || '';
                        document.getElementById('apellidopaterno').value = data.apellidopaterno || '';
                        document.getElementById('apellidomaterno').value = data.apellidomaterno || '';
                        document.getElementById('contacto1nombre').value = data.contacto1nombre || '';
                        document.getElementById('telefono1').value = data.telefono1 || '';
                        document.getElementById('correo_familia').value = data.correo_familia || '';
                        document.getElementById('contacto2nombre').value = data.contacto2nombre || '';
                        document.getElementById('telefono2').value = data.telefono2 || '';
                        document.getElementById('fecha_inscripcion').value = data.fecha_inscripcion || '';
                        
                        // Asignar los nombres en lugar de los IDs
                        document.getElementById('nivel_educativo').value = data.nivel_educativo_nombre || '';
                        document.getElementById('grado').value = data.grado_nombre || '';
                    } else {
                        limpiarCampos(); // Limpiar campos si no se encuentran resultados
                    }
                });
        }
    }

    // Limpiar los campos
    function limpiarCampos() {
        document.getElementById('matricula').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('apellidopaterno').value = '';
        document.getElementById('apellidomaterno').value = '';
        document.getElementById('contacto1nombre').value = '';
        document.getElementById('telefono1').value = '';
        document.getElementById('correo_familia').value = '';
        document.getElementById('contacto2nombre').value = '';
        document.getElementById('telefono2').value = '';
        document.getElementById('nivel_educativo').value = '';
        document.getElementById('grado').value = '';
        document.getElementById('fecha_inscripcion').value = '';
    }

    // Función para guardar o actualizar los datos
    function guardarActualizarAlumno(event) {
        event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

        // Crear un objeto con los datos del formulario
        const formData = new FormData(document.getElementById('student-form'));

        // Verificar si estamos actualizando (es decir, si ya hay un ID de matrícula)
        const alumnoId = document.getElementById('matricula').value;

        const url = alumnoId ? `{{ route('alumnos.update', ['alumno' => ':id']) }}`.replace(':id', alumnoId) : "{{ route('alumnos.store') }}";
        const method = alumnoId ? 'PUT' : 'POST'; // Usamos PUT si existe un ID, de lo contrario usamos POST

        // Enviar los datos a través de AJAX (Fetch API)
        fetch(url, {
            method: method,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar mensaje de éxito
                mostrarMensajeExito();

                // Limpiar los campos después de guardar o actualizar
                limpiarCampos();

                // Volver a bloquear los campos
                bloquearCampos();
            } else {
                alert("Hubo un error al guardar los datos.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Hubo un error con la solicitud.");
        });
    }

    // Función para mostrar el mensaje de éxito
    function mostrarMensajeExito() {
        const successMessage = document.getElementById('success-message');
        successMessage.style.display = 'block'; // Mostrar el mensaje
        setTimeout(() => {
            successMessage.style.display = 'none'; // Ocultar el mensaje después de 3 segundos
        }, 3000);
    }

    // Asignamos el evento de submit al formulario
    document.getElementById('student-form').addEventListener('submit', guardarActualizarAlumno);
</script>

<!-- Mensaje de éxito -->
<div id="success-message" style="display:none; color: green; font-weight: bold;">
    ¡Datos guardados o actualizados correctamente!
</div>

@endsection
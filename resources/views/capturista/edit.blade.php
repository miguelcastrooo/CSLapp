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

        
            <!-- Sección 2: Datos de Nacimiento y Edad -->
<div class="section mb-4">
    <br>
    <h6 class="text-secondary">Datos de Nacimiento y Edad</h6>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="fecha_nacimiento" class="form-label"><strong>Fecha de Nacimiento</strong></label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ $alumno->fecha_nacimiento }}" required placeholder="Ej. 2000-01-01">
        </div>

        <div class="col-md-6">
            <label for="edad_anios" class="form-label"><strong>Edad (Años)</strong></label>
            <input type="number" class="form-control" id="edad_anios" name="edad_anios" value="{{ $alumno->edad_anios }}" required min="1" max="150" placeholder="Edad en años">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="edad_meses" class="form-label"><strong>Edad (Meses)</strong></label>
            <input type="number" class="form-control" id="edad_meses" name="edad_meses" value="{{ $alumno->edad_meses }}" required min="0" max="150" placeholder="Edad en meses">
        </div>

        <div class="col-md-6">
            <label for="sexo" class="form-label"><strong>Sexo</strong></label>
            <select class="form-select" id="sexo" name="sexo" required>
                <option>Selecciona una opcion</option>
                <option value="Masculino" {{ $alumno->sexo == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                <option value="Femenino" {{ $alumno->sexo == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                <option value="Sin Definir" {{ $alumno->sexo == 'Sin Definir' ? 'selected' : '' }}>Sin Definir</option>
            </select>
        </div>
    </div>
</div>

<!-- Sección 3: Información de Contacto -->
<div class="section mb-4">
    <h6 class="text-secondary">Información de Contacto</h6>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="cp" class="form-label"><strong>Código Postal (C.P.)</strong></label>
            <input type="number" class="form-control" id="cp" name="cp" value="{{ $alumno->cp }}" required pattern="[0-9]{5}" title="El código postal debe ser un número de 5 dígitos.">
        </div>

        <div class="col-md-6">
            <label for="ciudad" class="form-label"><strong>Ciudad</strong></label>
            <input type="text" class="form-control" id="ciudad" name="ciudad" value="{{ $alumno->ciudad }}">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="estado" class="form-label"><strong>Estado</strong></label>
            <input type="text" class="form-control" id="estado" name="estado" value="{{ $alumno->estado }}">
        </div>

        <div class="col-md-6">
            <label for="lugar_nacimiento" class="form-label"><strong>Lugar de Nacimiento</strong></label>
            <input type="text" class="form-control" id="lugar_nacimiento" name="lugar_nacimiento" value="{{ $alumno->lugar_nacimiento }}">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="domicilio" class="form-label"><strong>Domicilio</strong></label>
            <input type="text" class="form-control" id="domicilio" name="domicilio" value="{{ $alumno->domicilio }}" placeholder="Ej. Calle 123, Número 45">
        </div>

        <div class="col-md-6">
            <label for="colonia" class="form-label"><strong>Colonia</strong></label>
            <input type="text" class="form-control" id="colonia" name="colonia" value="{{ $alumno->colonia }}" placeholder="Ej. Centro, Las Lomas">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="cerrada" class="form-label"><strong>Cerrada</strong></label>
            <input type="text" class="form-control" id="cerrada" name="cerrada" value="{{ $alumno->cerrada }}" placeholder="Ej. Cerrada del Bosque">
        </div>

        <div class="col-md-6">
            <label for="numero_calle" class="form-label"><strong>Número de Calle o Departamento</strong></label>
            <input type="text" class="form-control" id="no_domicilio" name="no_domicilio" value="{{ $alumno->no_domicilio }}" placeholder="Ej. 45 o A2" pattern="[A-Za-z0-9]+">
        </div>
    </div>

    <div class="section mb-4">
    <h6 class="text-secondary">Datos Familiares y Salud</h6>
    
    <!-- Primer Hermano (si ya existe en la base de datos) -->
    @if(isset($alumno->hermano1nombre) && $alumno->hermano1nombre)
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="hermano1nombre" class="form-label"><strong>Nombre del Primer Hermano</strong></label>
            <input type="text" class="form-control" id="hermano1nombre" name="hermano1nombre" 
                   value="{{ old('hermano1nombre', $alumno->hermano1nombre) }}" 
                   pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="Ej. Juan Pérez">
        </div>

        <div class="col-md-6">
            <label for="hermano1edad" class="form-label"><strong>Edad del Primer Hermano</strong></label>
            <input type="number" class="form-control" id="hermano1edad" name="hermano1edad" 
                   value="{{ old('hermano1edad', $alumno->hermano1edad) }}" 
                   min="0" max="50" required>
        </div>
    </div>
    @endif

    <!-- Hermanos Adicionales -->
    <div id="hermanos-adicionales">
        @foreach($alumno->hermanos as $index => $hermano) <!-- Usamos la relación de hermanos -->
            <div class="row mb-3" id="hermano{{ $index + 1 }}"> <!-- Empezamos desde el segundo hermano -->
                <div class="col-md-6">
                    <label for="hermano{{ $index + 1 }}nombre" class="form-label"><strong>Nombre Completo del Hermano </strong></label>
                    <input type="text" class="form-control" id="hermano{{ $index + 1 }}nombre" name="hermanos[{{ $index }}][nombre]" 
                           value="{{ old('hermanos[' . $index . '][nombre]', $hermano->nombre) }}" 
                           pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="Ej. Pedro Gómez">
                </div>

                <div class="col-md-6">
                    <label for="hermano{{ $index + 1 }}edad" class="form-label"><strong>Edad del Hermano </strong></label>
                    <input type="number" class="form-control" id="hermano{{ $index + 1 }}edad" name="hermanos[{{ $index }}][edad]" 
                           value="{{ old('hermanos[' . $index . '][edad]', $hermano->edad) }}" 
                           min="0" max="50">
                </div>
            </div>
        @endforeach
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="enfermedades_alergias" class="form-label"><strong>Enfermedades o Alergias</strong></label>
            <input type="text" class="form-control" id="enfermedades_alergias" name="enfermedades_alergias" 
                   value="{{ old('enfermedades_alergias', $alumno->enfermedades_alergias) }}">
        </div>

        <div class="col-md-6">
            <label for="pediatra_nombre" class="form-label"><strong>Nombre del Pediatra</strong></label>
            <input type="text" class="form-control" id="pediatra_nombre" name="pediatra_nombre" 
                   value="{{ old('pediatra_nombre', $alumno->pediatra_nombre) }}">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="pediatra_telefono" class="form-label"><strong>Teléfono del Pediatra</strong></label>
            <input type="number" class="form-control" id="pediatra_telefono" name="pediatra_telefono" 
                   value="{{ old('pediatra_telefono', $alumno->pediatra_telefono) }}">
        </div>
    </div>
</div>

                
            </div>
                <!-- Fechas de inscripción e inicio, en la misma fila -->
                <div class="row mb-3">
                         <h6 class="text-secondary">Fechas de Inscripción</h6>
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

        @if (Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('CoordinacionPreescolar') || Auth::user()->hasRole('CoordinacionPrimaria') || Auth::user()->hasRole('CoordinacionSecundaria'))
        <div class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
        <div class="card-body">
            <h5 class="card-title text-primary">Datos de Usuario</h5>

            @php
                // Define las plataformas por nivel educativo
                $plataformasPorNivel = [
                    'preescolar' => [1, 2], 
                    'primaria_baja' => [1, 2, 3], 
                    'primaria_alta' => [1, 2, 3, 4, 5], 
                    'secundaria' => [1, 2, 4] 
                ];

                // Determina las plataformas que deben mostrarse en función del nivel educativo
                $nivel = $alumno->nivel_educativo_id;
                $plataformasDisponibles = $plataformasPorNivel[
                    ['preescolar', 'primaria_baja', 'primaria_alta', 'secundaria'][$nivel - 1] ?? []
                ];
            @endphp

            @foreach($plataformasDisponibles as $plataformaId)
                @php
                    // Obtener datos de la plataforma
                    $plataformaExistente = $alumno->alumnoPlataforma->firstWhere('plataforma_id', $plataformaId);
                    $plataforma = \App\Models\Plataforma::find($plataformaId);
                    $plataformaNombre = $plataforma->nombre ?? 'Plataforma no asignada';
                @endphp

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="plataformas_{{ $plataformaId }}_usuario" class="form-label">Usuario {{ ucfirst($plataformaNombre) }}</label>
                        <input type="text" class="form-control" 
                            id="plataformas_{{ $plataformaId }}_usuario" 
                            name="plataformas[{{ $plataformaId }}][usuario]" 
                            value="{{ old('plataformas.'.$plataformaId.'.usuario', $plataformaExistente->usuario ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="plataformas_{{ $plataformaId }}_contraseña" class="form-label">Contraseña {{ ucfirst($plataformaNombre) }}</label>
                        <input type="text" class="form-control" 
                            id="plataformas_{{ $plataformaId }}_contraseña" 
                            name="plataformas[{{ $plataformaId }}][contraseña]" 
                            value="{{ old('plataformas.'.$plataformaId.'.contraseña', $plataformaExistente->contraseña ?? '') }}">
                    </div>
                </div>
            @endforeach
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

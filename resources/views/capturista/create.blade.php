@extends('layouts.app')

@section('content')
<div class="container-xl csl-student-form">
    <div class="csl-module-header">
        <div class="d-flex align-items-center gap-3">
            <span class="csl-choice-icon"><i class="ti ti-user-plus"></i></span>
            <div>
                <h1>Registro de alumno</h1>
                <p>Captura la información académica, familiar y de contacto.</p>
            </div>
        </div>
    </div>
    <nav class="csl-form-nav" aria-label="Secciones del registro">
        <a href="#datos-alumno"><i class="ti ti-user"></i> Datos del alumno</a>
        <a href="#contactos-emergencia"><i class="ti ti-phone"></i> Contactos</a>
        <a href="#familiares"><i class="ti ti-users"></i> Familiares</a>
    </nav>

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

    <form id="studentCreateForm" action="{{ route('alumnos.store') }}" method="POST" class="csl-student-form-body">
        @csrf
        <div class="row">
<!-- Información del Alumno en columnas -->
<div class="col-md-12">
    <div id="datos-alumno" class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
        <div class="card-body">
            <h5 class="card-title text-primary">Datos del Alumno</h5>

            <input type="hidden" name="nivel_educativo_id" value="{{ $nivel_id }}">

            <!-- Sección 1: Datos Básicos -->
            <div class="section mb-4">
                <div class="row mb-3">
                    
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="nombre" class="form-label"><strong>Nombre</strong></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" inputmode="text" style="text-transform: capitalize;" placeholder="Escribe el nombre" title="El nombre solo debe contener letras y espacios y comenzar con mayúscula.">
                    </div>

                <div class="col-md-4">
                    <label for="apellidopaterno" class="form-label"><strong>Apellido Paterno</strong></label>
                    <input type="text" class="form-control" id="apellidopaterno" name="apellidopaterno" value="{{ old('apellidopaterno') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" inputmode="text" style="text-transform: capitalize;" placeholder="Escribe su Apellido Paterno " title="El apellido paterno solo debe contener letras y espacios y comenzar con mayúscula.">
                </div>
                    <div class="col-md-4">
                        <label for="apellidomaterno" class="form-label"><strong>Apellido Materno</strong></label>
                        <input type="text" class="form-control" id="apellidomaterno" name="apellidomaterno" value="{{ old('apellidomaterno') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" style="text-transform: capitalize;" placeholder="Escribe su Apellido Materno" title="El apellido materno solo debe contener letras y espacios y comenzar con mayúscula.">
                    </div>
                </div>

            <!-- Sección 2: Datos de Nacimiento y Edad -->
            <div class="section mb-4">
                <h6 class="text-secondary">Datos de Nacimiento y Edad</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fecha_nacimiento" class="form-label"><strong>Fecha de Nacimiento</strong></label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required placeholder="Ej. 2000-01-01">
                    </div>
                    <div class="col-md-6">
                        <label for="edad_anios" class="form-label"><strong>Edad (Años)</strong></label>
                        <input type="number" class="form-control" id="edad_anios" name="edad_anios" value="{{ old('edad_anios') }}" required min="1" max="150" placeholder="Edad en años">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="edad_meses" class="form-label"><strong>Edad (Meses)</strong></label>
                        <input type="number" class="form-control" id="edad_meses" name="edad_meses" value="{{ old('edad_meses') }}" required min="0" max="150" placeholder="Edad en meses">
                    </div>
                    <div class="col-md-6">
                        <label for="sexo" class="form-label"><strong>Sexo</strong></label>
                        <select class="form-select" id="sexo" name="sexo" required>
                            <option>Selecciona una opcion</option>
                            <option value="Masculino" {{ old('sexo') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Femenino" {{ old('sexo') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="Sin Definir" {{ old('sexo') == 'Sin Definir' ? 'selected' : '' }}>Sin Definir</option>
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
                        <input type="number" class="form-control" id="cp" name="cp" value="{{ old('cp') }}" required pattern="[0-9]{5}" title="El código postal debe ser un número de 5 dígitos.">
                    </div>
                    <div class="col-md-6">
                        <label for="ciudad" class="form-label"><strong>Ciudad</strong></label>
                        <input type="text" class="form-control" id="ciudad" name="ciudad" value="{{ old('ciudad') }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="estado" class="form-label"><strong>Estado</strong></label>
                        <input type="text" class="form-control" id="estado" name="estado" value="{{ old('estado') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lugar_nacimiento" class="form-label"><strong>Lugar de Nacimiento</strong></label>
                        <input type="text" class="form-control" id="lugar_nacimiento" name="lugar_nacimiento" value="{{ old('lugar_nacimiento') }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="domicilio" class="form-label"><strong>Domicilio</strong></label>
                        <input type="text" class="form-control" id="domicilio" name="domicilio" value="{{ old('domicilio') }}" placeholder="Ej. Calle 123" required>
                    </div>
                    <div class="col-md-6">
                        <label for="colonia" class="form-label"><strong>Colonia</strong></label>
                        <input type="text" class="form-control" id="colonia" name="colonia" value="{{ old('colonia') }}" placeholder="Ej. Centro, Las Lomas">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="cerrada" class="form-label"><strong>Cerrada</strong></label>
                        <input type="text" class="form-control" id="cerrada" name="cerrada" value="{{ old('cerrada') }}" placeholder="Ej. Cerrada del Bosque">
                    </div>
                    <div class="col-md-6">
                        <label for="numero_calle" class="form-label"><strong>No. Domicilio</strong></label>
                        <input type="text" class="form-control" id="no_domicilio" name="no_domicilio" value="{{ old('no_domicilio') }}" placeholder="Ej. 45 o A2" pattern="[A-Za-z0-9]+">
                    </div>
                </div>
            </div>

            <!-- Sección 4: Datos Familiares y Salud -->
            <div class="section mb-4">
                <h6 class="text-secondary">Información adicional</h6>
            <p class="text-secondary small">Los datos de salud y hermanos son opcionales. Agrega sólo lo que corresponda al alumno.</p>
            <div id="hermanos-adicionales"></div>

            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-outline-primary" id="agregar-hermano"><i class="ti ti-plus"></i> Agregar hermano</button>
            </div>
            <p class="text-muted mt-2 mb-4">Puedes agregar todos los hermanos necesarios o dejar esta parte vacía.</p>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="enfermedades_alergias" class="form-label"><strong>Enfermedades o Alergias</strong></label>
                        <input type="text" class="form-control" id="enfermedades_alergias" name="enfermedades_alergias" value="{{ old('enfermedades_alergias') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="pediatra_nombre" class="form-label"><strong>Nombre del Pediatra</strong></label>
                        <input type="text" class="form-control" id="pediatra_nombre" name="pediatra_nombre" value="{{ old('pediatra_nombre') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="pediatra_telefono" class="form-label"><strong>Teléfono del Pediatra</strong></label>
                        <input type="number" class="form-control" id="pediatra_telefono" name="pediatra_telefono" value="{{ old('pediatra_telefono') }}">
                    </div>
                </div>

                <!-- Sección 5: Fechas de Inscripción -->
                <div class="section mb-4">
                    <h6 class="text-secondary">Grado y Fecha de Inscripción</h6>
                    <div class="row mb-3">
                    <div class="col-md-3">
                            <label for="grado_id" class="form-label"><strong>Grado</strong></label>
                            <select class="form-select" id="grado_id" name="grado_id" required>
                                <option value="">Selecciona un grado</option>
                                @if($grados && $grados->isNotEmpty())
                                    @foreach ($grados as $grado)
                                        <option value="{{ $grado->id }}" {{ old('grado_id') == $grado->id ? 'selected' : '' }}>{{ $grado->nombre }}</option>
                                    @endforeach
                                @else
                                    <option value="">No hay grados disponibles</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="seccion" class="form-label"><strong>Sección</strong></label>
                            <select class="form-select" id="seccion" name="seccion" disabled>
                                <option value="">Primero selecciona un grado</option>
                                @foreach($secciones as $seccion)
                                    <option value="{{ $seccion->nombre }}" data-grado="{{ $seccion->grado_id }}" @selected(old('seccion') === $seccion->nombre)>{{ $seccion->nombre }}</option>
                                @endforeach
                            </select>
                            <small class="text-secondary">Sólo se muestran las secciones configuradas para el grado.</small>
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_inscripcion" class="form-label"><strong>Fecha de Inscripción</strong></label>
                            <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', now()->toDateString()) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label"><strong>Fecha de Inicio</strong></label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', now()->toDateString()) }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Información de Contactos -->
                <div id="contactos-emergencia" class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Información de Contactos (En caso de no localizar a los padres por emergencia)</h5>

                        <!-- Primer Contacto -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contacto1nombre" class="form-label"><strong>Nombre Completo del Primer Contacto</strong></label>
                                <input type="text" class="form-control" id="contacto1nombre" name="contacto1nombre" value="{{ old('contacto1nombre') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="Ej. María Pérez">
                            </div>

                            <div class="col-md-6">
                                <label for="telefono1" class="form-label"><strong>Teléfono del Primer Contacto</strong></label>
                                <input type="tel" inputmode="numeric" class="form-control" id="telefono1" name="telefono1" value="{{ old('telefono1') }}" maxlength="15" required pattern="[0-9]{10,15}" title="Escribe de 10 a 15 dígitos.">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="correo1" class="form-label"><strong>Correo del Primer Contacto</strong></label>
                                <input type="email" class="form-control" id="correo1" name="correo1" value="{{ old('correo1') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="contacto1tipo" class="form-label"><strong>Tipo de Contacto</strong></label>
                                <input list="contactoTipos" class="form-control" id="contacto1tipo" name="contacto1tipo" required>
                            </div>
                        </div>

                        <!-- Agregar Contactos Adicionales -->
                        <div id="contactos-adicionales"></div>

                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-primary" id="agregar-contacto">Agregar otro contacto</button>
                        </div>

                        <p class="text-muted mt-2">El primer contacto es obligatorio. Agrega hasta dos contactos adicionales si hacen falta.</p>
                    </div>
                </div>
            </div>

        <!-- Información Común de los Padres o Tutor -->
@php
    $tiposFamiliares = ['Padre', 'Madre', 'Tutor'];
@endphp

    @foreach($tiposFamiliares as $tipo)
    <div id="{{ $loop->first ? 'familiares' : 'familiar-' . $loop->iteration }}" class="card shadow-sm mb-4" data-familiar-card style="background-color: #f8f9fa;">
        <div class="card-body">
            <h5 class="card-title text-primary">Datos de {{ $tipo }}</h5> <!-- Título dinámico -->
            <p class="text-secondary small">Completa esta tarjeta sólo si {{ strtolower($tipo) }} forma parte de los responsables del alumno.</p>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label"><strong>Nombre Completo</strong></label>
                    <input type="text" class="form-control" name="familiares[{{ $tipo }}][nombre]" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><strong>Apellido Paterno</strong></label>
                    <input type="text" class="form-control" name="familiares[{{ $tipo }}][apellido_paterno]" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><strong>Apellido Materno</strong></label>
                    <input type="text" class="form-control" name="familiares[{{ $tipo }}][apellido_materno]" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><strong>Fecha de Nacimiento</strong></label>
                    <input type="date" class="form-control" name="familiares[{{ $tipo }}][fecha_nacimiento]" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label"><strong>Estado Civil</strong></label>
                    <select class="form-select" name="familiares[{{ $tipo }}][estado_civil]" required>
                        <option value="">Selecciona estado civil</option>
                        <option value="Soltero">Soltero</option>
                        <option value="Casado">Casado</option>
                        <option value="Divorciado">Divorciado</option>
                        <option value="Viudo">Viudo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><strong>Domicilio</strong></label>
                    <input type="text" class="form-control" name="familiares[{{ $tipo }}][domicilio]" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-2">
                        <label class="form-label"><strong>Colonia</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $tipo }}][colonia]" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><strong>No.Domicilio</strong></label>
                        <input type="number" class="form-control" name="familiares[{{ $tipo }}][no_domicilio]" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><strong>C.P.</strong></label>
                        <input type="number" class="form-control" name="familiares[{{ $tipo }}][cp]" required>
                    </div>
                
                    <div class="col-md-3">
                        <label class="form-label"><strong>Ciudad</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $tipo }}][ciudad]" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label"><strong>Estado</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $tipo }}][estado]" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label"><strong>Teléfono celular</strong> <span class="text-secondary">(opcional)</span></label>
                        <input type="tel" inputmode="numeric" class="form-control" name="familiares[{{ $tipo }}][celular]" pattern="\d{10}" maxlength="10" placeholder="10 dígitos, si cuenta con uno">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><strong>Teléfono fijo</strong> <span class="text-secondary">(opcional)</span></label>
                        <input type="tel" inputmode="numeric" class="form-control" name="familiares[{{ $tipo }}][telefono_fijo]" pattern="\d{10}" maxlength="10" placeholder="10 dígitos, si cuenta con uno">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><strong>Correo</strong></label>
                        <input type="email" class="form-control" name="familiares[{{ $tipo }}][correo]" required placeholder="Ej. ejemplo@correo.com">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Profesión</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $tipo }}][profesion]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Ocupación</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $tipo }}][ocupacion]" required>
                    </div>
                </div>

                <!-- Campos de la Empresa -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Nombre de la Empresa</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $tipo }}][empresa_nombre]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Teléfono de la Empresa</strong></label>
                        <input type="number" class="form-control" name="familiares[{{ $tipo }}][empresa_telefono]" pattern="\d{10}" required placeholder="Ej. 1234567890">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Domicilio de la Empresa</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $tipo }}][empresa_domicilio]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Ciudad de la Empresa</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $tipo }}][empresa_ciudad]" required>
                    </div>
                </div>
            </div>
        </div>
     @endforeach
</div>
        <div id="revision-registro" class="card csl-data-card mb-4">
            <div class="card-body text-center">
                <h5 class="card-title text-primary mb-2"><i class="ti ti-circle-check me-2"></i>Revisar y registrar</h5>
                <p class="text-secondary mb-3">Confirma que los datos principales, el contacto de emergencia y al menos un familiar responsable estén correctos.</p>
                <button id="registerStudentBtn" type="submit" class="btn btn-success btn-lg"><i class="ti ti-device-floppy me-1"></i>Registrar alumno</button>
            </div>
        </div>
        <br><br>
    </form>
</div><br>
<script>
    // Contador y configuración para los contactos
    let contactoCount = 1;
    const maxContactos = 3;
    const agregarContactoButton = document.getElementById('agregar-contacto');
    const contactosAdicionales = document.getElementById('contactos-adicionales');

    // Función para agregar contactos
    agregarContactoButton.addEventListener('click', function() {
        if (contactoCount < maxContactos) {
            contactoCount++;
            const contactoDiv = document.createElement('div');
            contactoDiv.classList.add('card', 'shadow-sm', 'mb-4');
            contactoDiv.style.backgroundColor = '#f8f9fa';
            contactoDiv.innerHTML = `
                <div class="card-body">
                    <h5 class="card-title text-primary">Contacto ${contactoCount}</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="contacto${contactoCount}nombre" class="form-label"><strong>Nombre del Contacto ${contactoCount}</strong></label>
                            <input type="text" class="form-control" id="contacto${contactoCount}nombre" name="contacto${contactoCount}nombre">
                        </div>
                        <div class="col-md-6">
                            <label for="telefono${contactoCount}" class="form-label"><strong>Teléfono del Contacto ${contactoCount}</strong></label>
                            <input type="number" class="form-control" id="telefono${contactoCount}" name="telefono${contactoCount}" maxlength="12">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="correo${contactoCount}" class="form-label"><strong>Correo del Contacto ${contactoCount}</strong></label>
                            <input type="email" class="form-control" id="correo${contactoCount}" name="correo${contactoCount}" placeholder="Ej. contacto${contactoCount}@correo.com">
                        </div>
                        <div class="col-md-6">
                        <label for="contacto${contactoCount}tipo" class="form-label"><strong>Tipo de Contacto</strong></label>
                        <input list="contactoTipos${contactoCount}" class="form-control" id="contacto${contactoCount}tipo" name="contacto${contactoCount}tipo">
                    </div>
                        </div>
                    </div>
                </div>
            </div>`;
            contactosAdicionales.appendChild(contactoDiv);

            // Si se alcanzan los 3 contactos, ocultamos el botón
            if (contactoCount === maxContactos) {
                agregarContactoButton.classList.add('d-none');
            }
        }
    });
    // Contador y configuración para los hermanos
let hermanoCount = 0;
const agregarHermanoButton = document.getElementById('agregar-hermano');
const hermanosAdicionales = document.getElementById('hermanos-adicionales');

const gradoSelect = document.getElementById('grado_id');
const seccionSelect = document.getElementById('seccion');
const seccionesConfiguradas = [...seccionSelect.options].slice(1);

function actualizarSecciones() {
    const seccionAnterior = seccionSelect.value;
    seccionSelect.innerHTML = '<option value="">Selecciona una sección</option>';

    const seccionesDelGrado = seccionesConfiguradas.filter((option) => option.dataset.grado === gradoSelect.value);
    seccionesDelGrado.forEach((option) => seccionSelect.add(option.cloneNode(true)));

    seccionSelect.disabled = !gradoSelect.value || seccionesDelGrado.length === 0;

    if (seccionesDelGrado.length === 0 && gradoSelect.value) {
        seccionSelect.innerHTML = '<option value="">No hay secciones configuradas</option>';
    }

    if (seccionAnterior) {
        seccionSelect.value = seccionAnterior;
    }
}

gradoSelect.addEventListener('change', actualizarSecciones);
actualizarSecciones();

// Función para agregar hermanos
agregarHermanoButton.addEventListener('click', function() {
        hermanoCount++;
        const hermanoDiv = document.createElement('div');
        hermanoDiv.classList.add('card', 'shadow-sm', 'mb-4');
        hermanoDiv.style.backgroundColor = '#f8f9fa';
        hermanoDiv.innerHTML = `
             <div class="card-body">
    <h5 class="card-title text-primary">Hermano ${hermanoCount}</h5>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="hermano${hermanoCount}nombre" class="form-label"><strong>Nombre del Hermano ${hermanoCount}</strong></label>
            <input type="text" class="form-control" id="hermano${hermanoCount}nombre" name="hermanos[${hermanoCount}][nombre]" placeholder="Ej. Juan Pérez">
        </div>

        <div class="col-md-6">
            <label for="hermano${hermanoCount}apellido_paterno" class="form-label"><strong>Apellido Paterno del Hermano ${hermanoCount}</strong></label>
            <input type="text" class="form-control" id="hermano${hermanoCount}apellido_paterno" name="hermanos[${hermanoCount}][apellido_paterno]" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="Ej. Pérez">
        </div>

        <div class="col-md-6">
            <label for="hermano${hermanoCount}apellido_materno" class="form-label"><strong>Apellido Materno del Hermano ${hermanoCount}</strong></label>
            <input type="text" class="form-control" id="hermano${hermanoCount}apellido_materno" name="hermanos[${hermanoCount}][apellido_materno]" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="Ej. Gómez">
        </div>

        <div class="col-md-6">
            <label for="hermano${hermanoCount}edad" class="form-label"><strong>Edad del Hermano ${hermanoCount}</strong></label>
            <input type="number" class="form-control" id="hermano${hermanoCount}edad" name="hermanos[${hermanoCount}][edad]" min="0" max="50">
        </div>
    </div>
</div>
<button type="button" class="btn btn-sm btn-outline-danger quitar-hermano"><i class="ti ti-trash"></i> Quitar hermano</button>`;
        hermanosAdicionales.appendChild(hermanoDiv);
});

hermanosAdicionales.addEventListener('click', function(event) {
    const boton = event.target.closest('.quitar-hermano');
    if (boton) boton.closest('.card')?.remove();
});

// Las tarjetas de padre, madre y tutor son opcionales por separado. El
// servidor exige que al menos una tenga nombre antes de crear el expediente.
document.querySelectorAll('[data-familiar-card] input, [data-familiar-card] select').forEach((field) => {
    field.required = false;
});

// Recorrido por tarjetas: evita una captura interminable y conserva todos los
// campos en el mismo formulario hasta confirmar el registro.
(function inicializarPasosRegistro() {
    const form = document.getElementById('studentCreateForm');
    const nav = document.querySelector('.csl-form-nav');
    if (!form || !nav) return;

    const familiares = Array.from(form.querySelectorAll('[data-familiar-card]'));
    const pasos = [
        { titulo: 'Datos del alumno', elementos: Array.from(form.querySelectorAll('#datos-alumno')) },
        { titulo: 'Contactos de emergencia', elementos: Array.from(form.querySelectorAll('#contactos-emergencia')) },
        { titulo: 'Familiares responsables', elementos: familiares },
        { titulo: 'Revisar y registrar', elementos: Array.from(form.querySelectorAll('#revision-registro')) },
    ].filter((paso) => paso.elementos.length);
    if (pasos.length < 2) return;

    const controles = document.createElement('section');
    controles.className = 'csl-expediente-stepper';
    controles.setAttribute('aria-label', 'Pasos del registro');
    controles.innerHTML = `<div class="csl-expediente-stepper-copy"><strong id="registro-paso-titulo"></strong><small id="registro-paso-contador"></small></div><div class="csl-expediente-stepper-actions"><button type="button" class="btn btn-outline-secondary" id="registro-anterior"><i class="ti ti-chevron-left"></i> Anterior</button><button type="button" class="btn btn-primary" id="registro-siguiente">Siguiente <i class="ti ti-chevron-right"></i></button></div>`;
    nav.insertAdjacentElement('afterend', controles);

    const title = controles.querySelector('#registro-paso-titulo');
    const counter = controles.querySelector('#registro-paso-contador');
    const previous = controles.querySelector('#registro-anterior');
    const next = controles.querySelector('#registro-siguiente');
    let actual = 0;

    const mostrar = (indice, desplazar = true) => {
        actual = Math.max(0, Math.min(indice, pasos.length - 1));
        pasos.forEach((paso, pasoIndice) => paso.elementos.forEach((elemento) => elemento.classList.toggle('csl-expediente-step-hidden', pasoIndice !== actual)));
        title.textContent = pasos[actual].titulo;
        counter.textContent = `Paso ${actual + 1} de ${pasos.length}`;
        previous.disabled = actual === 0;
        next.style.visibility = actual === pasos.length - 1 ? 'hidden' : 'visible';
        const ultimo = pasos[actual].elementos[pasos[actual].elementos.length - 1];
        ultimo?.insertAdjacentElement('afterend', controles);
        if (desplazar) pasos[actual].elementos[0]?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    previous.addEventListener('click', () => mostrar(actual - 1));
    next.addEventListener('click', () => mostrar(actual + 1));
    nav.querySelectorAll('a').forEach((link) => link.addEventListener('click', (event) => {
        const target = document.querySelector(link.getAttribute('href'));
        const index = pasos.findIndex((paso) => paso.elementos.some((elemento) => elemento === target || elemento.contains(target)));
        if (index < 0) return;
        event.preventDefault();
        mostrar(index);
    }));

    // Antes de validar se revelan todas las secciones, para que el navegador
    // pueda indicar de forma clara el campo obligatorio pendiente.
    document.getElementById('registerStudentBtn')?.addEventListener('click', () => {
        pasos.forEach((paso) => paso.elementos.forEach((elemento) => elemento.classList.remove('csl-expediente-step-hidden')));
    });
    mostrar(0, false);
})();
</script>
@endsection

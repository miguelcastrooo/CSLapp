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
<div class="col-md-12"><br>

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
                        <label for="plataformas_{{ $plataformaId }}_usuario" class="form-label"><strong>Usuario {{ ucfirst($plataformaNombre) }} </strong></label>
                        <input type="text" class="form-control" 
                            id="plataformas_{{ $plataformaId }}_usuario" 
                            name="plataformas[{{ $plataformaId }}][usuario]" 
                            value="{{ old('plataformas.'.$plataformaId.'.usuario', $plataformaExistente->usuario ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="plataformas_{{ $plataformaId }}_contraseña" class="form-label"><strong>Contraseña {{ ucfirst($plataformaNombre) }}</strong></label>
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



    <div class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
        <div class="card-body">
            <h5 class="card-title text-primary">Datos del Alumno</h5>

            <input type="hidden" name="nivel_educativo_id" value="{{ old('nivel_educativo_id', $alumno->nivel_educativo_id) }}">

            <!-- Sección 1: Datos Básicos -->
            <div class="section mb-4">
                <div class="row mb-3">
                    
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="nombre" class="form-label"><strong>Nombre</strong></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $alumno->nombre) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" inputmode="text" style="text-transform: capitalize;" placeholder="Escribe el nombre" title="El nombre solo debe contener letras y espacios y comenzar con mayúscula." readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="apellidopaterno" class="form-label"><strong>Apellido Paterno</strong></label>
                        <input type="text" class="form-control" id="apellidopaterno" name="apellidopaterno" value="{{ old('apellidopaterno', $alumno->apellidopaterno) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" inputmode="text" style="text-transform: capitalize;" placeholder="Escribe su Apellido Paterno" title="El apellido paterno solo debe contener letras y espacios y comenzar con mayúscula." readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="apellidomaterno" class="form-label"><strong>Apellido Materno</strong></label>
                        <input type="text" class="form-control" id="apellidomaterno" name="apellidomaterno" value="{{ old('apellidomaterno', $alumno->apellidomaterno) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" style="text-transform: capitalize;" placeholder="Escribe su Apellido Materno" title="El apellido materno solo debe contener letras y espacios y comenzar con mayúscula." readonly>
                    </div>
                </div>

            <!-- Sección 2: Datos de Nacimiento y Edad -->
            <div class="section mb-4">
                <h6 class="text-secondary">Datos de Nacimiento y Edad</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fecha_nacimiento" class="form-label"><strong>Fecha de Nacimiento</strong></label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $alumno->fecha_nacimiento) }}" required placeholder="Ej. 2000-01-01" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="edad_anios" class="form-label"><strong>Edad (Años)</strong></label>
                        <input type="number" class="form-control" id="edad_anios" name="edad_anios" value="{{ old('edad_anios', $alumno->edad_anios) }}" required min="1" max="150" placeholder="Edad en años" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="edad_meses" class="form-label"><strong>Edad (Meses)</strong></label>
                        <input type="number" class="form-control" id="edad_meses" name="edad_meses" value="{{ old('edad_meses', $alumno->edad_meses) }}" required min="0" max="150" placeholder="Edad en meses" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="sexo" class="form-label"><strong>Sexo</strong></label>
                        <select class="form-select" id="sexo" name="sexo" required disabled>
                            <option>Selecciona una opcion</option>
                            <option value="Masculino" {{ old('sexo', $alumno->sexo) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Femenino" {{ old('sexo', $alumno->sexo) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="Sin Definir" {{ old('sexo', $alumno->sexo) == 'Sin Definir' ? 'selected' : '' }}>Sin Definir</option>
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
                        <input type="number" class="form-control" id="cp" name="cp" value="{{ old('cp', $alumno->cp) }}" required pattern="[0-9]{5}" title="El código postal debe ser un número de 5 dígitos." readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="ciudad" class="form-label"><strong>Ciudad</strong></label>
                        <input type="text" class="form-control" id="ciudad" name="ciudad" value="{{ old('ciudad', $alumno->ciudad) }}" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="estado" class="form-label"><strong>Estado</strong></label>
                        <input type="text" class="form-control" id="estado" name="estado" value="{{ old('estado', $alumno->estado) }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="lugar_nacimiento" class="form-label"><strong>Lugar de Nacimiento</strong></label>
                        <input type="text" class="form-control" id="lugar_nacimiento" name="lugar_nacimiento" value="{{ old('lugar_nacimiento', $alumno->lugar_nacimiento) }}" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="domicilio" class="form-label"><strong>Domicilio</strong></label>
                        <input type="text" class="form-control" id="domicilio" name="domicilio" value="{{ old('domicilio', $alumno->domicilio) }}" placeholder="Ej. Calle 123" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="colonia" class="form-label"><strong>Colonia</strong></label>
                        <input type="text" class="form-control" id="colonia" name="colonia" value="{{ old('colonia', $alumno->colonia) }}" placeholder="Ej. Centro, Las Lomas" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="cerrada" class="form-label"><strong>Cerrada</strong></label>
                        <input type="text" class="form-control" id="cerrada" name="cerrada" value="{{ old('cerrada', $alumno->cerrada) }}" placeholder="Ej. Cerrada del Bosque" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="numero_calle" class="form-label"><strong>No. Domicilio</strong></label>
                        <input type="text" class="form-control" id="no_domicilio" name="no_domicilio" value="{{ old('no_domicilio', $alumno->no_domicilio) }}" placeholder="Ej. 45 o A2" pattern="[A-Za-z0-9]+" readonly>
                    </div>
                </div>
            </div>

            <!-- Sección 4: Datos Familiares y Salud -->
<div class="section mb-4">
    <h6 class="text-secondary">Datos Familiares y Salud</h6>

    <!-- Ciclo para mostrar todos los hermanos -->
    @foreach ($alumno->hermanos as $index => $hermano)
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="hermano{{ $index+1 }}nombre" class="form-label"><strong>Nombre del Hermano {{ $index+1 }}</strong></label>
                <input type="text" class="form-control" id="hermano{{ $index+1 }}nombre" name="hermanos[{{ $index }}][nombre]" value="{{ old('hermanos.' . $index . '.nombre', $hermano->nombre) }}" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="Ej. Juan Pérez" readonly>
            </div>
            <div class="col-md-6">
                <label for="hermano{{ $index+1 }}apellido_paterno" class="form-label"><strong>Apellido Paterno del Hermano {{ $index+1 }}</strong></label>
                <input type="text" class="form-control" id="hermano{{ $index+1 }}apellido_paterno" name="hermanos[{{ $index }}][apellido_paterno]" value="{{ old('hermanos.' . $index . '.apellido_paterno', $hermano->apellido_paterno) }}" readonly>
            </div>
            <div class="col-md-6">
                <label for="hermano{{ $index+1 }}apellido_materno" class="form-label"><strong>Apellido Materno del Hermano {{ $index+1 }}</strong></label>
                <input type="text" class="form-control" id="hermano{{ $index+1 }}apellido_materno" name="hermanos[{{ $index }}][apellido_materno]" value="{{ old('hermanos.' . $index . '.apellido_materno', $hermano->apellido_materno) }}" readonly>
            </div>
            <div class="col-md-6">
                <label for="hermano{{ $index+1 }}edad" class="form-label"><strong>Edad del Hermano {{ $index+1 }}</strong></label>
                <input type="number" class="form-control" id="hermano{{ $index+1 }}edad" name="hermanos[{{ $index }}][edad]" value="{{ old('hermanos.' . $index . '.edad', $hermano->edad) }}" min="0" max="50" readonly>
            </div>
            <input type="hidden" name="hermanos[{{ $index }}][id]" value="{{ $hermano->id }}">
        </div>
    @endforeach
    <div id="hermanos-adicionales"></div>

<div class="d-flex justify-content-center">
    <button type="button" class="btn btn-primary" id="agregar-hermano">Agregar otro hermano</button>
</div>
<p class="text-muted mt-2">Puedes agregar hasta 5 hermanos. Los campos de hermano son opcionales.</p>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="enfermedades_alergias" class="form-label"><strong>Enfermedades o Alergias</strong></label>
                        <input type="text" class="form-control" id="enfermedades_alergias" name="enfermedades_alergias" value="{{ old('enfermedades_alergias', $alumno->enfermedades_alergias) }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="pediatra_nombre" class="form-label"><strong>Nombre del Pediatra</strong></label>
                        <input type="text" class="form-control" id="pediatra_nombre" name="pediatra_nombre" value="{{ old('pediatra_nombre', $alumno->pediatra_nombre) }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="pediatra_telefono" class="form-label"><strong>Teléfono del Pediatra</strong></label>
                        <input type="number" class="form-control" id="pediatra_telefono" name="pediatra_telefono" value="{{ old('pediatra_telefono', $alumno->pediatra_telefono) }}" placeholder="Ej. 1234567890" readonly>
                    </div>
                </div>
               <!-- Sección 5: Fechas de Inscripción -->
                <div class="section mb-4">
                    <h6 class="text-secondary">Grado y Fecha de Inscripción</h6>
                    <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="grado_id" class="form-label"><strong>Grado</strong></label>
                        <select class="form-select" id="grado_id" name="grado_id" required disabled>
                            <option value="">Selecciona un grado</option>
                            @if($grados && $grados->isNotEmpty())
                                @foreach ($grados as $grado)
                                    <option value="{{ $grado->id }}" {{ (old('grado_id') == $grado->id || $alumno->grado_id == $grado->id) ? 'selected' : '' }}>{{ $grado->nombre }}</option>
                                @endforeach
                            @else
                                <option value="">No hay grados disponibles</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_inscripcion" class="form-label"><strong>Fecha de Inscripción</strong></label>
                        <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', $alumno->fecha_inscripcion) }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_inicio" class="form-label"><strong>Fecha de Inicio</strong></label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $alumno->fecha_inicio) }}" readonly>
                        </div>
                    </div>
                    @if (Auth::user()->hasRole('SuperAdmin') )
                    <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="grado_id" class="form-label"><strong>NivelEducativo</strong></label>
                        <select class="form-select" id="nivel_educativo_id" name="nivel_educativo_id" required disabled>
                            <option value="">Selecciona un Nivel</option>
                                @foreach($niveles as $nivel)
                                    <option value="{{ $nivel->id }}" 
                                        {{ old('nivel_educativo_id', $alumno->nivel_educativo_id) == $nivel->id ? 'selected' : '' }}>
                                        {{ $nivel->nombre }}
                                    </option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="seccion" class="form-label"><strong>Seccion</strong></label>
                        <input type="text" class="form-control" id="seccion" name="seccion" value="{{ old('seccion', $alumno->seccion) }}" readonly>
                    </div>
                    @endif
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
                            <label for="contacto{{ $index + 1 }}nombre" class="form-label"><strong>Nombre del Contacto {{ $index + 1 }}</strong></label>
                            <input type="text" class="form-control" id="contacto{{ $index + 1 }}nombre" 
                                name="contactos[{{ $index }}][nombre]" 
                                value="{{ old('contactos.'.$index.'.nombre', $contacto->nombre) }}" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono{{ $index + 1 }}" class="form-label"><strong>Teléfono del Contacto {{ $index + 1 }}</strong></label>
                            <input type="number" class="form-control" id="telefono{{ $index + 1 }}" 
                                name="contactos[{{ $index }}][telefono]" 
                                value="{{ old('contactos.'.$index.'.telefono', $contacto->telefono) }}" 
                                maxlength="12" pattern="\d{12}" title="Debe ser un número de 12 dígitos" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="correo{{ $index + 1 }}" class="form-label"><strong>Correo del Contacto {{ $index + 1 }}</strong></label>
                            <input type="email" class="form-control" id="correo{{ $index + 1 }}" 
                                name="contactos[{{ $index }}][correo]" 
                                value="{{ old('contactos.'.$index.'.correo', $contacto->correo) }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="contacto1tipo{{ $index + 1 }}" class="form-label"><strong>Tipo de Contacto {{ $index + 1 }}</strong></label>
                            <input list="contactoTipos" class="form-control" id="contacto1tipo{{ $index + 1 }}" 
                                name="contactos[{{ $index }}][tipo_contacto]" 
                                value="{{ old('contactos.'.$index.'.tipo_contacto', $contacto->tipo_contacto) }}" required readonly>
                        </div>
                        <input type="hidden" name="contactos[{{ $index }}][id]" value="{{ $contacto->id }}">
                    </div>
                @endforeach
                 <!-- Agregar Contactos Adicionales -->
                 <div id="contactos-adicionales"></div>

                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-primary" id="agregar-contacto">Agregar otro contacto</button>
                    </div>

            </div>
        </div>
    </div>
</div>

    @foreach($familiares as $familiar)
        <div class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
            <div class="card-body">
            <h5 class="card-title text-primary">Datos de {{ ucfirst(strtolower($familiar->tipo_familiar)) }}</h5>
                <!-- Campos de datos personales -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Nombre Completo</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][nombre]" 
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.nombre', $familiar->nombre ?? '') }}" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Apellido Paterno</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][apellido_paterno]" 
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.apellido_paterno', $familiar->apellido_paterno ?? '') }}" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Apellido Materno</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][apellido_materno]" 
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.apellido_materno', $familiar->apellido_materno ?? '') }}" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Fecha de Nacimiento</strong></label>
                        <input type="date" class="form-control" name="familiares[{{ $loop->index }}][fecha_nacimiento]" 
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.fecha_nacimiento', $familiar->fecha_nacimiento ?? '') }}" required readonly>
                    </div>
                </div>
                <!-- Campos de estado civil y domicilio -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Estado Civil</strong></label>
                        <select class="form-select" name="familiares[{{ $loop->index }}][estado_civil]" required disabled>
                            <option value="">Selecciona estado civil</option>
                            <option value="Soltero" {{ old('familiares.'.$familiar->tipo_familiar.'.estado_civil', $familiar->estado_civil ?? '') == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                            <option value="Casado" {{ old('familiares.'.$familiar->tipo_familiar.'.estado_civil', $familiar->estado_civil ?? '') == 'Casado' ? 'selected' : '' }}>Casado</option>
                            <option value="Divorciado" {{ old('familiares.'.$familiar->tipo_familiar.'.estado_civil', $familiar->estado_civil ?? '') == 'Divorciado' ? 'selected' : '' }}>Divorciado</option>
                            <option value="Viudo" {{ old('familiares.'.$familiar->tipo_familiar.'.estado_civil', $familiar->estado_civil ?? '') == 'Viudo' ? 'selected' : '' }}>Viudo</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Domicilio</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][domicilio]" 
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.domicilio', $familiar->domicilio ?? '') }}" required readonly>
                    </div>
                </div>
                <!-- Campos de dirección -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label"><strong>Colonia</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][colonia]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.colonia', $familiar->colonia ?? '') }}" required readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><strong>No.Domicilio</strong></label>
                        <input type="number" class="form-control" name="familiares[{{ $loop->index }}][no_domicilio]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.no_domicilio', $familiar->no_domicilio ?? '') }}" required readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><strong>C.P.</strong></label>
                        <input type="number" class="form-control"name="familiares[{{ $loop->index }}][cp]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.cp', $familiar->cp ?? '') }}" required readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><strong>Ciudad</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][ciudad]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.ciudad', $familiar->ciudad ?? '') }}" required readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><strong>Estado</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][estado]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.estado', $familiar->estado ?? '') }}" required readonly>
                    </div>
                </div>
                <!-- Campos de contacto -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label"><strong>Teléfono Celular</strong></label>
                        <input type="number" class="form-control" name="familiares[{{ $loop->index }}][celular]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.celular', $familiar->celular ?? '') }}" pattern="\d{10}" required placeholder="Ej. 1234567890" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><strong>Teléfono Fijo</strong></label>
                        <input type="number" class="form-control" name="familiares[{{ $loop->index }}][telefono_fijo]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.telefono_fijo', $familiar->telefono_fijo ?? '') }}" pattern="\d{10}" required placeholder="Ej. 1234567890" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><strong>Correo</strong></label>
                        <input type="email" class="form-control" name="familiares[{{ $loop->index }}][correo]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.correo', $familiar->correo ?? '') }}" required placeholder="Ej. ejemplo@correo.com" readonly>
                    </div>
                </div>
                <!-- Campos de trabajo -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Profesión</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][profesion]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.profesion', $familiar->profesion ?? '') }}" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Ocupación</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][ocupacion]" 
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.ocupacion', $familiar->ocupacion ?? '') }}" required readonly>
                    </div>
                </div>
                <!-- Campos de empresa -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Nombre de la Empresa</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][empresa_nombre]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.empresa_nombre', $familiar->empresa_nombre ?? '') }}" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Teléfono de la Empresa</strong></label>
                        <input type="number" class="form-control"name="familiares[{{ $loop->index }}][empresa_telefono]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.empresa_telefono', $familiar->empresa_telefono ?? '') }}" pattern="\d{10}" required placeholder="Ej. 1234567890" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Domicilio de la Empresa</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][empresa_domicilio]" 
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.empresa_domicilio', $familiar->empresa_domicilio ?? '') }}" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Ciudad de la Empresa</strong></label>
                        <input type="text" class="form-control" name="familiares[{{ $loop->index }}][empresa_ciudad]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.empresa_ciudad', $familiar->empresa_ciudad ?? '') }}" required readonly>
                    </div>
                </div>
                <input type="hidden" name="familiares[{{ $loop->index }}][id]" value="{{ $familiar->id }}">
                <input type="hidden" name="familiares[{{ $loop->index }}][tipo_familiar]" value="{{ $familiar->tipo_familiar }}">
            </div>
        </div>
    @endforeach
<!-- Card para agrupar las tarjetas de los familiares -->
@php
    $familiaresCount = $alumno->familiares->count();  // Obtiene el número de familiares ya registrados
@endphp

<!-- Solo muestra la card si hay menos de 3 familiares -->
@if ($familiaresCount < 3)
    <div class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
        <div class="card-body">
            <h5 class="card-title text-primary">Agregar Familiares</h5>

            <!-- Verifica si ya hay menos de 3 familiares registrados -->
             <center>
            <div id="agregar-familiares">
                <button type="button" class="btn btn-primary" id="agregar-familiares-btn">Agregar Familiares</button>
            </div>
            </center>

            <!-- Sección donde se agregarán los familiares -->
            <div id="familiares-container" class="mt-3"></div>
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
let hermanoCount = {{ $alumno->hermanos->count() }};
    let nuevoHermanoIndex = {{ $alumno->hermanos->count() }};
    const maxHermanos = 5;
    const agregarHermanoButton = document.getElementById('agregar-hermano');
    const hermanosAdicionales = document.getElementById('hermanos-adicionales');

    agregarHermanoButton.addEventListener('click', function () {
        if (hermanoCount < maxHermanos) {
            hermanoCount++;
            nuevoHermanoIndex++;

            const hermanoDiv = document.createElement('div');
            hermanoDiv.classList.add('card', 'shadow-sm', 'mb-4');
            hermanoDiv.style.backgroundColor = '#f8f9fa';

            hermanoDiv.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title text-primary mb-0">Hermano ${hermanoCount}</h5>
                        <button type="button" class="btn btn-danger btn-sm eliminar-hermano">Eliminar</button>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Nombre del Hermano</strong></label>
                            <input type="text" class="form-control" name="hermanos[${nuevoHermanoIndex}][nombre]" placeholder="Ej. Juan Pérez">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Apellido Paterno</strong></label>
                            <input type="text" class="form-control" name="hermanos[${nuevoHermanoIndex}][apellido_paterno]" placeholder="Ej. Pérez">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Apellido Materno</strong></label>
                            <input type="text" class="form-control" name="hermanos[${nuevoHermanoIndex}][apellido_materno]" placeholder="Ej. Gómez">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Edad</strong></label>
                            <input type="number" class="form-control" name="hermanos[${nuevoHermanoIndex}][edad]" min="0" max="50">
                        </div>
                    </div>
                </div>
            `;

            // Agregamos la funcionalidad al botón de eliminar
            hermanoDiv.querySelector('.eliminar-hermano').addEventListener('click', function () {
                hermanosAdicionales.removeChild(hermanoDiv);
                hermanoCount--;

                // Mostrar el botón agregar si se bajó del máximo
                if (hermanoCount < maxHermanos) {
                    agregarHermanoButton.classList.remove('d-none');
                }
            });

            hermanosAdicionales.appendChild(hermanoDiv);

            if (hermanoCount === maxHermanos) {
                agregarHermanoButton.classList.add('d-none');
            }
        }
    });
    let contactoCount = 0; // Comienza en 0
const maxContactos = 3;
const agregarContactoButton = document.getElementById('agregar-contacto');
const contactosAdicionales = document.getElementById('contactos-adicionales');

agregarContactoButton.addEventListener('click', function () {
    if (contactoCount < maxContactos) {
        const index = contactoCount;
        contactoCount++;

        const contactoDiv = document.createElement('div');
        contactoDiv.classList.add('card', 'shadow-sm', 'mb-4');
        contactoDiv.style.backgroundColor = '#f8f9fa';
        contactoDiv.innerHTML = `
            <div class="card-body">
                <h5 class="card-title text-primary">Contacto ${index + 1}</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Nombre</strong></label>
                        <input type="text" class="form-control" name="contactos[${index}][nombre]">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Teléfono</strong></label>
                        <input type="number" class="form-control" name="contactos[${index}][telefono]" pattern="\\d{10,12}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Correo</strong></label>
                        <input type="email" class="form-control" name="contactos[${index}][correo]" placeholder="Ej. contacto@correo.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Tipo de Contacto</strong></label>
                        <input list="contactoTipos" class="form-control" name="contactos[${index}][tipo_contacto]">
                    </div>
                </div>
            </div>
        `;
        contactosAdicionales.appendChild(contactoDiv);

        if (contactoCount === maxContactos) {
            agregarContactoButton.classList.add('d-none');
        }
    }
});
// Función para agregar las tres tarjetas de familiares
document.getElementById('agregar-familiares').addEventListener('click', function() {
    const familiaresContainer = document.getElementById('familiares-container');
    const botonAgregar = document.getElementById('agregar-familiares');
    
    // Verificar si ya se han agregado las tres tarjetas
    if (familiaresContainer.children.length === 3) {
        return;  // Si ya hay 3 tarjetas, no hacer nada
    }

    // Tipos de familiares
    const tiposFamiliares = ['Padre', 'Madre', 'Tutor'];

    // Limpiar el contenedor antes de agregar los nuevos familiares (si no se han agregado)
    familiaresContainer.innerHTML = '';

    // Agregar las tres tarjetas de familiares
    tiposFamiliares.forEach(tipo => {
        const cardDiv = document.createElement('div');
        cardDiv.classList.add('card', 'shadow-sm', 'mb-4');
        cardDiv.style.backgroundColor = '#f8f9fa';
        cardDiv.innerHTML = `
            <div class="card-body">
                <h5 class="card-title text-primary">Datos de ${tipo}</h5>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Nombre Completo</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][nombre]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Apellido Paterno</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][apellido_paterno]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Apellido Materno</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][apellido_materno]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Fecha de Nacimiento</strong></label>
                        <input type="date" class="form-control" name="familiares[${tipo}][fecha_nacimiento]" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Estado Civil</strong></label>
                        <select class="form-select" name="familiares[${tipo}][estado_civil]" required>
                            <option value="">Selecciona estado civil</option>
                            <option value="Soltero">Soltero</option>
                            <option value="Casado">Casado</option>
                            <option value="Divorciado">Divorciado</option>
                            <option value="Viudo">Viudo</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Domicilio</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][domicilio]" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label"><strong>Colonia</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][colonia]" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><strong>No.Domicilio</strong></label>
                        <input type="number" class="form-control" name="familiares[${tipo}][no_domicilio]" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><strong>C.P.</strong></label>
                        <input type="number" class="form-control" name="familiares[${tipo}][cp]" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label"><strong>Ciudad</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][ciudad]" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label"><strong>Estado</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][estado]" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label"><strong>Teléfono Celular</strong></label>
                        <input type="number" class="form-control" name="familiares[${tipo}][celular]" pattern="\d{10}" required placeholder="Ej. 1234567890">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><strong>Teléfono Fijo</strong></label>
                        <input type="number" class="form-control" name="familiares[${tipo}][telefono_fijo]" pattern="\d{10}" required placeholder="Ej. 1234567890">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><strong>Correo</strong></label>
                        <input type="email" class="form-control" name="familiares[${tipo}][correo]" required placeholder="Ej. ejemplo@correo.com">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Profesión</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][profesion]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Ocupación</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][ocupacion]" required>
                    </div>
                </div>

                <!-- Campos de la Empresa -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Nombre de la Empresa</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][empresa_nombre]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Teléfono de la Empresa</strong></label>
                        <input type="number" class="form-control" name="familiares[${tipo}][empresa_telefono]" pattern="\d{10}" required placeholder="Ej. 1234567890">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>Domicilio de la Empresa</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][empresa_domicilio]" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><strong>Ciudad de la Empresa</strong></label>
                        <input type="text" class="form-control" name="familiares[${tipo}][empresa_ciudad]" required>
                    </div>
                </div>
            </div>
        </div>`;

        // Agregar la card al contenedor
        familiaresContainer.appendChild(cardDiv);
    });

    // Ocultar el botón después de agregar las tres tarjetas
    botonAgregar.style.display = 'none';
});
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-xl csl-student-form">
    <div class="csl-module-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-3">
            <span class="csl-choice-icon"><i class="ti ti-school"></i></span>
            <div>
                <h1>Expediente del alumno</h1>
                <p>Consulta y actualiza la información registrada.</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap ms-auto" aria-label="Acciones del expediente">
            <a href="{{ route('capturista.selectsearch') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Volver</a>
            <a href="{{ route('admin.alumnos.reporte', $alumno) }}" target="_blank" rel="noopener" class="btn btn-outline-primary"><i class="ti ti-file-description"></i> Reporte PDF</a>
            <a href="{{ route('admin.alumnos.pdf.id', $alumno) }}" target="_blank" rel="noopener" class="btn btn-outline-danger"><i class="ti ti-printer"></i> Credenciales PDF</a>
            @if($puedeEditarExpediente)
                <button type="button" class="btn btn-warning" id="editBtn"><i class="ti ti-pencil"></i> Editar expediente</button>
                <button type="submit" form="studentEditForm" class="btn btn-primary" id="updateBtn" style="display:none;"><i class="ti ti-device-floppy"></i> Guardar cambios</button>
            @else
                <button type="button" class="btn btn-warning" id="editCredentialsBtn" onclick="window.habilitarContrasenasCoordinacion?.()"><i class="ti ti-key"></i> Editar accesos</button>
                <button type="submit" form="studentEditForm" class="btn btn-primary" id="saveCredentialsBtn" style="display:none;"><i class="ti ti-device-floppy"></i> Guardar accesos</button>
            @endif
        </div>
    </div>
    <nav class="csl-form-nav" aria-label="Secciones del expediente">
        <a href="#datos-alumno"><i class="ti ti-user"></i> Datos del alumno</a>
        <a href="#contactos-emergencia"><i class="ti ti-phone"></i> Contactos</a>
        <a href="#familiares"><i class="ti ti-users"></i> Familiares</a>
        <a href="#plataformas-acceso"><i class="ti ti-key"></i> Plataformas</a>
        <a href="#bitacora-contacto"><i class="ti ti-messages"></i> Bitácora</a>
    </nav>
    <x-task-guide :title="$puedeEditarExpediente ? 'Cómo actualizar este expediente' : 'Consulta de expediente'" :description="$puedeEditarExpediente ? 'Primero consulta los datos. Si necesitas corregir algo, usa el botón Editar.' : 'Puedes consultar toda la información y actualizar únicamente los accesos de plataformas.'" :steps="$puedeEditarExpediente ? ['Pulsa “Editar expediente” para habilitar el formulario.', 'Cambia solo los datos necesarios.', 'Revisa y pulsa “Guardar cambios”.'] : ['Consulta la información del alumno.', 'Usa “Editar accesos” sólo si cambia un usuario o contraseña de plataforma.', 'Guarda los accesos al terminar.']" />
    <div id="edit-status" class="alert alert-info d-flex gap-2" role="status"><i class="fas fa-eye mt-1"></i><div><strong>Modo consulta.</strong> {{ $puedeEditarExpediente ? 'Los datos están protegidos mientras los revisas.' : 'La información del alumno está protegida; sólo los accesos de plataformas pueden modificarse.' }}</div></div>
    @php
        $importantes = collect($estadoExpediente['importantes']);
        $opcionales = collect($estadoExpediente['opcionales']);
        $anclasPorCampo = [
            'Matrícula' => 'matricula', 'Nombre' => 'nombre', 'Apellido paterno' => 'apellidopaterno', 'Apellido materno' => 'apellidomaterno',
            'Fecha de nacimiento' => 'fecha_nacimiento', 'Sexo' => 'sexo', 'Lugar de nacimiento' => 'lugar_nacimiento',
            'Nivel educativo' => 'nivel_educativo_id', 'Grado' => 'grado_id', 'Sección' => 'seccion',
            'Fecha de inscripción' => 'fecha_inscripcion', 'Fecha de inicio' => 'fecha_inicio', 'Domicilio' => 'domicilio',
            'Código postal' => 'cp', 'Ciudad' => 'ciudad', 'Estado' => 'estado',
            'Alergias o padecimientos' => 'enfermedades_alergias',
        ];
        $resumenSecciones = [
            ['titulo' => 'Datos del alumno', 'ancla' => 'datos-alumno', 'icono' => 'fa-id-card', 'importantes' => ['Matrícula', 'Nombre', 'Apellido paterno', 'Apellido materno', 'Fecha de nacimiento', 'Sexo', 'Lugar de nacimiento', 'Nivel educativo', 'Grado', 'Sección', 'Fecha de inscripción', 'Fecha de inicio', 'Domicilio', 'Código postal', 'Ciudad', 'Estado'], 'opcionales' => ['alergias_padecimientos']],
            ['titulo' => 'Contactos', 'ancla' => 'contactos-emergencia', 'icono' => 'fa-phone', 'importantes' => ['Contacto de emergencia completo'], 'opcionales' => ['contacto_alterno', 'tercer_contacto']],
            ['titulo' => 'Familiares', 'ancla' => 'familiares', 'icono' => 'fa-users', 'importantes' => ['Familiar responsable completo'], 'opcionales' => ['telefono_fijo_familiar']],
            ['titulo' => 'Plataformas', 'ancla' => 'plataformas-acceso', 'icono' => 'fa-key', 'importantes' => [], 'opcionales' => ['plataforma_asignada']],
        ];
    @endphp
    @php
        // Valores seguros para que el expediente pueda abrir incluso después de una
        // actualización de vistas o una caché anterior.
        $importantes = collect($estadoExpediente['importantes'] ?? []);
        $opcionales = collect($estadoExpediente['opcionales'] ?? []);
        $anclasPorCampo = $anclasPorCampo ?? [
            'Matrícula' => 'matricula', 'Nombre' => 'nombre', 'Apellido paterno' => 'apellidopaterno', 'Apellido materno' => 'apellidomaterno',
            'Fecha de nacimiento' => 'fecha_nacimiento', 'Sexo' => 'sexo', 'Lugar de nacimiento' => 'lugar_nacimiento',
            'Nivel educativo' => 'nivel_educativo_id', 'Grado' => 'grado_id', 'Sección' => 'seccion',
            'Fecha de inscripción' => 'fecha_inscripcion', 'Fecha de inicio' => 'fecha_inicio', 'Domicilio' => 'domicilio',
            'Código postal' => 'cp', 'Ciudad' => 'ciudad', 'Estado' => 'estado', 'Alergias o padecimientos' => 'enfermedades_alergias',
        ];
        // Respaldo para mantener el expediente disponible aun si una vista compilada
        // anterior no incluyó la definición inicial del resumen.
        $resumenSecciones = $resumenSecciones ?? [
            ['titulo' => 'Datos del alumno', 'ancla' => 'datos-alumno', 'icono' => 'fa-id-card', 'importantes' => ['Matrícula', 'Nombre', 'Apellido paterno', 'Apellido materno', 'Fecha de nacimiento', 'Sexo', 'Lugar de nacimiento', 'Nivel educativo', 'Grado', 'Sección', 'Fecha de inscripción', 'Fecha de inicio', 'Domicilio', 'Código postal', 'Ciudad', 'Estado'], 'opcionales' => ['alergias_padecimientos']],
            ['titulo' => 'Contactos', 'ancla' => 'contactos-emergencia', 'icono' => 'fa-phone', 'importantes' => ['Contacto de emergencia completo'], 'opcionales' => ['contacto_alterno', 'tercer_contacto']],
            ['titulo' => 'Familiares', 'ancla' => 'familiares', 'icono' => 'fa-users', 'importantes' => ['Familiar responsable completo'], 'opcionales' => ['telefono_fijo_familiar']],
            ['titulo' => 'Plataformas', 'ancla' => 'plataformas-acceso', 'icono' => 'fa-key', 'importantes' => [], 'opcionales' => ['plataforma_asignada']],
        ];
    @endphp
    <section class="csl-expediente-summary" aria-label="Resumen del expediente">
        @foreach($resumenSecciones as $seccionResumen)
            @php
                $tieneImportantes = $importantes->intersect($seccionResumen['importantes'])->isNotEmpty();
                $tieneOpcionales = $opcionales->only($seccionResumen['opcionales'])->isNotEmpty();
                $estadoResumen = $tieneImportantes ? 'critical' : ($tieneOpcionales ? 'pending' : 'complete');
                $textoResumen = $tieneImportantes ? 'Requiere atención' : ($tieneOpcionales ? 'Por complementar' : 'Completo');
                $primerDatoPendiente = $importantes->intersect($seccionResumen['importantes'])->first()
                    ?? $opcionales->only($seccionResumen['opcionales'])->first();
                $anclaDestino = $anclasPorCampo[$primerDatoPendiente] ?? $seccionResumen['ancla'];
            @endphp
            <a href="#{{ $anclaDestino }}" class="csl-expediente-summary-card {{ $estadoResumen }}"><span class="csl-expediente-summary-icon"><i class="fas {{ $seccionResumen['icono'] }}"></i></span><span><strong>{{ $seccionResumen['titulo'] }}</strong><small>{{ $textoResumen }}</small></span><i class="fas fa-arrow-right ms-auto"></i></a>
        @endforeach
        <a href="#bitacora-contacto" class="csl-expediente-summary-card">
            <span class="csl-expediente-summary-icon"><i class="fas fa-comments"></i></span>
            <span><strong>Bitácora</strong><small>Comunicación con la familia</small></span>
            <i class="fas fa-arrow-right ms-auto"></i>
        </a>
    </section>
    @if($estadoExpediente['importantes'])
        <div class="alert alert-danger d-flex gap-2 align-items-start" role="alert"><i class="fas fa-circle-exclamation mt-1"></i><div><strong>Expediente incompleto: datos importantes</strong><p class="mb-2">Completa estos campos antes de considerar el expediente terminado:</p><ul class="mb-0">@foreach($estadoExpediente['importantes'] as $campo)<li>{{ $campo }}</li>@endforeach</ul></div></div>
    @endif
    @if($estadoExpediente['opcionales'])
        <div class="alert alert-warning d-flex gap-2 align-items-start" role="status"><i class="fas fa-circle-info mt-1"></i><div><strong>Información opcional por complementar</strong><p class="mb-2">@if($estadoExpediente['importantes']) Además de los datos importantes, si cuentas con ellos puedes agregar: @else El expediente tiene sus datos importantes. Si cuentas con ellos, puedes agregar: @endif</p><ul class="mb-0">@foreach($estadoExpediente['opcionales'] as $clave => $campo)<li class="mb-2"><span>{{ $campo }}</span>@if($puedeEditarExpediente)<form method="POST" action="{{ route('alumnos.campos-opcionales.confirmar', [$alumno, $clave]) }}" class="d-inline ms-2" data-confirm="Confirmarás que este dato no aplica o no está disponible actualmente. Podrás volver a habilitarlo después." data-confirm-title="Confirmar dato opcional" data-confirm-button="Sí, confirmar" data-confirm-variant="primary">@csrf<button class="btn btn-sm btn-outline-secondary">No aplica / no se cuenta</button></form>@endif</li>@endforeach</ul></div></div>
    @endif
    @if($alumno->confirmacionesOpcionales->isNotEmpty())
        <div class="alert alert-info d-flex gap-2 align-items-start" role="status"><i class="fas fa-circle-check mt-1"></i><div><strong>Información opcional confirmada</strong><p class="mb-2">Estos datos se marcaron como no aplicables o no disponibles. Puedes reabrirlos si más adelante se obtienen:</p><ul class="mb-0">@foreach($alumno->confirmacionesOpcionales as $confirmacion)<li class="mb-2">{{ $camposOpcionalesConfirmables[$confirmacion->campo] ?? $confirmacion->campo }}@if($puedeEditarExpediente)<form method="POST" action="{{ route('alumnos.campos-opcionales.reabrir', [$alumno, $confirmacion->campo]) }}" class="d-inline ms-2">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-primary">Volver a solicitar</button></form>@endif</li>@endforeach</ul></div></div>
    @endif
    @if(! $estadoExpediente['importantes'] && ! $estadoExpediente['opcionales'])
        <div class="alert alert-success d-flex gap-2" role="status"><i class="fas fa-circle-check mt-1"></i><div><strong>Expediente completo.</strong> Tiene la información importante y complementaria registrada.</div></div>
    @endif

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

    <form id="studentEditForm" action="{{ route('alumnos.update', $alumno->id) }}" method="POST" class="csl-student-form-body {{ $puedeEditarExpediente ? 'csl-readonly-form' : 'csl-coordination-view' }}">
        @csrf
        @method('PUT')
        <div class="row">

<!-- Información del Alumno en columnas -->
<div class="col-md-12"><br>

@if (Auth::user()->hasAnyRole(['SuperAdmin', 'ControlEscolar', 'CoordinacionPreescolar', 'CoordinacionPrimaria', 'CoordinacionSecundaria']))
        <div id="plataformas-acceso" class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
        <div class="card-body">
            <h5 class="card-title text-primary">Datos de Usuario</h5>

            @php
                // Define las plataformas por nivel educativo
                // Plataformas configuradas para el nivel, sin depender de valores fijos.
                $plataformasDisponibles = $alumno->nivelEducativo?->plataformas?->where('activo', true)->pluck('id')->all() ?? [];
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
                            value="{{ old('plataformas.'.$plataformaId.'.usuario', $plataformaExistente->usuario ?? '') }}" @unless($puedeEditarExpediente) disabled data-platform-access @endunless>
                    </div>
                    <div class="col-md-6">
                        <label for="plataformas_{{ $plataformaId }}_contraseña" class="form-label"><strong>Contraseña {{ ucfirst($plataformaNombre) }}</strong></label>
                        <input type="text" class="form-control" 
                            id="plataformas_{{ $plataformaId }}_contraseña" 
                            name="plataformas[{{ $plataformaId }}][contraseña]" 
                            value="{{ old('plataformas.'.$plataformaId.'.contraseña', $plataformaExistente->contraseña ?? '') }}" @unless($puedeEditarExpediente) disabled data-platform-access @endunless>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif



    <div id="datos-alumno" class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
        <div class="card-body">
            <h5 class="card-title text-primary">Datos del Alumno</h5>

            <input type="hidden" name="nivel_educativo_id" value="{{ old('nivel_educativo_id', $alumno->nivel_educativo_id) }}">

            <!-- Sección 1: Datos Básicos -->
            <div class="section mb-4">
                <div class="row mb-3">
                    
                </div>
                <div class="row mb-3">
                    @if (Auth::user()->hasRole('SuperAdmin'))
                     <div class="col-md-3">
                        <label for="nombre" class="form-label"><strong>Matricula</strong></label>
                        <input type="text" class="form-control" id="matricula" name="matricula" value="{{ $alumno->matricula }}" readonly aria-readonly="true">
                    </div>
                    @endif
                    <div class="col-md-3">
                        <label for="nombre" class="form-label"><strong>Nombre</strong></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $alumno->nombre) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" inputmode="text" style="text-transform: capitalize;" placeholder="Escribe el nombre" title="El nombre solo debe contener letras y espacios y comenzar con mayúscula." readonly>
                    </div>

                    <div class="col-md-3">
                        <label for="apellidopaterno" class="form-label"><strong>Apellido Paterno</strong></label>
                        <input type="text" class="form-control" id="apellidopaterno" name="apellidopaterno" value="{{ old('apellidopaterno', $alumno->apellidopaterno) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" inputmode="text" style="text-transform: capitalize;" placeholder="Escribe su Apellido Paterno" title="El apellido paterno solo debe contener letras y espacios y comenzar con mayúscula." readonly>
                    </div>

                    <div class="col-md-3">
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
        <div class="row mb-3 hermano-existente">
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
            @if($puedeEditarExpediente)<div class="col-12 mt-2"><button type="button" class="btn btn-outline-danger btn-sm eliminar-hermano-existente" data-edit-action disabled><i class="fas fa-trash me-1"></i>Quitar hermano</button></div>@endif
        </div>
    @endforeach
    <div id="hermanos-adicionales"></div>

<div class="d-flex justify-content-center">
    @if($puedeEditarExpediente)<button type="button" class="btn btn-primary" id="agregar-hermano" data-edit-action disabled>Agregar otro hermano</button>@endif
</div>
<p class="text-muted mt-2">Agrega los hermanos que necesites. Los campos son opcionales.</p>

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
                        <select class="form-select" id="grado_id" name="grado_id" required disabled onchange="cargarSecciones(this.value)">
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
                        <select class="form-select" id="nivel_educativo_id" name="nivel_educativo_id" required disabled onchange="cargarGrados(this.value)">
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
                        <label for="seccion" class="form-label"><strong>Sección</strong></label>
                        <select class="form-select" id="seccion" name="seccion" disabled>
                            <option value="">Sin sección</option>
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion['nombre'] }}" @selected(old('seccion', $alumno->seccion) === $seccion['nombre'])>{{ $seccion['nombre'] }}</option>
                            @endforeach
                        </select>
                        <small class="text-secondary">Las secciones dependen del grado seleccionado.</small>
                    </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Información de Contactos dentro de una tarjeta -->
        <div id="contactos-emergencia" class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
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
                        @if($puedeEditarExpediente)<button type="button" class="btn btn-primary" id="agregar-contacto" data-edit-action disabled>Agregar otro contacto</button>@endif
                    </div>

            </div>
        </div>
    </div>
</div>

    @foreach($familiares as $familiar)
        <div id="{{ $loop->first ? 'familiares' : 'familiar-' . $loop->iteration }}" class="card shadow-sm mb-4 {{ $puedeEditarExpediente ? 'csl-readonly-form' : 'csl-coordination-view' }}" style="background-color: #f8f9fa;">
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
                        <label class="form-label"><strong>Teléfono celular</strong> <span class="text-secondary">(opcional)</span></label>
                        <input type="tel" inputmode="numeric" class="form-control" name="familiares[{{ $loop->index }}][celular]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.celular', $familiar->celular ?? '') }}" maxlength="15" placeholder="10 dígitos, si cuenta con uno" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><strong>Teléfono fijo</strong> <span class="text-secondary">(opcional)</span></label>
                        <input type="tel" inputmode="numeric" class="form-control" name="familiares[{{ $loop->index }}][telefono_fijo]"
                            value="{{ old('familiares.'.$familiar->tipo_familiar.'.telefono_fijo', $familiar->telefono_fijo ?? '') }}" maxlength="15" placeholder="10 dígitos, si cuenta con uno" readonly>
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
@if ($puedeEditarExpediente && $familiaresCount < 3)
    <div class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
        <div class="card-body">
            <h5 class="card-title text-primary">Agregar Familiares</h5>

            <!-- Verifica si ya hay menos de 3 familiares registrados -->
             <center>
            <div id="agregar-familiares-container">
                <button type="button" class="btn btn-primary" id="agregar-familiares" data-edit-action disabled>Agregar Familiares</button>
            </div>
            </center>

            <!-- Sección donde se agregarán los familiares -->
            <div id="familiares-container" class="mt-3"></div>
        </div>
    </div>
@endif
    </form>

    <section class="card csl-data-card mt-4" id="bitacora-contacto">
        <div class="card-header">
            <div>
                <h2 class="card-title mb-1"><i class="fas fa-comments me-2"></i>Seguimiento de comunicación con la familia</h2>
                <p class="text-secondary small mb-0">Historial de llamadas, correos, reuniones o acuerdos. No modifica los datos del alumno.</p>
            </div>
        </div>
        <div class="card-body">
            @if($puedeEditarExpediente)
                <div class="alert alert-info d-flex gap-2 align-items-start mb-4" role="note"><i class="fas fa-circle-info mt-1"></i><div><strong>¿Cuándo se usa?</strong><div class="small mt-1">Después de comunicarte con padre, madre o tutor. Elige el medio, revisa la fecha, escribe un resumen breve y guarda el registro.</div></div></div>
                <form method="POST" action="{{ route('alumnos.bitacora-contacto.registrar', $alumno) }}" class="row g-3 mb-4">
                    @csrf
                    <div class="col-md-4"><label class="form-label" for="contacto-tipo">¿Cómo se realizó la comunicación?</label><select id="contacto-tipo" name="tipo" class="form-select" required><option value="llamada">Llamada telefónica</option><option value="correo">Correo electrónico</option><option value="reunion">Reunión</option><option value="mensaje">Mensaje</option><option value="otro">Otro medio</option></select></div>
                    <div class="col-md-4"><label class="form-label" for="contacto-fecha">¿Cuándo ocurrió?</label><input id="contacto-fecha" name="fecha_contacto" type="datetime-local" class="form-control" value="{{ now()->format('Y-m-d\\TH:i') }}" required></div>
                    <div class="col-12"><label class="form-label" for="contacto-resumen">¿Qué se habló o acordó?</label><textarea id="contacto-resumen" name="resumen" class="form-control" rows="4" maxlength="1500" required placeholder="Ejemplo: Se habló con la madre sobre la actualización de datos y se acordó entregar el documento el viernes."></textarea><small class="text-secondary">El resumen es obligatorio. Evita incluir contraseñas u otra información sensible.</small></div>
                    <div class="col-12"><button class="btn btn-primary" type="submit"><i class="fas fa-floppy-disk me-2"></i>Guardar en el historial</button></div>
                </form>
            @else
                <div class="alert alert-secondary mb-4"><i class="fas fa-lock me-2"></i>Coordinación puede consultar este historial como referencia. Administración y Control Escolar registran nuevas comunicaciones.</div>
            @endif
            <div class="table-responsive"><table class="table table-vcenter mb-0"><thead><tr><th>Fecha</th><th>Medio</th><th>Resumen</th><th>Registró</th></tr></thead><tbody>@forelse($alumno->bitacorasContacto as $registro)<tr><td class="text-nowrap">{{ $registro->fecha_contacto->format('d/m/Y H:i') }}</td><td><span class="badge bg-azure-lt">{{ ucfirst($registro->tipo) }}</span></td><td>{{ $registro->resumen }}</td><td>{{ $registro->usuario?->name ?? 'Sin registro' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-secondary py-4">Aún no hay contactos registrados con la familia.</td></tr>@endforelse</tbody></table></div>
        </div>
    </section>

</div>
<br><br>
<!-- Script para habilitar los campos y mostrar los botones correctos -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    window.habilitarContrasenasCoordinacion = function () {
        document.querySelectorAll('[data-platform-access]').forEach((field) => field.removeAttribute('disabled'));
        document.getElementById('saveCredentialsBtn')?.style.setProperty('display', 'inline-block');
        document.getElementById('editCredentialsBtn')?.style.setProperty('display', 'none');
        const status = document.getElementById('edit-status');
        if (status) {
            status.className = 'alert alert-warning d-flex gap-2';
            status.innerHTML = '<i class="fas fa-key mt-1"></i><div><strong>Edición de accesos.</strong> Modifica únicamente usuarios y contraseñas de las plataformas y guarda al terminar.</div>';
        }
    };

    // Funcionalidad de habilitar campos cuando el SuperAdmin hace click en "Editar"
    document.getElementById('editBtn')?.addEventListener('click', function() {
        // Hacer los campos input editables
        let fields = document.querySelectorAll('input');
        fields.forEach(field => field.removeAttribute('readonly'));

        // Hacer los campos select habilitables
        let selects = document.querySelectorAll('select');
        selects.forEach(select => select.removeAttribute('disabled'));

        // Mostrar el botón de actualizar
        document.getElementById('updateBtn').style.display = 'inline-block';
        this.style.display = 'none'; // Ocultar el botón de editar
        document.querySelectorAll('.csl-readonly-form').forEach((section) => section.classList.remove('csl-readonly-form'));
        document.querySelectorAll('[data-edit-action]').forEach((button) => { button.disabled = false; });
        document.getElementById('edit-status').className = 'alert alert-warning d-flex gap-2';
        document.getElementById('edit-status').innerHTML = '<i class="fas fa-pen-to-square mt-1"></i><div><strong>Modo edición.</strong> Modifica únicamente los datos necesarios y pulsa “Guardar cambios” al terminar.</div>';
    });

    document.getElementById('editCredentialsBtn')?.addEventListener('click', function() {
        window.habilitarContrasenasCoordinacion();
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

                cargarSecciones('');
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

function cargarSecciones(gradoId) {
    const seccionSelect = document.getElementById('seccion');

    if (!seccionSelect) {
        return;
    }

    seccionSelect.innerHTML = '<option value="">Sin sección</option>';

    if (!gradoId) {
        return;
    }

    fetch(`/secciones/${gradoId}`)
        .then(response => response.json())
        .then(data => {
            data.forEach(seccion => {
                const option = document.createElement('option');
                option.value = seccion.nombre;
                option.textContent = seccion.nombre;
                seccionSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error cargando las secciones:', error));
}
let hermanoCount = {{ $alumno->hermanos->count() }};
    let nuevoHermanoIndex = {{ $alumno->hermanos->count() }};
    const agregarHermanoButton = document.getElementById('agregar-hermano');
    const hermanosAdicionales = document.getElementById('hermanos-adicionales');

    agregarHermanoButton?.addEventListener('click', function () {
        {
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

            });

            hermanosAdicionales.appendChild(hermanoDiv);

        }
    });
    document.querySelectorAll('.eliminar-hermano-existente').forEach((button) => button.addEventListener('click', () => button.closest('.hermano-existente').remove()));
    let contactoCount = 0; // Comienza en 0
const maxContactos = 3;
const agregarContactoButton = document.getElementById('agregar-contacto');
const contactosAdicionales = document.getElementById('contactos-adicionales');

agregarContactoButton?.addEventListener('click', function () {
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
document.getElementById('agregar-familiares')?.addEventListener('click', function() {
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
                        <label class="form-label"><strong>Teléfono celular</strong> <span class="text-secondary">(opcional)</span></label>
                        <input type="tel" inputmode="numeric" class="form-control" name="familiares[${tipo}][celular]" pattern="\d{10}" maxlength="10" placeholder="10 dígitos, si cuenta con uno">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><strong>Teléfono fijo</strong> <span class="text-secondary">(opcional)</span></label>
                        <input type="tel" inputmode="numeric" class="form-control" name="familiares[${tipo}][telefono_fijo]" pattern="\d{10}" maxlength="10" placeholder="10 dígitos, si cuenta con uno">
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

// El expediente se consulta por secciones para evitar una página muy larga.
// Los datos continúan dentro del mismo formulario y se muestran todos antes
// de guardar, por lo que la validación y el guardado no cambian.
(function inicializarNavegacionExpediente() {
    const summary = document.querySelector('.csl-expediente-summary');
    const form = document.getElementById('studentEditForm');
    if (!summary || !form) return;

    const familiares = Array.from(form.querySelectorAll('#familiares, [id^="familiar-"]'));
    const tarjetaAgregarFamiliar = document.getElementById('agregar-familiares')?.closest('.card');
    if (tarjetaAgregarFamiliar) familiares.push(tarjetaAgregarFamiliar);

    const pasos = [
        { clave: 'datos', titulo: 'Datos del alumno', elementos: Array.from(form.querySelectorAll('#datos-alumno')) },
        { clave: 'contactos', titulo: 'Contactos', elementos: Array.from(form.querySelectorAll('#contactos-emergencia')) },
        { clave: 'familiares', titulo: 'Familiares', elementos: familiares },
        { clave: 'plataformas', titulo: 'Plataformas', elementos: Array.from(form.querySelectorAll('#plataformas-acceso')) },
        { clave: 'bitacora', titulo: 'Bitácora de comunicación', elementos: Array.from(document.querySelectorAll('#bitacora-contacto')) },
    ].filter((paso) => paso.elementos.length);

    if (pasos.length < 2) return;

    const selector = document.createElement('section');
    selector.className = 'csl-expediente-stepper';
    selector.setAttribute('aria-label', 'Navegación del expediente');
    selector.innerHTML = `
        <div class="csl-expediente-stepper-copy"><strong id="expediente-paso-titulo"></strong><small id="expediente-paso-contador"></small></div>
        <div class="csl-expediente-stepper-actions">
            <button type="button" class="btn btn-outline-secondary" id="expediente-paso-anterior"><i class="ti ti-chevron-left"></i> Anterior</button>
            <button type="button" class="btn btn-primary" id="expediente-paso-siguiente">Siguiente <i class="ti ti-chevron-right"></i></button>
        </div>`;
    summary.insertAdjacentElement('afterend', selector);

    const title = selector.querySelector('#expediente-paso-titulo');
    const counter = selector.querySelector('#expediente-paso-contador');
    const previous = selector.querySelector('#expediente-paso-anterior');
    const next = selector.querySelector('#expediente-paso-siguiente');
    let pasoActual = {{ $puedeEditarExpediente ? '0' : "Math.max(0, pasos.findIndex((paso) => paso.clave === 'plataformas'))" }};

    const mostrarPaso = (index, moverVista = true) => {
        pasoActual = Math.max(0, Math.min(index, pasos.length - 1));
        pasos.forEach((paso, indice) => paso.elementos.forEach((elemento) => {
            elemento.classList.toggle('csl-expediente-step-hidden', indice !== pasoActual);
        }));
        title.textContent = pasos[pasoActual].titulo;
        counter.textContent = `Sección ${pasoActual + 1} de ${pasos.length}`;
        previous.disabled = pasoActual === 0;
        next.innerHTML = pasoActual === pasos.length - 1
            ? 'Finalizar revisión <i class="ti ti-check"></i>'
            : 'Siguiente <i class="ti ti-chevron-right"></i>';
        summary.querySelectorAll('.csl-expediente-summary-card').forEach((card) => card.classList.remove('is-active'));
        const tarjetaActiva = summary.querySelector(`[href="#${pasos[pasoActual].elementos[0].id}"]`);
        tarjetaActiva?.classList.add('is-active');
        // Los controles acompañan el final de la sección actual: primero se
        // revisan los datos y después se avanza, sin tener que volver arriba.
        const ultimoElemento = pasos[pasoActual].elementos[pasos[pasoActual].elementos.length - 1];
        ultimoElemento?.insertAdjacentElement('afterend', selector);
        if (moverVista) pasos[pasoActual].elementos[0]?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const encontrarPaso = (elemento) => pasos.findIndex((paso) => paso.elementos.some((contenedor) => contenedor === elemento || contenedor.contains(elemento)));
    const abrirEnlace = (event) => {
        const destino = document.querySelector(event.currentTarget.getAttribute('href'));
        const indice = destino ? encontrarPaso(destino) : -1;
        if (indice < 0) return;
        event.preventDefault();
        mostrarPaso(indice);
        destino.focus?.({ preventScroll: true });
    };

    summary.querySelectorAll('.csl-expediente-summary-card').forEach((card) => card.addEventListener('click', abrirEnlace));
    document.querySelectorAll('.csl-form-nav a').forEach((card) => card.addEventListener('click', abrirEnlace));
    previous.addEventListener('click', () => mostrarPaso(pasoActual - 1));
    next.addEventListener('click', () => {
        if (pasoActual < pasos.length - 1) mostrarPaso(pasoActual + 1);
    });

    // Al guardar se muestran todas las secciones para que el navegador pueda
    // señalar claramente cualquier dato obligatorio pendiente.
    document.getElementById('updateBtn')?.addEventListener('click', () => {
        pasos.forEach((paso) => paso.elementos.forEach((elemento) => elemento.classList.remove('csl-expediente-step-hidden')));
    });

    mostrarPaso(pasoActual, false);
})();
</script>
@endsection

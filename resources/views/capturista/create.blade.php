@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="text-center mb-4">
        <h1 class="display-4 text-primary">Registro de Alumno</h1>
        <p class="lead">Ingresa los datos del alumno a continuación:</p>
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

    <form action="{{ route('alumnos.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Información del Alumno en columnas -->
            <div class="col-md-12">
                <div class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Datos del Alumno</h5>

                        <!-- Matrícula en su propia fila centrado -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-md-6">
                                <label for="matricula" class="form-label text-center d-block"><strong>Matrícula</strong></label>
                                <input type="number" class="form-control" id="matricula" name="matricula" value="{{ old('matricula') }}" required min="1" placeholder="Ej. 123456">
                            </div>
                        </div>

                        <!-- Los demás campos en una sola fila -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label"><strong>Nombre</strong></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo letras y espacios" placeholder="Ej. Juan">
                            </div>

                            <div class="col-md-6">
                                <label for="apellidopaterno" class="form-label"><strong>Apellido Paterno</strong></label>
                                <input type="text" class="form-control" id="apellidopaterno" name="apellidopaterno" value="{{ old('apellidopaterno') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="Ej. Pérez">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="apellidomaterno" class="form-label"><strong>Apellido Materno</strong></label>
                                <input type="text" class="form-control" id="apellidomaterno" name="apellidomaterno" value="{{ old('apellidomaterno') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="Ej. Gómez">
                            </div>

                            <input type="hidden" name="nivel_educativo_id" value="{{ $nivel_id }}">

                            <div class="col-md-6">
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
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fecha_inscripcion" class="form-label"><strong>Fecha de Inscripción</strong></label>
                                <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" value="{{ old('fecha_inscripcion') }}" required placeholder="Ej. 2025-03-01">
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_inicio" class="form-label"><strong>Fecha de Inicio</strong></label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}" required placeholder="Ej. 2025-08-15">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Contactos -->
                <div class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Información de Contactos</h5>

                        <!-- Primer Contacto -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contacto1nombre" class="form-label"><strong>Nombre del Primer Contacto</strong></label>
                                <input type="text" class="form-control" id="contacto1nombre" name="contacto1nombre" value="{{ old('contacto1nombre') }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" placeholder="Ej. María Pérez">
                            </div>

                            <div class="col-md-6">
                                <label for="telefono1" class="form-label"><strong>Teléfono del Primer Contacto</strong></label>
                                <input type="number" class="form-control" id="telefono1" name="telefono1" value="{{ old('telefono1') }}" maxlength="12" required pattern="\d{12}" title="Debe ser un número de 12 dígitos" placeholder="Ej. 551234567890">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="correo1" class="form-label"><strong>Correo del Primer Contacto</strong></label>
                                <input type="email" class="form-control" id="correo1" name="correo1" value="{{ old('correo1') }}" required placeholder="Ej. contacto@correo.com">
                            </div>

                            <div class="col-md-6">
                                <label for="contacto1tipo" class="form-label"><strong>Tipo de Contacto</strong></label>
                                <select class="form-select" id="contacto1tipo" name="contacto1tipo" required>
                                    <option value="">Selecciona tipo</option>
                                    <option value="madre" {{ old('contacto1tipo') == 'madre' ? 'selected' : '' }}>Madre</option>
                                    <option value="padre" {{ old('contacto1tipo') == 'padre' ? 'selected' : '' }}>Padre</option>
                                    <option value="tutor" {{ old('contacto1tipo') == 'tutor' ? 'selected' : '' }}>Tutor</option>
                                </select>
                            </div>
                        </div>

                        <!-- Agregar Contactos Adicionales -->
                        <div id="contactos-adicionales"></div>

                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-info" id="agregar-contacto">Agregar otro contacto</button>
                        </div>

                        <p class="text-muted mt-2">Puedes agregar hasta 3 contactos. Los campos de contacto son opcionales.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-success btn-lg">Registrar Alumno</button>
        </div>
    </form>
</div>

<script>
    let contactoCount = 1;
    const maxContactos = 3;
    const agregarContactoButton = document.getElementById('agregar-contacto');
    const contactosAdicionales = document.getElementById('contactos-adicionales');

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
                            <input type="text" class="form-control" id="contacto${contactoCount}nombre" name="contacto${contactoCount}nombre" placeholder="Ej. Juan Pérez">
                        </div>
                        <div class="col-md-6">
                            <label for="telefono${contactoCount}" class="form-label"><strong>Teléfono del Contacto ${contactoCount}</strong></label>
                            <input type="number" class="form-control" id="telefono${contactoCount}" name="telefono${contactoCount}" maxlength="12" placeholder="Ej. 551234567890">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="correo${contactoCount}" class="form-label"><strong>Correo del Contacto ${contactoCount}</strong></label>
                            <input type="email" class="form-control" id="correo${contactoCount}" name="correo${contactoCount}" placeholder="Ej. contacto${contactoCount}@correo.com">
                        </div>
                        <div class="col-md-6">
                            <label for="contacto${contactoCount}tipo" class="form-label"><strong>Tipo de Contacto</strong></label>
                            <select class="form-select" id="contacto${contactoCount}tipo" name="contacto${contactoCount}tipo">
                                <option value="">Selecciona tipo</option>
                                <option value="madre">Madre</option>
                                <option value="padre">Padre</option>
                                <option value="tutor">Tutor</option>
                            </select>
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
</script>
@endsection

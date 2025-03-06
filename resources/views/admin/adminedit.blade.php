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
            <div class="col-md-12">
                <div class="card shadow-sm mb-4" style="background-color: #f8f9fa;">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Datos del Alumno</h5>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-md-6">
                                <label for="matricula" class="form-label text-center d-block">Matrícula</label>
                                <input type="number" class="form-control" id="matricula" name="matricula" value="{{ old('matricula', $alumno->matricula) }}" required min="1" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $alumno->nombre) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo letras y espacios" readonly>
                            </div>

                            <div class="col-md-6">
                                <label for="apellidopaterno" class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" id="apellidopaterno" name="apellidopaterno" value="{{ old('apellidopaterno', $alumno->apellidopaterno) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="apellidomaterno" class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" id="apellidomaterno" name="apellidomaterno" value="{{ old('apellidomaterno', $alumno->apellidomaterno) }}" required pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" readonly>
                            </div>

                            <div class="col-md-6">
                                <label for="grado_id" class="form-label">Grado</label>
                                <select class="form-select" id="grado_id" name="grado_id" required disabled>
                                    <option value="">Selecciona un grado</option>
                                    @if($grados && $grados->isNotEmpty())
                                        @foreach ($grados as $grado)
                                            <option value="{{ $grado->id }}" {{ old('grado_id', $alumno->grado_id) == $grado->id ? 'selected' : '' }}>{{ $grado->nombre }}</option>
                                        @endforeach
                                    @else
                                        <option value="">No hay grados disponibles</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
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

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary" id="updateBtn" style="display:none;"><i class="fas fa-save"></i> Actualizar Alumno</button>
            <a href="{{ route('capturista.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
            <button type="button" class="btn btn-warning" id="editBtn"><i class="fas fa-pencil-alt"></i> Editar</button>
        </div>
    </form>
</div>

<script>
 document.getElementById('editBtn').addEventListener('click', function() {
    let fields = document.querySelectorAll('input');
    fields.forEach(field => field.removeAttribute('readonly'));

    let selects = document.querySelectorAll('select');
    selects.forEach(select => select.removeAttribute('disabled'));

    document.getElementById('updateBtn').style.display = 'inline-block';  // Mostrar botón de actualizar
    this.style.display = 'none';  // Ocultar botón de editar
});
</script>

@endsection

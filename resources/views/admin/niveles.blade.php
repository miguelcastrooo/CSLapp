@extends('layouts.app')

@section('content')
@isset($nivelEducativo)
<br><br>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<h1 class="text-center mb-4" style="color: #333;">{{ $nivelEducativo->nombre }} - Información de Alumnos</h1><br>

<!-- Filtros -->
<div class="filters mb-4">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Filtros de Alumnos</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Grado -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="gradoSelect" class="font-weight-bold"><strong>Filtrar por Grado</strong></label>
                        <select id="gradoSelect" class="form-control">
                            <option value="">Todos</option>
                            @foreach ($grados as $grado)
                            <option value="{{ $grado->nombre }}">{{ $grado->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Sección -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold"><strong>Filtrar por Sección</strong></label>
                        <div class="d-flex flex-wrap">
                            <div class="form-check mr-3">
                                <input type="checkbox" class="form-check-input seccion-check" value="a" id="seccion-a">
                                <label class="form-check-label" for="seccion-a">A</label>
                            </div>
                            <div class="form-check mr-3">
                                <input type="checkbox" class="form-check-input seccion-check" value="b" id="seccion-b">
                                <label class="form-check-label" for="seccion-b">B</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plataforma -->
                @php
                $plataformas = $alumnos->flatMap(function($alumno) {
                    return $alumno->alumnoPlataforma->map(function($ap) {
                        return $ap->plataforma->nombre ?? $ap->nombre;
                    });
                })->unique()->sort();
                @endphp
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold"><strong>Filtrar por Plataforma</strong></label>
                        <select id="plataformaSelect" class="form-control">
                            <option value="">Todas</option>
                            @foreach ($plataformas as $plataforma)
                            <option value="{{ $plataforma }}">{{ $plataforma }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Buscar -->
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label for="searchBox" class="font-weight-bold"><strong>Buscar Alumno</strong></label>
                        <input type="text" id="searchBox" class="form-control" placeholder="Nombre o Matrícula">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('admin.select') }}" class="btn btn-primary">Volver</a>

<!-- Tabla -->
<div class="table-responsive mt-4">
    <table class="table table-striped table-bordered table-sm">
        <thead class="thead-light">
            <tr>
                <th class="text-center">Matrícula</th>
                <th class="text-center">Alumno</th>
                <th class="text-center">Grado</th>
                <th class="text-center">Sección</th>
                <th class="text-center">Plataforma</th>
                <th class="text-center">Usuario</th>
                <th class="text-center">Contraseña</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody id="alumnosTableBody">
            @foreach ($alumnos as $alumno)
                @foreach ($alumno->alumnoPlataforma ?? [] as $index => $plataforma)
                <tr class="{{ $loop->first ? 'fila-principal' : '' }}"
                    data-grado="{{ strtolower($alumno->grado->nombre ?? '') }}"
                    data-seccion="{{ strtolower($alumno->seccion ?? '') }}"
                    data-plataforma="{{ strtolower($plataforma->plataforma->nombre ?? $plataforma->nombre ?? '') }}"
                    data-nombre="{{ strtolower($alumno->nombre . ' ' . $alumno->apellidopaterno . ' ' . $alumno->apellidomaterno) }}"
                    data-matricula="{{ strtolower($alumno->matricula) }}"
                >
                    @if ($loop->first)
                    <td rowspan="{{ count($alumno->alumnoPlataforma) }}" class="text-center align-middle">{{ $alumno->matricula }}</td>
                    <td rowspan="{{ count($alumno->alumnoPlataforma) }}" class="align-middle">{{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</td>
                    <td rowspan="{{ count($alumno->alumnoPlataforma) }}" class="text-center align-middle">{{ $alumno->grado->nombre ?? 'N/A' }}</td>
                    <td rowspan="{{ count($alumno->alumnoPlataforma) }}" class="text-center align-middle">{{ $alumno->seccion ?? 'N/A' }}</td>
                    @endif

                    <td class="text-center">{{ $plataforma->plataforma->nombre ?? $plataforma->nombre ?? 'N/A' }}</td>
                    <td class="text-center">{{ $plataforma->usuario ?? 'N/A' }}</td>
                    <td class="text-center">{{ $plataforma->contraseña ?? 'N/A' }}</td>

                    @if ($loop->first)
                    <td rowspan="{{ count($alumno->alumnoPlataforma) }}" class="text-center align-middle">
                        <div class="d-flex flex-column align-items-center">
                            <a href="{{ route('admin.alumnos.pdf.individual', ['nivel' => $nivelEducativo->nombre, 'id' => $alumno->id]) }}" class="btn btn-danger btn-sm mb-1">Generar PDF</a>
                            <div class="btn-group mb-1">
                                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Enviar Correo</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.enviarCorreo', ['alumnoId' => $alumno->id, 'destinatario' => 'padre']) }}">Solo al Padre</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.enviarCorreo', ['alumnoId' => $alumno->id, 'destinatario' => 'madre']) }}">Solo a la Madre</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.enviarCorreo', ['alumnoId' => $alumno->id, 'destinatario' => 'ambos']) }}">A Ambos</a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class="text-center mt-4">No hay alumnos disponibles para este nivel.</p>
@endisset
@endsection

@section('styles')
<style>
    body {
        background-color: #f0f0f0;
        font-family: 'Arial', sans-serif;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f2f2f2 !important;
    }

    .table-bordered th, .table-bordered td {
        border: 1px solid #ddd !important;
    }

    .table th {
        background-color: rgb(0, 0, 0) !important;
        color: #fff !important;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa !important;
    }

    .btn-danger, .btn-info {
        width: 100% !important;
        text-align: center !important;
    }

    .alert {
        text-align: center;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const gradoSelect = document.getElementById('gradoSelect');
    const plataformaSelect = document.getElementById('plataformaSelect');
    const searchBox = document.getElementById('searchBox');
    const seccionChecks = document.querySelectorAll('.seccion-check');
    const filas = document.querySelectorAll('#alumnosTableBody tr');

    function filterAlumnos() {
        const grado = gradoSelect.value.toLowerCase();
        const plataforma = plataformaSelect.value.toLowerCase();
        const search = searchBox.value.toLowerCase();

        const seccionesSeleccionadas = Array.from(seccionChecks)
            .filter(chk => chk.checked)
            .map(chk => chk.value.toLowerCase());

        filas.forEach(fila => {
            const filaGrado = fila.dataset.grado ?? '';
            const filaSeccion = fila.dataset.seccion ?? '';
            const filaPlataforma = fila.dataset.plataforma ?? '';
            const filaNombre = fila.dataset.nombre ?? '';
            const filaMatricula = fila.dataset.matricula ?? '';

            let mostrar = true;

            if (grado && filaGrado !== grado) mostrar = false;
            if (seccionesSeleccionadas.length > 0 && !seccionesSeleccionadas.includes(filaSeccion)) mostrar = false;
            if (plataforma && !filaPlataforma.includes(plataforma)) mostrar = false;
            if (search && !(filaNombre.includes(search) || filaMatricula.includes(search))) mostrar = false;

            fila.style.display = mostrar ? '' : 'none';
        });
    }

    gradoSelect.addEventListener('change', filterAlumnos);
    plataformaSelect.addEventListener('change', filterAlumnos);
    searchBox.addEventListener('input', filterAlumnos);
    seccionChecks.forEach(chk => chk.addEventListener('change', filterAlumnos));
});
</script>
@endsection

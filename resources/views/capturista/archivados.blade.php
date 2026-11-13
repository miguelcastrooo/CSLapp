@extends('layouts.app')

@section('content')
<div class="container-xl">

    <!-- Mostrar mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="csl-module-header"><h1>Alumnos archivados</h1><p>Consulta el motivo de baja y reactiva expedientes cuando corresponda.</p></div>
    <x-task-guide title="Consulta y reactivación" description="Aquí permanecen los expedientes dados de baja; no se han eliminado." :steps="['Usa los filtros para localizar el expediente.', 'Consulta el motivo y la fecha de baja.', 'Reactiva solo cuando la información esté confirmada.']" />

    {{-- Filtros dinámicos --}}
    <div class="card csl-filter-card mb-4"><div class="card-body"><div class="row g-3">
        <div class="col-md-3">
            <select id="nivelFilter" class="form-control">
                <option value="">Filtrar por Nivel</option>
                @foreach ($niveles as $nivel)
                    <option value="{{ $nivel->id }}" {{ request('nivel_educativo_id') == $nivel->id ? 'selected' : '' }}>
                        {{ $nivel->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select id="gradoFilter" class="form-control" disabled>
                <option value="">Filtrar por Grado</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="seccionFilter" class="form-control">
                <option value="">Filtrar por Sección</option>
                <option value="A" {{ request('seccion') == 'A' ? 'selected' : '' }}>A</option>
                <option value="B" {{ request('seccion') == 'B' ? 'selected' : '' }}>B</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" id="searchBox" class="form-control" placeholder="Buscar por nombre o matrícula..." value="{{ request('search') }}">
        </div>
    </div></div></div>

    {{-- Tabla de alumnos archivados --}}
    <div class="card csl-data-card"><div class="table-responsive">
    <table class="table table-striped table-bordered" id="alumnosTable" style="background: white;">
    <thead class="table-dark">
    <tr>
                <th>Matrícula</th>
                <th>Nombre</th>
                <th>Nivel Educativo</th>
                <th>Grado</th>
                <th>Sección</th>
                <th>Detalle de baja</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($alumnosArchivados as $alumno)
                @php
                    $baja = $historialBajas->get($alumno->id) ?? $bajasAnteriores->get($alumno->id);
                    $fechaBaja = $baja ? \Illuminate\Support\Carbon::parse($baja->fecha ?? $baja->fecha_baja)->format('d/m/Y H:i') : 'No disponible';
                    $responsableBaja = isset($baja?->usuario) ? $baja->usuario->name : 'Sin registro anterior';
                @endphp
                <tr data-nivel="{{ $alumno->nivel_educativo_id }}" 
                    data-grado="{{ $alumno->grado_id }}" 
                    data-seccion="{{ $alumno->seccion }}">
                    <td>{{ $alumno->matricula }}</td>
                    <td>{{ $alumno->nombre }} {{ $alumno->apellidopaterno }}</td>
                    <td>{{ $alumno->nivelEducativo->nombre ?? 'N/A' }}</td>
                    <td>{{ $alumno->grado->nombre ?? 'N/A' }}</td>
                    <td>{{ $alumno->seccion }}</td>
                    <td>
                        <div class="small fw-semibold">{{ $fechaBaja }}</div>
                        @if($baja)
                            <button type="button" class="btn btn-outline-primary btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#detalleBajaModal" data-motivo="{{ $baja->motivo }}" data-fecha="{{ $fechaBaja }}" data-responsable="{{ $responsableBaja }}">Ver motivo</button>
                        @else
                            <span class="text-muted small">Sin detalle</span>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#reactivarModal" data-id="{{ $alumno->id }}" data-nombre="{{ $alumno->nombre }} {{ $alumno->apellidopaterno }}">Reactivar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div></div>

<div class="modal fade" id="detalleBajaModal" tabindex="-1" aria-labelledby="detalleBajaModalLabel" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="detalleBajaModalLabel">Detalle de la baja</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
        <div class="modal-body"><dl class="mb-0"><dt>Motivo</dt><dd id="detalleBajaMotivo" class="mb-3"></dd><dt>Fecha</dt><dd id="detalleBajaFecha" class="mb-3"></dd><dt>Registró</dt><dd id="detalleBajaResponsable" class="mb-0"></dd></dl></div>
    </div></div>
</div>

<div class="modal fade" id="reactivarModal" tabindex="-1" aria-labelledby="reactivarModalLabel" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" id="reactivarForm">
            @csrf
            <div class="modal-header"><h5 class="modal-title" id="reactivarModalLabel">Reactivar alumno</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
            <div class="modal-body"><p id="reactivarAlumno" class="fw-semibold"></p><label for="motivo_reactivacion" class="form-label">Motivo de la reactivación</label><textarea id="motivo_reactivacion" name="motivo_reactivacion" class="form-control" rows="4" maxlength="2000" required placeholder="Explica por qué se reactiva este expediente."></textarea></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success">Confirmar reactivación</button></div>
        </form>
    </div></div>
</div>

</div>

{{-- Script de filtros dinámicos --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const nivelFilter = document.getElementById("nivelFilter");
    const gradoFilter = document.getElementById("gradoFilter");
    const seccionFilter = document.getElementById("seccionFilter");
    const searchBox = document.getElementById("searchBox");
    const alumnosTable = document.getElementById("alumnosTable").getElementsByTagName("tbody")[0];

    // Cargar grados dinámicamente cuando se seleccione un nivel
    nivelFilter.addEventListener("change", function () {
        const nivelId = this.value;
        gradoFilter.innerHTML = '<option value="">Filtrar por Grado</option>';
        gradoFilter.disabled = true;

        if (nivelId) {
            fetch(`/grados/${nivelId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(grado => {
                        let option = document.createElement("option");
                        option.value = grado.id;
                        option.textContent = grado.nombre;
                        gradoFilter.appendChild(option);
                    });
                    gradoFilter.disabled = false;
                })
                .catch(error => console.error('Error:', error));
        }
    });

    function filterTable() {
        const nivel = nivelFilter.value;
        const grado = gradoFilter.value;
        const seccion = seccionFilter.value.toLowerCase();
        const searchText = searchBox.value.toLowerCase();

        Array.from(alumnosTable.getElementsByTagName("tr")).forEach(row => {
            const rowNivel = row.getAttribute("data-nivel");
            const rowGrado = row.getAttribute("data-grado");
            const rowSeccion = row.getAttribute("data-seccion").toLowerCase();
            const rowText = row.innerText.toLowerCase();

            const nivelMatch = nivel === "" || rowNivel === nivel;
            const gradoMatch = grado === "" || rowGrado === grado;
            const seccionMatch = seccion === "" || rowSeccion.includes(seccion);
            const searchMatch = searchText === "" || rowText.includes(searchText);

            row.style.display = (nivelMatch && gradoMatch && seccionMatch && searchMatch) ? "" : "none";
        });
    }

    nivelFilter.addEventListener("change", filterTable);
    gradoFilter.addEventListener("change", filterTable);
    seccionFilter.addEventListener("change", filterTable);
    searchBox.addEventListener("keyup", filterTable);
});

document.getElementById('detalleBajaModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('detalleBajaMotivo').textContent = button.getAttribute('data-motivo');
    document.getElementById('detalleBajaFecha').textContent = button.getAttribute('data-fecha');
    document.getElementById('detalleBajaResponsable').textContent = button.getAttribute('data-responsable');
});

document.getElementById('reactivarModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('reactivarAlumno').textContent = button.getAttribute('data-nombre');
    document.getElementById('reactivarForm').action = '{{ url('alumnos/archivados') }}/' + button.getAttribute('data-id') + '/reactivar';
    document.getElementById('motivo_reactivacion').value = '';
});
</script>

@endsection

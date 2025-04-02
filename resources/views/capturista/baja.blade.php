@extends('layouts.app')

@section('content')
<div class="container">

<div class="container">
    <!-- Mostrar mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <h2>Dar de Baja a un Alumno</h2>

    {{-- Filtros dinámicos --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <select id="nivelFilter" class="form-control">
                <option value="">Filtrar por Nivel</option>
                @foreach ($niveles as $nivel)
                    <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
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
                <option value="A">A</option>
                <option value="B">B</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" id="searchBox" class="form-control" placeholder="Buscar por nombre o matrícula...">
        </div>
    </div>

    {{-- Tabla de alumnos --}}
    <div class="table-responsive">
    <table class="table table-striped table-bordered" id="alumnosTable" style="background: white;">
        <thead class="table-dark">
            <tr>
                <th>Matrícula</th>
                <th>Nombre</th>
                <th>Nivel Educativo</th>
                <th>Grado</th>
                <th>Sección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($alumnos as $alumno)
                <tr data-nivel="{{ $alumno->nivel_educativo_id }}" 
                    data-grado="{{ $alumno->grado_id }}" 
                    data-seccion="{{ $alumno->seccion }}">
                    <td>{{ $alumno->matricula }}</td>
                    <td>{{ $alumno->nombre }} {{ $alumno->apellidopaterno }}</td>
                    <td>{{ $alumno->nivelEducativo->nombre ?? 'N/A' }}</td>
                    <td>{{ $alumno->grado->nombre ?? 'N/A' }}</td>
                    <td>{{ $alumno->seccion }}</td>
                    <td>
                        <a href="#" class="btn btn-danger btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#motivoBajaModal" 
                            data-id="{{ $alumno->id }}" 
                            data-nombre="{{ $alumno->nombre }}">
                            Dar de baja
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $alumnos->links() }}
</div>

<!-- Modal para capturar motivo de la baja -->
<div class="modal fade" id="motivoBajaModal" tabindex="-1" role="dialog" aria-labelledby="motivoBajaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="motivoBajaModalLabel">Motivo de la Baja</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="motivoBajaForm" method="POST" action="{{ route('alumno.baja') }}">
                @csrf
                <input type="hidden" name="alumno_id" id="alumno_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="motivo_baja">Motivo de la Baja:</label>
                        <textarea name="motivo_baja" id="motivo_baja" class="form-control" rows="3" placeholder="Escribe el motivo de la baja..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" id="confirmarBaja" class="btn btn-danger" disabled>Confirmar Baja</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Script de filtros dinámicos --}}
<!-- Cargar jQuery desde CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Cargar Bootstrap desde CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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

$(document).ready(function () {
    console.log("jQuery cargado correctamente.");

    // Evento cuando el modal se abre
    $('#motivoBajaModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var alumnoId = button.data('id');

        var modal = $(this);
        modal.find('#alumno_id').val(alumnoId);
        modal.find('#motivo_baja').val('').trigger('input'); // Limpia y dispara evento de input
        $('#confirmarBaja').prop('disabled', true); // Asegurar que inicia deshabilitado
    });

    // Habilitar el botón cuando se escribe en el textarea
    $(document).on('input keyup', '#motivo_baja', function () {
        var motivo = $(this).val().trim();
        console.log('Texto ingresado:', motivo); // Verificar si detecta el texto
        $('#confirmarBaja').prop('disabled', motivo === '');
    });

    // Depuración: Verificar si jQuery está capturando los eventos correctamente
    $('#motivo_baja').on('input keyup', function () {
        console.log("Evento capturado: ", $(this).val());
    });

    // Cerrar el modal correctamente en Bootstrap 5
    $(document).on('click', '[data-bs-dismiss="modal"]', function () {
        $('#motivoBajaModal').modal('hide');
    });
});
</script>
@endsection

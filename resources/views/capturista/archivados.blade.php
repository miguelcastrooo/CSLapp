@extends('layouts.app')

@section('content')
<div class="container">

    <!-- Mostrar mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <h2>Alumnos Archivados</h2>

    {{-- Filtros dinámicos --}}
    <div class="row mb-3">
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
    </div>

    {{-- Tabla de alumnos archivados --}}
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
            @foreach ($alumnosArchivados as $alumno)
                <tr data-nivel="{{ $alumno->nivel_educativo_id }}" 
                    data-grado="{{ $alumno->grado_id }}" 
                    data-seccion="{{ $alumno->seccion }}">
                    <td>{{ $alumno->matricula }}</td>
                    <td>{{ $alumno->nombre }} {{ $alumno->apellidopaterno }}</td>
                    <td>{{ $alumno->nivelEducativo->nombre ?? 'N/A' }}</td>
                    <td>{{ $alumno->grado->nombre ?? 'N/A' }}</td>
                    <td>{{ $alumno->seccion }}</td>
                    <td>
                        <a href="{{ route('alumnos.reactivar', $alumno->id) }}" class="btn btn-success"
                        onclick="return confirm('¿Estás seguro de reactivar a este alumno?')">
                            Reactivar
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
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
</script>

@endsection

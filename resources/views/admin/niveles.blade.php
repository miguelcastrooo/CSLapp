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

    {{-- Filtros --}}
<div class="row mb-3">
    <div class="col-md-3">
        <select id="gradoFilter" class="form-control">
            <option value="">Filtrar por Grado</option>
            @foreach ($grados as $grado)
                <option value="{{ $grado->id }}">{{ $grado->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <input type="text" id="searchBox" class="form-control" placeholder="Buscar por Matrícula o Nombre">
    </div>
    <div class="col-md-3">
        <select id="seccionFilter" class="form-control">
            <option value="">Filtrar por Sección</option>
            <option value="a">Sección A</option>
            <option value="b">Sección B</option>
            <option value="c">Sección C</option>
            <!-- Agrega aquí más opciones si es necesario -->
        </select>
    </div>
</div>

{{-- Tabla --}}
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
                <tr class="{{ $loop->first ? 'fila-principal' : '' }}" data-grado="{{ $alumno->grado->id ?? '' }}" data-seccion="{{ strtolower($alumno->seccion ?? '') }}" data-matricula="{{ strtolower($alumno->matricula) }}" data-nombre="{{ strtolower($alumno->nombre . ' ' . $alumno->apellidopaterno . ' ' . $alumno->apellidomaterno) }}">
                    @if ($loop->first)
                        <td rowspan="{{ count($alumno->alumnoPlataforma) }}" class="text-center align-middle">
                            {{ $alumno->matricula }}
                        </td>
                        <td rowspan="{{ count($alumno->alumnoPlataforma) }}" class="align-middle">
                            {{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}
                        </td>
                        <td rowspan="{{ count($alumno->alumnoPlataforma) }}" class="text-center align-middle">
                            {{ $alumno->grado->nombre ?? 'N/A' }}
                        </td>
                        <td rowspan="{{ count($alumno->alumnoPlataforma) }}" class="text-center align-middle">
                            {{ $alumno->seccion ?? 'N/A' }}
                        </td>
                    @endif
                    <td class="text-center">{{ $plataforma->nombre ?? ($plataforma->plataforma->nombre ?? 'N/A') }}</td>
                    <td class="text-center">{{ $plataforma->usuario ?? 'N/A' }}</td>
                    <td class="text-center">{{ $plataforma->contraseña ?? 'N/A' }}</td>
                    @if ($loop->first)
                        <td rowspan="{{ count($alumno->alumnoPlataforma) }}" class="text-center align-middle">
                            <div class="d-flex flex-column justify-content-start align-items-center">
                                <a href="{{ route('admin.alumnos.pdf.individual', ['nivel' => $nivelEducativo->nombre, 'id' => $alumno->id]) }}" class="btn btn-danger btn-sm mb-1">Generar PDF</a>
                                <a href="{{ route('admin.enviarCorreo', ['alumnoId' => $alumno->id]) }}" class="btn btn-info btn-sm mb-1">Enviar Correo</a>
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

    <div class="text-center mt-4">
        <a href="{{ route('admin.select') }}" class="btn btn-primary">Volver</a>
    </div>
@endsection

@section('styles')
    <style>
        /* Estilos personalizados para mejorar la legibilidad y el diseño */
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
            background-color:rgb(0, 0, 0) !important;
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
document.addEventListener('DOMContentLoaded', function () {
    const gradoFilter = document.getElementById('gradoFilter');
    const seccionFilter = document.getElementById('seccionFilter');
    const searchBox = document.getElementById('searchBox');

    function applyFilters() {
        const grado = gradoFilter.value;
        const seccion = seccionFilter.value.toLowerCase();
        const search = searchBox.value.toLowerCase();

        const filasPrincipales = document.querySelectorAll('tr.fila-principal');

        filasPrincipales.forEach(fila => {
            const rowGrado = fila.dataset.grado;
            const rowSeccion = fila.dataset.seccion;
            const rowMatricula = fila.dataset.matricula;
            const rowNombre = fila.dataset.nombre;

            const searchText = `${rowMatricula} ${rowNombre}`;

            const gradoMatch = !grado || rowGrado === grado;
            const seccionMatch = !seccion || rowSeccion === seccion;
            const searchMatch = !search || searchText.includes(search);

            const shouldShow = gradoMatch && seccionMatch && searchMatch;

            // Mostrar u ocultar la fila principal y sus hermanas
            let currentRow = fila;
            const rowspan = parseInt(fila.querySelector('td[rowspan]')?.getAttribute('rowspan')) || 1;

            for (let i = 0; i < rowspan; i++) {
                if (currentRow) {
                    currentRow.style.display = shouldShow ? '' : 'none';
                    currentRow = currentRow.nextElementSibling;
                }
            }
        });
    }

    gradoFilter.addEventListener('change', applyFilters);
    seccionFilter.addEventListener('change', applyFilters);
    searchBox.addEventListener('input', applyFilters);
});
</script>
@endsection


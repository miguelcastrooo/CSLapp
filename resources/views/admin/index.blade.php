@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h1 class="mb-4">Alumnos del Nivel: {{ $nivel->nombre }}</h1>

    <!-- Filtros de Grado y Sección -->
    <div class="filters mb-4">
        <div class="form-group">
            <label for="grado-filter">Filtrar por Grado</label>
            <div id="grado-filter">
                @foreach($grados as $grado)
                    <div class="form-check form-check-inline">
                        <input type="checkbox" class="form-check-input" id="grado-{{ $grado->id }}" value="{{ $grado->id }}" onchange="filterAlumnos()">
                        <label class="form-check-label" for="grado-{{ $grado->id }}">{{ $grado->nombre }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label for="seccion-filter">Filtrar por Sección</label>
            <div id="seccion-filter">
                <div class="form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" id="seccion-A" value="A" onchange="filterAlumnos()">
                    <label class="form-check-label" for="seccion-A">A</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" id="seccion-B" value="B" onchange="filterAlumnos()">
                    <label class="form-check-label" for="seccion-B">B</label>
                </div>
            </div>
        </div>

        <!-- Buscador dinámico -->
        <div class="form-group">
            <label for="search">Buscar por Nombre, Apellido o Matrícula</label>
            <input type="text" id="search" class="form-control" placeholder="Buscar..." onkeyup="searchAlumnos()">
        </div>
    </div>

    <!-- Contenedor de la tabla con scroll horizontal -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="text-center" style="background-color: #212121 !important; color: #e0e0e0 !important;">
                <tr>
                    <th class="p-3">Matrícula</th>
                    <th class="p-3">Nombre del Alumno</th>
                    <th class="p-3">Apellido Paterno</th>
                    <th class="p-3">Apellido Materno</th>
                    <th class="p-3">Grado</th>
                    <th class="p-3">Sección</th>
                    <th class="p-3">Plataforma</th>
                    <th class="p-3">Usuario</th>
                    <th class="p-3">Contraseña</th>

                    @if (in_array($nivel->first()->nombre, ['Primaria Inferior', 'Primaria Superior']))
                        <th class="p-3">Usuario HMH</th>
                        <th class="p-3">Contraseña HMH</th>
                    @endif

                    @if (in_array($nivel->first()->nombre, ['Primaria Superior', 'Secundaria']))
                        <th class="p-3">Usuario Mathletics</th>
                        <th class="p-3">Contraseña Mathletics</th>
                    @endif

                    @if ($nivel->first()->nombre == 'Primaria Superior')
                        <th class="p-3">Usuario Progrentis</th>
                        <th class="p-3">Contraseña Progrentis</th>
                    @endif

                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody style="background-color: #2c2c2c !important; color: white !important;">
                @foreach ($alumnos as $alumno)
                    @foreach ($alumno->plataformas as $index => $plataforma)
                        <tr class="alumno-row" data-grado="{{ $alumno->grado_id }}" data-seccion="{{ $alumno->seccion }}" data-nombre="{{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }} {{ $alumno->matricula }}">
                            @if ($loop->first)
                                <td rowspan="{{ count($alumno->plataformas) }}" class="align-middle">{{ $alumno->matricula }}</td>
                                <td rowspan="{{ count($alumno->plataformas) }}" class="align-middle">{{ $alumno->nombre }}</td>
                                <td rowspan="{{ count($alumno->plataformas) }}" class="align-middle">{{ $alumno->apellidopaterno }}</td>
                                <td rowspan="{{ count($alumno->plataformas) }}" class="align-middle">{{ $alumno->apellidomaterno }}</td>
                                <td rowspan="{{ count($alumno->plataformas) }}" class="align-middle">{{ $alumno->grado->nombre }}</td>
                                <td rowspan="{{ count($alumno->plataformas) }}" class="align-middle">{{ $alumno->seccion }}</td>
                            @endif

                            <!-- Nombre de la plataforma -->
                            <td>{{ $plataforma->nombre ?? 'N/A' }}</td>

                            <!-- Usuario y Contraseña, mostrando N/A solo si están vacíos -->
                            <td>{{ $plataforma->pivot->usuario ?? 'N/A' }}</td>
                            <td>{{ $plataforma->pivot->contraseña ?? 'N/A' }}</td>

                            @if (in_array($nivel->first()->nombre, ['Primaria Inferior', 'Primaria Superior','Secundaria']))
                                <!-- Campos adicionales para Primaria Baja y Alta -->
                                <td>{{ $plataforma->pivot->usuario_hmh ?? 'N/A' }}</td>
                                <td>{{ $plataforma->pivot->contraseña_hmh ?? 'N/A' }}</td>
                            @endif

                            @if (in_array($nivel->first()->nombre, ['Primaria Superior', 'Secundaria']))
                                <!-- Campos adicionales para Primaria Alta y Secundaria -->
                                <td>{{ $plataforma->pivot->usuario_mathletics ?? 'N/A' }}</td>
                                <td>{{ $plataforma->pivot->contraseña_mathletics ?? 'N/A' }}</td>
                            @endif

                            @if ($nivel->first()->nombre == 'Primaria Superior')
                                <!-- Campos específicos para Primaria Alta -->
                                <td>{{ $plataforma->pivot->usuario_progrentis ?? 'N/A' }}</td>
                                <td>{{ $plataforma->pivot->contraseña_progrentis ?? 'N/A' }}</td>
                            @endif

                            @if ($loop->first)
                                <td class="text-center">
                                    <a href="{{ route('alumnos.edit', $alumno->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        {{ $alumnos->links() }}
    </div>

    <a href="{{ route('admin.selectadmin') }}" class="btn btn-primary mt-3">Volver</a>

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


    <script>
        function filterAlumnos() {
            let selectedGrados = [];
            let selectedSecciones = [];

            document.querySelectorAll('#grado-filter input:checked').forEach(function (checkbox) {
                selectedGrados.push(checkbox.value);
            });

            document.querySelectorAll('#seccion-filter input:checked').forEach(function (checkbox) {
                selectedSecciones.push(checkbox.value);
            });

            document.querySelectorAll('.alumno-row').forEach(function (row) {
                let grado = row.getAttribute('data-grado');
                let seccion = row.getAttribute('data-seccion');

                let showRow = true;

                if (selectedGrados.length > 0 && !selectedGrados.includes(grado)) {
                    showRow = false;
                }

                if (selectedSecciones.length > 0 && !selectedSecciones.includes(seccion)) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });
        }

        function searchAlumnos() {
            let searchValue = document.getElementById('search').value.toLowerCase();

            document.querySelectorAll('.alumno-row').forEach(function (row) {
                let rowText = row.getAttribute('data-nombre').toLowerCase() + " " + row.getAttribute('data-grado').toLowerCase();
                if (rowText.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>

@endsection

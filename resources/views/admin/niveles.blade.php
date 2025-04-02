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

    {{-- Filtros dinámicos --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <select id="gradoFilter" class="form-control">
                <option value="">Filtrar por Grado</option>
                @foreach($grados as $grado)
                    <option value="{{ $grado->id }}">{{ $grado->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select id="seccionFilter" class="form-control">
                <option value="">Filtrar por Sección</option>
                <option value="a">A</option>
                <option value="b">B</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" id="searchBox" class="form-control" placeholder="Buscar por nombre, matrícula...">
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
                        <tr data-grado="{{ $alumno->grado->id ?? '' }}" data-seccion="{{ strtolower($alumno->seccion ?? '') }}">
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

    <div class="d-flex justify-content-center mt-4">
        <a href="{{ route('admin.generarPdfTodos', ['nivel' => $nivelEducativo->nombre]) }}" class="btn btn-danger btn-lg mx-2">Generar PDF para Todos</a>
        <a href="{{ route('admin.enviarCorreoATodos', ['nivel' => $nivelEducativo->nombre]) }}" class="btn btn-info btn-lg mx-2">Enviar Correo a Todos</a>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $alumnos->links('pagination::bootstrap-4') }}
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
        document.addEventListener('DOMContentLoaded', function() {
            const gradoFilter = document.getElementById('gradoFilter');
            const seccionFilter = document.getElementById('seccionFilter');
            const searchBox = document.getElementById('searchBox');
            const alumnosTable = document.getElementById('alumnosTableBody');

            function applyFilters() {
                const grado = gradoFilter.value; // Filtra por ID de Grado
                const seccion = seccionFilter.value.toLowerCase(); // Filtra por Sección (A o B)
                const search = searchBox.value.toLowerCase(); // Búsqueda por nombre o matrícula

                Array.from(alumnosTable.getElementsByTagName('tr')).forEach(row => {
                    const rowGrado = row.getAttribute('data-grado'); // Obtener el grado de la fila
                    const rowSeccion = row.getAttribute('data-seccion')?.toLowerCase(); // Obtener la sección de la fila
                    const rowText = row.innerText.toLowerCase(); // Obtener todo el texto de la fila

                    // Comparar si las filas coinciden con los filtros
                    const gradoMatch = grado === '' || rowGrado === grado;
                    const seccionMatch = seccion === '' || rowSeccion.includes(seccion);
                    const searchMatch = search === '' || rowText.includes(search);

                    // Mostrar u ocultar la fila en base a las coincidencias
                    row.style.display = (gradoMatch && seccionMatch && searchMatch) ? '' : 'none';
                });
            }

            // Aplicar filtros cuando cambian los inputs
            gradoFilter.addEventListener('change', applyFilters);
            seccionFilter.addEventListener('change', applyFilters);
            searchBox.addEventListener('input', applyFilters);
        });
    </script>
@endsection

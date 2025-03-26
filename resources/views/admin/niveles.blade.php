@extends('layouts.app')

@section('content')
    @isset($nivelEducativo)

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
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" id="searchBox" class="form-control" placeholder="Buscar por nombre, matrícula...">
            </div>
        </div>

        {{-- Tabla --}}
        <div class="table-responsive mt-4">
            <table class="table table-hover table-sm">
                <thead class="thead-dark">
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
                            <tr data-grado="{{ $alumno->grado->nombre ?? '' }}" data-seccion="{{ $alumno->seccion ?? '' }}">
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
                                @if ($loop->last)
                                    <td class="text-center">
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
            {{ $alumnos->links('pagination::bootstrap-4') }}
        </div>

    @else
        <p class="text-center mt-4">No hay alumnos disponibles para este nivel.</p>
    @endisset

    <div class="text-center mt-4">
        <a href="{{ route('admin.select') }}" class="btn btn-primary">Volver</a>
    </div>
@endsection

@section('style')
    <style>
        thead.thead-dark {
            background-color: #212529;
            color: #ffffff;
        }

        table.table-hover {
            border-collapse: collapse;
        }

        table.table-hover th, table.table-hover td {
            border: 1px solid #dee2e6;
            padding: 12px;
        }

        td.text-center[style="border: none;"] {
            border: none;
        }

        .table td .btn {
            margin: 0;
        }

        table.table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filtros
            const gradoFilter = document.getElementById('gradoFilter');
            const seccionFilter = document.getElementById('seccionFilter');
            const searchBox = document.getElementById('searchBox');
            const alumnosTable = document.getElementById('alumnosTableBody');

            // Función para aplicar filtros
            function applyFilters() {
                const grado = gradoFilter.value;
                const seccion = seccionFilter.value.toLowerCase();
                const search = searchBox.value.toLowerCase();

                Array.from(alumnosTable.getElementsByTagName('tr')).forEach(row => {
                    const rowGrado = row.getAttribute('data-grado')?.toLowerCase();
                    const rowSeccion = row.getAttribute('data-seccion')?.toLowerCase();
                    const rowText = row.innerText.toLowerCase();

                    const gradoMatch = grado === '' || rowGrado.includes(grado);
                    const seccionMatch = seccion === '' || rowSeccion.includes(seccion);
                    const searchMatch = search === '' || rowText.includes(search);

                    row.style.display = (gradoMatch && seccionMatch && searchMatch) ? '' : 'none';
                });
            }

            // Event listeners para los filtros
            gradoFilter.addEventListener('change', applyFilters);
            seccionFilter.addEventListener('change', applyFilters);
            searchBox.addEventListener('input', applyFilters);
        });
    </script>
@endsection

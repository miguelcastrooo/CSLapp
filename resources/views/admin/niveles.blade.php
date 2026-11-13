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

<div class="csl-module-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
    <div><h1>{{ $nivelEducativo->nombre }}</h1><p>Plataformas y accesos de los alumnos de este nivel.</p></div>
    <a href="{{ route('admin.select') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver a niveles</a>
</div>

<x-task-guide title="Encuentra un acceso" description="Usa uno o varios filtros para localizar plataformas y credenciales dentro de este nivel." :steps="['Escribe el nombre o matrícula del alumno.', 'Filtra por grado, sección o plataforma si lo necesitas.', 'Usa las acciones del alumno solo después de confirmar el resultado.']" />

<div class="card csl-data-card">
<div class="card-header p-3"><div class="row g-3 align-items-end">
    <div class="col-md-4 position-relative"><label for="platform-search" class="form-label">Buscar alumno</label><input id="platform-search" type="search" class="form-control" placeholder="Nombre, apellidos o matrícula" autocomplete="off"><div id="platform-search-suggestions" class="csl-search-suggestions d-none"></div></div>
    <div class="col-md-3"><label for="platform-grade-filter" class="form-label">Grado</label><select id="platform-grade-filter" class="form-select"><option value="">Todos los grados</option></select></div>
    <div class="col-md-2"><label for="platform-section-filter" class="form-label">Sección</label><select id="platform-section-filter" class="form-select"><option value="">Todas</option></select></div>
    <div class="col-md-3"><label for="platform-name-filter" class="form-label">Plataforma</label><select id="platform-name-filter" class="form-select"><option value="">Todas las plataformas</option></select></div>
</div></div>
<div class="table-responsive">
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
                            <a href="{{ route('admin.alumnos.pdf.individual', ['nivel' => $nivelEducativo->nombre, 'id' => $alumno->id]) }}" class="btn btn-outline-danger btn-sm mb-1"><i class="fas fa-file-pdf me-1"></i>PDF</a>
                            <div class="btn-group mb-1">
                                <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Enviar Correo</button>
                                <ul class="dropdown-menu">
                                    @foreach(['padre' => 'Solo al Padre', 'madre' => 'Solo a la Madre', 'ambos' => 'A Ambos'] as $destinatario => $etiqueta)
                                        <li>
                                            <form method="POST" action="{{ route('admin.enviarCorreo', ['alumnoId' => $alumno->id]) }}" onsubmit="return confirm('¿Deseas enviar las credenciales por correo?');">
                                                @csrf
                                                <input type="hidden" name="destinatario" value="{{ $destinatario }}">
                                                <button type="submit" class="dropdown-item">{{ $etiqueta }}</button>
                                            </form>
                                        </li>
                                    @endforeach
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
</div>
@else
<div class="alert alert-info">No hay alumnos disponibles para este nivel.</div>
@endisset
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = Array.from(document.querySelectorAll('#alumnosTableBody tr'));
    const search = document.getElementById('platform-search');
    const suggestions = document.getElementById('platform-search-suggestions');
    const filters = { grado: document.getElementById('platform-grade-filter'), seccion: document.getElementById('platform-section-filter'), plataforma: document.getElementById('platform-name-filter') };
    const addOptions = (select, attribute) => [...new Set(rows.map((row) => row.dataset[attribute]).filter(Boolean))].sort().forEach((value) => select.add(new Option(value.replace(/\b\w/g, (letter) => letter.toUpperCase()), value)));
    addOptions(filters.grado, 'grado'); addOptions(filters.seccion, 'seccion'); addOptions(filters.plataforma, 'plataforma');
    const applyFilters = () => rows.forEach((row) => {
        const text = `${row.dataset.nombre} ${row.dataset.matricula}`;
        const visible = (!search.value || text.includes(search.value.toLowerCase())) && (!filters.grado.value || row.dataset.grado === filters.grado.value) && (!filters.seccion.value || row.dataset.seccion === filters.seccion.value) && (!filters.plataforma.value || row.dataset.plataforma === filters.plataforma.value);
        row.style.display = visible ? '' : 'none';
    });
    const showSuggestions = () => {
        const query = search.value.trim().toLowerCase();
        if (query.length < 2) { suggestions.classList.add('d-none'); suggestions.innerHTML = ''; return; }
        const alumnos = [...new Map(rows.filter((row) => `${row.dataset.nombre} ${row.dataset.matricula}`.includes(query)).map((row) => [`${row.dataset.nombre}|${row.dataset.matricula}`, { nombre: row.dataset.nombre, matricula: row.dataset.matricula }])).values()].slice(0, 6);
        suggestions.innerHTML = alumnos.length ? alumnos.map((alumno) => `<button type="button" class="csl-search-suggestion w-100 border-0 text-start" data-search="${alumno.nombre}"><span class="csl-search-suggestion-icon"><i class="fas fa-user"></i></span><span><strong>${alumno.nombre}</strong><small>${alumno.matricula}</small></span></button>`).join('') : '<div class="csl-search-empty">No hay coincidencias en este nivel.</div>';
        suggestions.classList.remove('d-none');
    };
    search.addEventListener('input', () => { applyFilters(); showSuggestions(); });
    suggestions.addEventListener('click', (event) => { const button = event.target.closest('[data-search]'); if (!button) return; search.value = button.dataset.search; suggestions.classList.add('d-none'); applyFilters(); });
    Object.values(filters).forEach((filter) => filter.addEventListener('change', applyFilters));
});
</script>
@endsection

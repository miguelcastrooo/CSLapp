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

<div class="card csl-data-card mb-4">
    <div class="card-body d-flex flex-wrap align-items-center gap-2">
        <strong class="me-2"><i class="fas fa-laptop me-1"></i>Plataformas de {{ $nivelEducativo->nombre }}:</strong>
        @forelse($plataformasNivel as $plataformaNivel)
            <span class="badge bg-primary-lt text-primary px-3 py-2">{{ $plataformaNivel->nombre }}</span>
        @empty
            <span class="text-secondary">Aún no hay plataformas activas asignadas a este nivel.</span>
        @endforelse
    </div>
</div>

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
            @forelse ($alumnos as $alumno)
                @php
                    $accesosDelNivel = $plataformasNivel->map(fn ($plataformaNivel) => [
                        'plataforma' => $plataformaNivel,
                        'acceso' => $alumno->alumnoPlataforma->firstWhere('plataforma_id', $plataformaNivel->id),
                    ]);
                @endphp
                @forelse ($accesosDelNivel as $index => $accesoNivel)
                    @php
                        $plataforma = $accesoNivel['plataforma'];
                        $acceso = $accesoNivel['acceso'];
                    @endphp
                <tr class="{{ $loop->first ? 'fila-principal' : '' }}"
                    data-alumno="{{ $alumno->id }}"
                    data-grado="{{ strtolower($alumno->grado->nombre ?? '') }}"
                    data-seccion="{{ strtolower($alumno->seccion ?? '') }}"
                    data-plataforma="{{ strtolower($plataforma->nombre) }}"
                    data-nombre="{{ strtolower($alumno->nombre . ' ' . $alumno->apellidopaterno . ' ' . $alumno->apellidomaterno) }}"
                    data-matricula="{{ strtolower($alumno->matricula) }}"
                >
                    @if ($loop->first)
                    <td rowspan="{{ $accesosDelNivel->count() }}" class="text-center align-middle">{{ $alumno->matricula }}</td>
                    <td rowspan="{{ $accesosDelNivel->count() }}" class="align-middle">{{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</td>
                    <td rowspan="{{ $accesosDelNivel->count() }}" class="text-center align-middle">{{ $alumno->grado->nombre ?? 'N/A' }}</td>
                    <td rowspan="{{ $accesosDelNivel->count() }}" class="text-center align-middle">{{ $alumno->seccion ?? 'N/A' }}</td>
                    @endif

                    <td class="text-center">{{ $plataforma->nombre }}</td>
                    <td class="text-center">{{ $acceso?->usuario ?: 'Pendiente por registrar' }}</td>
                    <td class="text-center">{{ $acceso?->contraseña ?: 'Pendiente por registrar' }}</td>

                    @if ($loop->first)
                    <td rowspan="{{ $accesosDelNivel->count() }}" class="text-center align-middle">
                        <div class="d-flex flex-column align-items-center">
                            <a href="{{ route('admin.alumnos.pdf.individual', ['nivel' => $nivelEducativo->nombre, 'id' => $alumno->id]) }}" class="btn btn-outline-danger btn-sm mb-1"><i class="fas fa-file-pdf me-1"></i>PDF</a>
                            <details class="csl-email-recipient-menu mb-1">
                                <summary class="btn btn-info btn-sm">Enviar correo <i class="fas fa-chevron-down ms-1"></i></summary>
                                <div class="csl-email-recipient-list">
                                    @foreach(['padre' => 'Solo al Padre', 'madre' => 'Solo a la Madre', 'tutor' => 'Solo al Tutor', 'ambos' => 'Padre y madre'] as $destinatario => $etiqueta)
                                        <div>
                                            <form method="POST" action="{{ route('admin.enviarCorreo', ['alumnoId' => $alumno->id]) }}" data-confirm="¿Deseas enviar las credenciales por correo?" data-confirm-title="Enviar credenciales" data-confirm-button="Sí, enviar" data-confirm-variant="primary">
                                                @csrf
                                                <input type="hidden" name="destinatario" value="{{ $destinatario }}">
                                                <button type="submit" class="csl-email-recipient-option">{{ $etiqueta }}</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-secondary py-3">Este nivel aún no tiene plataformas activas configuradas.</td></tr>
                @endforelse
            @empty
                <tr><td colspan="8" class="text-center text-secondary py-3">No hay alumnos registrados en este nivel.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div id="platform-no-results" class="text-center text-secondary py-4 d-none"><i class="fas fa-filter-circle-xmark me-2"></i>No hay resultados con los filtros seleccionados.</div>
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
    const noResults = document.getElementById('platform-no-results');
    const filters = { grado: document.getElementById('platform-grade-filter'), seccion: document.getElementById('platform-section-filter'), plataforma: document.getElementById('platform-name-filter') };
    const addOptions = (select, attribute) => [...new Set(rows.map((row) => row.dataset[attribute]).filter(Boolean))].sort().forEach((value) => select.add(new Option(value.replace(/\b\w/g, (letter) => letter.toUpperCase()), value)));
    addOptions(filters.grado, 'grado'); addOptions(filters.seccion, 'seccion'); addOptions(filters.plataforma, 'plataforma');
    const applyFilters = () => {
        const grupos = new Map();
        rows.forEach((row) => {
            const grupo = grupos.get(row.dataset.alumno) ?? [];
            grupo.push(row);
            grupos.set(row.dataset.alumno, grupo);
        });

        let hayResultados = false;
        grupos.forEach((filasAlumno) => {
            const referencia = filasAlumno[0];
            const texto = `${referencia.dataset.nombre} ${referencia.dataset.matricula}`;
            const coincideAlumno = (!search.value || texto.includes(search.value.toLowerCase()))
                && (!filters.grado.value || referencia.dataset.grado === filters.grado.value)
                && (!filters.seccion.value || referencia.dataset.seccion === filters.seccion.value);
            const coincidePlataforma = !filters.plataforma.value
                || filasAlumno.some((fila) => fila.dataset.plataforma === filters.plataforma.value);
            const visible = coincideAlumno && coincidePlataforma;
            hayResultados ||= visible;
            filasAlumno.forEach((fila) => { fila.style.display = visible ? '' : 'none'; });
        });
        noResults?.classList.toggle('d-none', hayResultados || grupos.size === 0);
    };
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

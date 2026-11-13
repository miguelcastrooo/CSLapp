@extends('layouts.app')

@section('title', 'Panel principal | Instituto Horizonte')

@section('content')
    <div class="csl-page-heading d-flex flex-column flex-md-row justify-content-between gap-3">
        <div><h1>Buenos días, {{ auth()->user()->name }}</h1><p>Consulta el estado general del Instituto Horizonte.</p></div>
        @if($accesoCompleto)
            <div><a href="{{ route('alumnos.select') }}" class="btn btn-success"><i class="fas fa-user-plus me-2"></i>Registrar alumno</a></div>
        @endif
    </div>

    <div id="dashboardCarousel" class="carousel slide carousel-fade csl-dashboard-carousel" data-bs-interval="false">
        <div class="carousel-indicators"><button type="button" data-bs-target="#dashboardCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Mensaje 1"></button><button type="button" data-bs-target="#dashboardCarousel" data-bs-slide-to="1" aria-label="Mensaje 2"></button><button type="button" data-bs-target="#dashboardCarousel" data-bs-slide-to="2" aria-label="Mensaje 3"></button></div>
        <div class="carousel-inner">
            <div class="carousel-item active"><div class="csl-dashboard-slide" style="background-image: url('{{ asset('img/dashboard-educativo.png') }}');"><div class="csl-dashboard-slide-overlay"><h2>Todo lo importante, a la vista</h2><p>Consulta alumnos, niveles y plataformas desde un solo lugar.</p></div></div></div>
            <div class="carousel-item"><div class="csl-dashboard-slide" style="background-image: url('{{ asset('img/login-educativo.png') }}');"><div class="csl-dashboard-slide-overlay"><h2>Expedientes claros y ordenados</h2><p>Usa la búsqueda para localizar rápidamente a cada alumno.</p></div></div></div>
            <div class="carousel-item"><div class="csl-dashboard-slide" style="background-image: url('{{ asset('img/dashboard-educativo.png') }}');"><div class="csl-dashboard-slide-overlay"><h2>Trabajo en equipo con control</h2><p>Cada coordinación consulta únicamente la información de su nivel.</p></div></div></div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Anterior</span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Siguiente</span></button>
    </div>

    <form action="{{ route('capturista.selectsearch') }}" method="GET" class="csl-dashboard-search mb-4" role="search" autocomplete="off">
        <label for="dashboard-search" class="form-label mb-1">Buscar alumno</label>
        <div class="input-group"><span class="input-group-text"><i class="fas fa-magnifying-glass"></i></span><input id="dashboard-search" name="search" type="search" class="form-control" placeholder="Escribe al menos 2 letras del nombre o matrícula" aria-label="Buscar alumno por matrícula, nombre o apellidos" aria-controls="dashboard-search-results" aria-expanded="false"><button class="btn btn-primary" type="submit">Ver resultados</button></div>
        <div id="dashboard-search-results" class="csl-search-suggestions d-none" role="listbox" aria-label="Sugerencias de alumnos"></div>
    </form>

    <div class="row row-deck row-cards mb-4">
        <div class="{{ $accesoCompleto ? 'col-sm-6 col-lg-3' : 'col-sm-6 col-lg-4' }}"><div class="card csl-stat-card"><div class="card-body"><div class="d-flex align-items-center"><span class="csl-stat-icon green"><i class="fas fa-users"></i></span><div class="ms-3"><div class="text-secondary small">{{ $accesoCompleto ? 'Alumnos activos' : 'Mis alumnos activos' }}</div><div class="h2 mb-0">{{ $totalAlumnos }}</div></div></div></div></div></div>
        @if($accesoCompleto)
            <div class="col-sm-6 col-lg-3"><div class="card csl-stat-card"><div class="card-body"><div class="d-flex align-items-center"><span class="csl-stat-icon gold"><i class="fas fa-layer-group"></i></span><div class="ms-3"><div class="text-secondary small">Niveles educativos</div><div class="h2 mb-0">{{ $niveles->count() }}</div></div></div></div></div></div>
            <div class="col-sm-6 col-lg-3"><div class="card csl-stat-card"><div class="card-body"><div class="d-flex align-items-center"><span class="csl-stat-icon blue"><i class="fas fa-laptop"></i></span><div class="ms-3"><div class="text-secondary small">Plataformas</div><div class="h2 mb-0">{{ $totalPlataformas }}</div></div></div></div></div></div>
            <div class="col-sm-6 col-lg-3"><div class="card csl-stat-card"><div class="card-body"><div class="d-flex align-items-center"><span class="csl-stat-icon red"><i class="fas fa-box-archive"></i></span><div class="ms-3"><div class="text-secondary small">Archivados</div><div class="h2 mb-0">{{ $totalArchivados }}</div></div></div></div></div></div>
        @endif
    </div>

    <div class="card">
        <div class="card-header"><div><h2 class="card-title mb-1">{{ $accesoCompleto ? 'Niveles educativos' : 'Mi nivel educativo' }}</h2><div class="text-secondary">Elige la tarea que deseas realizar.</div></div></div>
        <div class="card-body"><div class="row row-cards">
            @forelse($niveles as $nivel)
                <div class="col-sm-6 col-xl-3">
                    <div class="card csl-level-card h-100">
                        <div class="card-body"><div class="d-flex justify-content-between align-items-start"><span class="csl-level-dot"></span><span class="badge bg-green-lt">{{ $nivel->alumnos_count }} alumnos</span></div><div class="h3 mt-4 mb-1">{{ $nivel->nombre }}</div><div class="text-secondary small mb-3">Accesos directos de este nivel.</div><div class="d-grid gap-2"><a class="btn btn-outline-primary btn-sm" href="{{ route('capturista.index', ['nivel' => $nivel->id]) }}"><i class="fas fa-users me-1"></i>Ver alumnos</a><a class="btn btn-outline-secondary btn-sm" href="{{ route('niveles.show', ['nivelId' => $nivel->id]) }}"><i class="fas fa-laptop me-1"></i>Ver plataformas</a></div></div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-secondary">Aún no hay niveles educativos registrados.</div>
            @endforelse
        </div></div>
    </div>
@endsection

@section('scripts')
<script>
    (() => {
        const input = document.getElementById('dashboard-search');
        const results = document.getElementById('dashboard-search-results');
        let timer;
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' })[character]);
        const closeResults = () => { results.classList.add('d-none'); results.innerHTML = ''; input.setAttribute('aria-expanded', 'false'); };

        input.addEventListener('input', () => {
            window.clearTimeout(timer);
            const query = input.value.trim();
            if (query.length < 2) return closeResults();
            timer = window.setTimeout(() => {
                fetch(`{{ route('alumnos.search') }}?search=${encodeURIComponent(query)}&per_page=6`, { headers: { Accept: 'application/json' } })
                    .then((response) => response.json())
                    .then((data) => {
                        const alumnos = data.data ?? [];
                        if (!alumnos.length) {
                            results.innerHTML = '<div class="csl-search-empty">No se encontraron alumnos. Puedes usar “Ver resultados” para una búsqueda más amplia.</div>';
                        } else {
                            results.innerHTML = alumnos.slice(0, 6).map((alumno) => `<a class="csl-search-suggestion" role="option" href="{{ url('alumnos') }}/${alumno.id}/edit"><span class="csl-search-suggestion-icon"><i class="fas fa-user"></i></span><span><strong>${escapeHtml(alumno.nombre)} ${escapeHtml(alumno.apellidopaterno)} ${escapeHtml(alumno.apellidomaterno)}</strong><small>${escapeHtml(alumno.matricula)} · ${escapeHtml(alumno.grado?.nombre ?? 'Sin grado')} ${escapeHtml(alumno.seccion ?? '')}</small></span><i class="fas fa-arrow-right ms-auto"></i></a>`).join('');
                        }
                        results.classList.remove('d-none');
                        input.setAttribute('aria-expanded', 'true');
                    })
                    .catch(closeResults);
            }, 250);
        });
        document.addEventListener('click', (event) => { if (!event.target.closest('.csl-dashboard-search')) closeResults(); });
        input.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeResults(); });
    })();
</script>
@endsection

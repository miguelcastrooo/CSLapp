@extends('layouts.app')

@section('title', 'Panel principal | ' . $configuracionInstitucional->nombre)

@section('content')
    <div class="csl-page-heading d-flex flex-column flex-md-row justify-content-between gap-3">
        <div><h1>Buenos días, {{ auth()->user()->name }}</h1><p>Consulta el estado general de {{ $configuracionInstitucional->nombre }}.</p></div>
        @if($accesoCompleto)
            <div><a href="{{ route('alumnos.select') }}" class="btn btn-success"><i class="fas fa-user-plus me-2"></i>Registrar alumno</a></div>
        @endif
    </div>

    <div id="dashboardCarousel" class="carousel slide carousel-fade csl-dashboard-carousel" data-bs-interval="false">
        @php($slides = $imagenesCarrusel->isNotEmpty() ? $imagenesCarrusel : collect([(object)['url' => fn () => asset('img/dashboard-educativo.png'), 'titulo' => 'Todo lo importante, a la vista', 'descripcion' => 'Consulta alumnos, niveles y plataformas desde un solo lugar.']]))
        @if($slides->count() > 1)<div class="carousel-indicators">@foreach($slides as $slide)<button type="button" data-bs-target="#dashboardCarousel" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}" aria-label="Imagen {{ $loop->iteration }}"></button>@endforeach</div>@endif
        <div class="carousel-inner">
            @foreach($slides as $slide)<div class="carousel-item {{ $loop->first ? 'active' : '' }}"><div class="csl-dashboard-slide" style="background-image: url('{{ method_exists($slide, 'url') ? $slide->url() : ($slide->url)() }}');"><div class="csl-dashboard-slide-overlay"><h2>{{ $slide->titulo }}</h2><p>{{ $slide->descripcion }}</p></div></div></div>@endforeach
        </div>
        @if($slides->count() > 1)<button class="carousel-control-prev" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Anterior</span></button><button class="carousel-control-next" type="button" data-bs-target="#dashboardCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Siguiente</span></button>@endif
    </div>

    <form action="{{ route('capturista.selectsearch') }}" method="GET" class="csl-dashboard-search mb-4" role="search" autocomplete="off">
        <label for="dashboard-search" class="form-label mb-1">Buscar alumno</label>
        <div class="input-group"><span class="input-group-text"><i class="fas fa-magnifying-glass"></i></span><input id="dashboard-search" name="search" type="search" class="form-control" placeholder="Escribe al menos 2 letras del nombre o matrícula" aria-label="Buscar alumno por matrícula, nombre o apellidos" aria-controls="dashboard-search-results" aria-expanded="false"><button class="btn btn-primary" type="submit">Ver resultados</button></div>
        <div id="dashboard-search-results" class="csl-search-suggestions d-none" role="listbox" aria-label="Sugerencias de alumnos"></div>
    </form>

    <div class="csl-quick-help mb-4"><span class="csl-quick-help-icon"><i class="fas fa-circle-question"></i></span><div><strong>Guía rápida de tu acceso</strong><ul>@foreach($accesoCompleto ? ['Gestiona alumnos, altas, bajas y cierre escolar.', 'Usa Configuración académica para ampliar niveles, grados y plataformas.', 'Revisa Avisos para dar seguimiento al equipo.'] : ['Consulta únicamente los alumnos y plataformas de tus niveles.', 'Abre un expediente para completar o corregir información autorizada.', 'Envía una consulta desde Avisos si necesitas apoyo de Administración.'] as $paso)<li>{{ $paso }}</li>@endforeach</ul></div></div>

    <div class="row row-deck row-cards mb-4">
        <div class="{{ $accesoCompleto ? 'col-sm-6 col-lg-3' : 'col-sm-6 col-lg-4' }}"><div class="card csl-stat-card"><div class="card-body"><div class="d-flex align-items-center"><span class="csl-stat-icon green"><i class="fas fa-users"></i></span><div class="ms-3"><div class="text-secondary small">{{ $accesoCompleto ? 'Alumnos activos' : 'Mis alumnos activos' }}</div><div class="h2 mb-0">{{ $totalAlumnos }}</div></div></div></div></div></div>
        @if($accesoCompleto)
            <div class="col-sm-6 col-lg-3"><div class="card csl-stat-card"><div class="card-body"><div class="d-flex align-items-center"><span class="csl-stat-icon gold"><i class="fas fa-layer-group"></i></span><div class="ms-3"><div class="text-secondary small">Niveles educativos</div><div class="h2 mb-0">{{ $niveles->count() }}</div></div></div></div></div></div>
            <div class="col-sm-6 col-lg-3"><div class="card csl-stat-card"><div class="card-body"><div class="d-flex align-items-center"><span class="csl-stat-icon blue"><i class="fas fa-laptop"></i></span><div class="ms-3"><div class="text-secondary small">Plataformas activas</div><div class="h2 mb-0">{{ $totalPlataformas }}</div></div></div></div></div></div>
            <div class="col-sm-6 col-lg-3"><div class="card csl-stat-card"><div class="card-body"><div class="d-flex align-items-center"><span class="csl-stat-icon red"><i class="fas fa-box-archive"></i></span><div class="ms-3"><div class="text-secondary small">Archivados</div><div class="h2 mb-0">{{ $totalArchivados }}</div></div></div></div></div></div>
        @endif
    </div>

    <div class="row row-cards mb-4">
        <div class="col-md-6"><a class="card csl-stat-card text-reset text-decoration-none h-100" href="{{ route('capturista.selectsearch') }}"><div class="card-body d-flex align-items-center"><span class="csl-stat-icon {{ $expedientesPorRevisar ? 'gold' : 'green' }}"><i class="fas {{ $esCoordinacion ? 'fa-key' : 'fa-folder-open' }}"></i></span><div class="ms-3"><div class="text-secondary small">{{ $esCoordinacion ? 'Accesos de plataformas por revisar' : 'Expedientes por revisar' }}</div><div class="h2 mb-0">{{ $expedientesPorRevisar }}</div><small class="text-secondary">{{ $esCoordinacion ? 'Usuarios o contraseñas pendientes' : 'Datos importantes u opcionales por completar' }}</small></div><i class="fas fa-arrow-right ms-auto"></i></div></a></div>
        <div class="col-md-6"><a class="card csl-stat-card text-reset text-decoration-none h-100" href="{{ route('avisos.index') }}"><div class="card-body d-flex align-items-center"><span class="csl-stat-icon {{ $avisosSinLeer ? 'blue' : 'green' }}"><i class="fas fa-bell"></i></span><div class="ms-3"><div class="text-secondary small">Avisos sin leer</div><div class="h2 mb-0">{{ $avisosSinLeer }}</div><small class="text-secondary">Consulta y responde al equipo</small></div><i class="fas fa-arrow-right ms-auto"></i></div></a></div>
        @if($accesoCompleto)<div class="col-12"><a class="card csl-data-card text-reset text-decoration-none" href="{{ route('admin.cierre-escolar') }}"><div class="card-body d-flex align-items-center gap-3"><span class="csl-management-icon"><i class="fas fa-graduation-cap"></i></span><div><strong>Preparar cierre escolar</strong><div class="text-secondary small">Revisa grupos, promociones y egresos antes de realizar movimientos.</div></div><i class="fas fa-arrow-right ms-auto"></i></div></a></div>@endif
    </div>

    <div class="card">
        <div class="card-header"><div><h2 class="card-title mb-1">{{ $accesoCompleto ? 'Niveles educativos' : 'Mi nivel educativo' }}</h2><div class="text-secondary">Elige la tarea que deseas realizar.</div></div></div>
        <div class="card-body"><div class="row row-cards">
            @forelse($niveles as $nivel)
                <div class="{{ $accesoCompleto ? 'col-sm-6 col-xl-3' : 'col-12 col-lg-6' }}">
                    <div class="card csl-level-card h-100">
                        <div class="card-body"><div class="d-flex justify-content-between align-items-start gap-2"><span class="csl-level-dot mt-1"></span><span class="badge bg-green-lt text-nowrap">{{ $nivel->alumnos_count }} alumnos</span></div><div class="h3 mt-4 mb-1">{{ $nivel->nombre }}</div><div class="text-secondary small mb-3">Accesos directos de este nivel.</div><div class="d-grid gap-2 csl-level-actions"><a class="btn btn-outline-primary btn-sm" href="{{ route('capturista.index', ['nivel' => $nivel->id]) }}"><i class="fas fa-users me-1"></i>Ver alumnos</a><a class="btn btn-outline-secondary btn-sm" href="{{ route('niveles.show', ['nivelId' => $nivel->id]) }}"><i class="fas fa-laptop me-1"></i>Ver plataformas</a></div></div>
                    </div>
                </div>
            @empty
                <div class="col-12"><x-empty-state icon="fa-layer-group" title="Aún no hay niveles disponibles" description="Cuando se agreguen niveles educativos, aparecerán aquí para entrar a sus alumnos y plataformas." /></div>
            @endforelse
        </div></div>
    </div>
@endsection

@section('scripts')
<script>
    (() => {
        const carousel = document.getElementById('dashboardCarousel');
        if (carousel) {
            const mantenerAltura = () => {
                carousel.style.height = `${window.innerWidth < 768 ? 185 : 220}px`;
            };
            mantenerAltura();
            carousel.addEventListener('slide.bs.carousel', mantenerAltura);
            carousel.addEventListener('slid.bs.carousel', mantenerAltura);
            window.addEventListener('resize', mantenerAltura);
        }

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

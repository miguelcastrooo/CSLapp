<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php($pageTitle = str_replace('Instituto Horizonte', $configuracionInstitucional->nombre, trim($__env->yieldContent('title'))))
    <title>{{ $pageTitle ?: $configuracionInstitucional->subtitulo . ' | ' . $configuracionInstitucional->nombre }}</title>
    <link rel="icon" href="{{ $configuracionInstitucional->logoUrl() }}">
    <link href="{{ asset('fontawesome/css/all.min.css') }}" rel="stylesheet">
    <script>
        window.schoolNotifications = {
            userId: {{ auth()->id() }},
            indexUrl: @json(route('avisos.index')),
            // Funciona desde localhost, la IP local o una instalación en subcarpeta.
            broadcastAuthPath: @json(rtrim(request()->getBaseUrl(), '/').'/broadcasting/auth'),
        };
        // If the browser restores this protected page from its back/forward cache,
        // request it again so Laravel can verify that the session still exists.
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) window.location.reload();
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --csl-primary: {{ $configuracionInstitucional->color_primario }};
            --csl-sidebar: {{ $configuracionInstitucional->color_sidebar }};
            --csl-accent: {{ $configuracionInstitucional->color_acento }};
            --csl-surface: {{ $configuracionInstitucional->color_fondo }};
            --csl-text: {{ $configuracionInstitucional->color_texto }};
            --csl-text-muted: {{ $configuracionInstitucional->color_texto_secundario }};
            --csl-carousel: {{ $configuracionInstitucional->color_carrusel }};
            --csl-font: {{ $configuracionInstitucional->familiaTipografica() }};
            --csl-base-size: {{ $configuracionInstitucional->tamano_texto ?? 16 }}px;
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>
<body>
    <div class="page csl-shell">
        <aside class="navbar navbar-vertical navbar-expand-lg csl-sidebar" data-bs-theme="light">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-sidebar-toggle aria-controls="csl-sidebar-navigation" aria-expanded="false" aria-label="Abrir navegación">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                </button>
                <a class="navbar-brand navbar-brand-autodark d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                    <img src="{{ $configuracionInstitucional->logoUrl() }}" alt="Logotipo de {{ $configuracionInstitucional->nombre }}" class="csl-brand-mark">
                    <span><span class="csl-brand-name d-block">{{ $configuracionInstitucional->nombre }}</span><span class="csl-brand-subtitle d-block">{{ $configuracionInstitucional->subtitulo }}</span></span>
                </a>
                <div class="collapse navbar-collapse" id="csl-sidebar-navigation"><x-sidebar /></div>
            </div>
        </aside>

        <div class="page-wrapper csl-page">
            <header class="navbar navbar-expand-md navbar-light d-print-none csl-topbar">
                <div class="container-xl">
                    <div class="navbar-nav flex-row order-md-last align-items-center">
                        <div class="nav-item me-3">
                            <button type="button" id="readable-mode-toggle" class="nav-link px-1 border-0 bg-transparent" aria-pressed="false" aria-label="Activar modo de lectura fácil" title="Modo de lectura fácil"><i class="fas fa-universal-access fs-3"></i></button>
                        </div>
                        <details class="nav-item dropdown me-3 csl-notification-menu">
                            <summary class="nav-link px-1 position-relative" aria-label="Abrir avisos" title="Avisos">
                                <i class="fas fa-bell fs-3"></i>
                                <span id="notification-count" class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle {{ ($totalAvisosPendientes ?? 0) > 0 ? '' : 'd-none' }}" data-count="{{ $totalAvisosPendientes ?? 0 }}">{{ min($totalAvisosPendientes ?? 0, 99) }}</span>
                            </summary>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow csl-notification-dropdown">
                                <div class="dropdown-header"><a href="{{ route('avisos.index') }}" class="csl-notification-all">Ver todos</a></div>
                                <div id="notification-list">
                                    @forelse($avisosPendientes ?? [] as $aviso)
                                        <a href="{{ route('avisos.index') }}" class="dropdown-item text-wrap py-2 csl-notification-item"><span class="csl-notification-icon"><i class="fas {{ ($aviso->data['tipo'] ?? '') === 'aviso' ? 'fa-bullhorn' : 'fa-bell' }}"></i></span><span><strong class="d-block">{{ $aviso->data['titulo'] ?? 'Aviso del sistema' }}</strong><span class="small text-secondary d-block mt-1">{{ \Illuminate\Support\Str::limit($aviso->data['mensaje'] ?? '', 76) }}</span></span></a>
                                    @empty
                                        <div id="notification-empty" class="dropdown-item-text text-secondary small py-3">No tienes avisos pendientes.</div>
                                    @endforelse
                                </div>
                            </div>
                        </details>
                        <div class="nav-item me-3 d-none d-md-flex">
                            <a href="{{ route('avisos.index') }}" class="nav-link px-1" aria-label="Ver historial de avisos" title="Ver historial de avisos"><i class="fas fa-list fs-3"></i></a>
                        </div>
                        <details class="nav-item dropdown csl-user-menu">
                            <summary class="nav-link d-flex lh-1 text-reset p-0" aria-label="Abrir menú de usuario" title="Abrir menú de usuario">
                                @if(auth()->user()->profile_picture)
                                    <span class="avatar avatar-sm" style="background-image: url('{{ asset(auth()->user()->profile_picture) }}')"></span>
                                @else
                                    <span class="avatar avatar-sm csl-avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                                @endif
                                <div class="d-none d-xl-block ps-2"><div>{{ auth()->user()->name }}</div><div class="mt-1 small text-secondary">{{ \App\Support\RoleLabel::for(auth()->user()->getRoleNames()->first()) }}</div></div>
                            </summary>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="fas fa-user-circle me-2"></i> Mi perfil</a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('logout') }}" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-right-from-bracket me-2"></i> Cerrar sesión</a>
                            </div>
                        </details>
                    </div>
                    <div class="navbar-nav"><div class="nav-item d-flex align-items-center text-secondary small"><i class="ti ti-layout-dashboard me-2"></i> {{ $configuracionInstitucional->subtitulo }}</div></div>
                </div>
            </header>

            <main class="csl-main">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert"><div class="d-flex"><i class="fas fa-circle-check me-2 mt-1"></i><div>{{ session('success') }}@if(session('student_created_id'))<div class="mt-2"><a class="btn btn-sm btn-success" href="{{ route('alumnos.edit', session('student_created_id')) }}"><i class="fas fa-folder-open me-1"></i>Abrir expediente</a></div>@endif</div></div><a class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></a></div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert"><div class="d-flex"><i class="fas fa-circle-exclamation me-2 mt-1"></i><div>{{ session('error') }}</div></div><a class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></a></div>
                @endif
                <x-breadcrumbs />
                @yield('content')
            </main>
        </div>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    <div class="modal modal-blur fade" id="school-confirm-modal" tabindex="-1" aria-labelledby="school-confirm-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title" id="school-confirm-title">Confirmar acción</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancelar"></button></div><div class="modal-body"><p id="school-confirm-message" class="mb-0" style="white-space: pre-line;"></p></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-danger" id="school-confirm-button">Confirmar</button></div></div></div>
    </div>
    @yield('scripts')
    @stack('scripts')
</body>
</html>

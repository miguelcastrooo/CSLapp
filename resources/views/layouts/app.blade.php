<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Académico | Instituto Horizonte')</title>
    <link rel="icon" href="{{ asset('img/demo-school.svg') }}" type="image/svg+xml">
    <link href="{{ asset('fontawesome/css/all.min.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
    @stack('styles')
</head>
<body>
    <div class="page csl-shell">
        <aside class="navbar navbar-vertical navbar-expand-lg csl-sidebar" data-bs-theme="light">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#csl-sidebar-navigation" aria-controls="csl-sidebar-navigation" aria-label="Abrir navegación">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand navbar-brand-autodark d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                    <img src="{{ asset('img/demo-school.svg') }}" alt="Instituto Horizonte" class="csl-brand-mark">
                    <span><span class="csl-brand-name d-block">Instituto Horizonte</span><span class="csl-brand-subtitle d-block">Portal académico</span></span>
                </a>
                <div class="collapse navbar-collapse" id="csl-sidebar-navigation"><x-sidebar /></div>
            </div>
        </aside>

        <div class="page-wrapper csl-page">
            <header class="navbar navbar-expand-md navbar-light d-print-none csl-topbar">
                <div class="container-xl">
                    <div class="navbar-nav flex-row order-md-last align-items-center">
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Abrir menú de usuario">
                                @if(auth()->user()->profile_picture)
                                    <span class="avatar avatar-sm" style="background-image: url('{{ asset(auth()->user()->profile_picture) }}')"></span>
                                @else
                                    <span class="avatar avatar-sm csl-avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                                @endif
                                <div class="d-none d-xl-block ps-2"><div>{{ auth()->user()->name }}</div><div class="mt-1 small text-secondary">{{ auth()->user()->getRoleNames()->first() ?? 'Usuario' }}</div></div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="fas fa-user-circle me-2"></i> Mi perfil</a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('logout') }}" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-right-from-bracket me-2"></i> Cerrar sesión</a>
                            </div>
                        </div>
                    </div>
                    <div class="navbar-nav"><div class="nav-item d-flex align-items-center text-secondary small"><i class="ti ti-layout-dashboard me-2"></i> Portal académico</div></div>
                </div>
            </header>

            <main class="csl-main">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert"><div class="d-flex"><i class="fas fa-circle-check me-2 mt-1"></i><div>{{ session('success') }}</div></div><a class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></a></div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert"><div class="d-flex"><i class="fas fa-circle-exclamation me-2 mt-1"></i><div>{{ session('error') }}</div></div><a class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></a></div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    @yield('scripts')
    @stack('scripts')
</body>
</html>

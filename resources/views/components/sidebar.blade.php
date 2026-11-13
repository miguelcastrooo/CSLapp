@php
    $usuario = auth()->user();
    $puedeGestionarAlumnos = $usuario->hasAnyRole(['SuperAdmin', 'ControlEscolar', 'CoordinacionPreescolar', 'CoordinacionPrimaria', 'CoordinacionSecundaria']);
    $accesoCompleto = $usuario->hasAnyRole(['SuperAdmin', 'ControlEscolar']);
@endphp

<div class="d-flex flex-column w-100 h-100 pt-3">
    <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fas fa-th-large"></i><span>Panel principal</span></a></li>

        @if($usuario->hasRole('SuperAdmin'))
            <li class="csl-sidebar-section">Administración</li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.assignRoles') ? 'active' : '' }}" href="{{ route('admin.assignRoles') }}"><i class="fas fa-user-cog"></i><span>Usuarios y roles</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.configuracion*') ? 'active' : '' }}" href="{{ route('admin.configuracion') }}"><i class="fas fa-sliders"></i><span>Configuración académica</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.respaldo') ? 'active' : '' }}" href="{{ route('admin.respaldo') }}"><i class="fas fa-hard-drive"></i><span>Respaldo del sistema</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.historial') ? 'active' : '' }}" href="{{ route('admin.historial') }}"><i class="fas fa-clock-rotate-left"></i><span>Historial de cambios</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.historialSeguridad') ? 'active' : '' }}" href="{{ route('admin.historialSeguridad') }}"><i class="fas fa-shield-halved"></i><span>Historial de seguridad</span></a></li>
        @endif

        @if($puedeGestionarAlumnos)
            <li class="csl-sidebar-section">Alumnos</li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('capturista.selectsearch', 'capturista.index', 'alumnos.search*') ? 'active' : '' }}" href="{{ route('capturista.selectsearch') }}"><i class="fas fa-users"></i><span>{{ $accesoCompleto ? 'Ver alumnos' : 'Mis alumnos' }}</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('alumnos.select', 'alumnos.create') ? 'active' : '' }}" href="{{ route('alumnos.select') }}"><i class="fas fa-user-plus"></i><span>Registrar alumno</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.select', 'niveles.show') ? 'active' : '' }}" href="{{ route('admin.select') }}"><i class="fas fa-layer-group"></i><span>Plataformas</span></a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('index.baja') ? 'active' : '' }}" href="{{ route('index.baja') }}"><i class="fas fa-user-minus"></i><span>Dar de baja</span></a></li>
            @if($accesoCompleto)
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('alumnos.archivados') ? 'active' : '' }}" href="{{ route('alumnos.archivados') }}"><i class="fas fa-box-archive"></i><span>Alumnos archivados</span></a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('alumnos.egresados') ? 'active' : '' }}" href="{{ route('alumnos.egresados') }}"><i class="fas fa-graduation-cap"></i><span>Egresados</span></a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.grupos.*') ? 'active' : '' }}" href="{{ route('admin.grupos.form') }}"><i class="fas fa-people-arrows"></i><span>Mover alumnos</span></a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.cierre-escolar') ? 'active' : '' }}" href="{{ route('admin.cierre-escolar') }}"><i class="fas fa-calendar-check"></i><span>Cierre escolar</span></a></li>
            @endif
        @endif

    </ul>

    <div class="mt-auto csl-sidebar-footer border-top pt-3 mx-2 mb-3">
        <a href="{{ route('profile.edit') }}" class="nav-link"><i class="fas fa-user-pen"></i><span>Mi perfil</span></a>
        <a href="{{ route('logout') }}" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-right-from-bracket"></i><span>Cerrar sesión</span></a>
    </div>
</div>

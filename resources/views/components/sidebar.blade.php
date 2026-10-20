<div class="sidebar p-3">
    <!-- Logo y nombre del colegio centrado -->
    <div class="d-flex flex-column align-items-center mb-3">
        <img src="{{ asset('img/sanluis.webp') }}" alt="Icono" class="mb-2" style="width: 100px; height: 100px;">
        <h3 class="text-center">
            <a href="{{ route('dashboard') }}" style="color: white; text-decoration: none;">Colegio San Luis</a>
        </h3>
    </div>

    <!-- Foto de perfil centrada -->
    <div class="d-flex justify-content-center mb-3">
        @if(Auth::user()->profile_picture)
            <img src="{{ asset(Auth::user()->profile_picture) }}" class="rounded-circle img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
        @else
            <i class="fas fa-user-circle fa-5x text-white"></i>
        @endif
    </div>

    <h6 class="text-center text-white">Bienvenido, {{ Auth::user()->name }}</h6>

 <div class="d-flex justify-content-center mb-3">
    <a href="{{ route('profile.edit') }}" class="btn btn-outline-light btn-sm text-white">
        <i class="fas fa-user-circle"></i>Mi Perfil
    </a>
</div>


    <!-- Menú lateral -->
    <ul class="nav flex-column">
        @if (Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('ControlEscolar'))
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.select') }}"><i class="fas fa-layer-group"></i> Plataformas</a></li>
        @endif
        @if (Auth::user()->hasRole('SuperAdmin'))
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.grupos.form') }}"><i class="fas fa-people-arrows"></i> Mover Alumnos</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.assignRoles') }}"><i class="fas fa-user-cog"></i> Asignar Roles</a></li>
        @endif
         <!-- 
        @if (Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('CoordinacionPreescolar') || Auth::user()->hasRole('CoordinacionPrimaria') || Auth::user()->hasRole('CoordinacionSecundaria'))
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.selectGrado') }}"><i class="fas fa-user-graduate"></i> Plataformas Alumno</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('calificaciones.index') }}"><i class="fas fa-graduation-cap"></i> Calificaciones</a></li>
        @endif
        -->
        @if (Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('ControlEscolar'))
            <li class="nav-item"><a class="nav-link" href="{{ route('capturista.selectsearch') }}"><i class="fas fa-users-viewfinder"></i> Ver Alumnos</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('alumnos.select') }}"><i class="fas fa-user-plus"></i> Registrar Alumno</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('index.baja') }}"><i class="fas fa-user-minus"></i> Dar de Baja</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('alumnos.archivados') }}"><i class="fas fa-sign-out-alt"></i> Alumnos Archivados</a></li>
        @endif
    </ul>

    <div class="d-flex justify-content-center mt-3">
        <a class="btn btn-danger btn-sm" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </a>
    </div>
</div>

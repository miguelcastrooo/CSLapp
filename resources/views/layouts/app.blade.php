<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Carga de estilos con Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')
    <link href="{{ asset('fontawesome/css/all.min.css') }}" rel="stylesheet">

</head>
<body class="font-sans antialiased">
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar bg-dark text-white" style="width: 250px; height: 100vh; position: fixed; top: 0; left: 0;">
            <div class="p-3">
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <img src="{{ asset('img/san-luis_512%20(1).webp') }}" alt="Icono" style="width: 100px; height: 100px; margin-bottom: 10px;">
                    <h3 class="text-white">
                        <a class="nav-link text-white" href="{{ route('dashboard') }}">
                            Colegio San Luis
                        </a>
                    </h3>
                </div>

                <!-- Profile Section -->
                <div class="mt-5">
                    <h6 class="text-center text-white">Bienvenido, {{ Auth::user()->name }}</h6>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('profile.edit') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-user-circle"></i> Mi Perfil
                        </a>
                    </div>
                </div>
            </div>

            <ul class="nav flex-column">
                @if (Auth::user()->hasRole('SuperAdmin'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.select') }}">
                            <i class="fas fa-cogs"></i> Credenciales de Alumno
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.selectadmin') }}">
                            <i class="fas fa-user"></i> Administrar Alumnos
                        </a>
                    </li>
                @endif

                @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('AdministracionPreescolar') || Auth::user()->hasRole('AdministracionPrimariaBaja') || Auth::user()->hasRole('AdministracionPrimariaAlta') || Auth::user()->hasRole('AdministracionSecundaria'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('coordinacion.index') }}">
                            <i class="fas fa-cogs"></i> Administrar por Nivel
                        </a>
                    </li>
                @endif

                @if (Auth::user()->hasRole('ControlEscolar'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('capturista.index') }}">
                            <i class="fas fa-search"></i> Buscar Alumnos
                        </a>
                    </li>
                @endif

                @if (Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('ControlEscolar'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('alumnos.select') }}">
                            <i class="fas fa-user-plus"></i> Registrar Alumno
                        </a>
                    </li>
                @endif

                @if (Auth::user()->hasRole('SuperAdmin'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.search') }}">
                            <i class="fas fa-search"></i> Buscar Alumno 
                        </a>
                    </li>
                @endif

                @if (Auth::user()->hasRole('SuperAdmin'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">
                            <i class="fas fa-cogs"></i> Dar Rol
                        </a>
                    </li>
                @endif
            </ul>

            <div class="d-flex justify-content-center mt-2">
                <a class="btn btn-danger btn-sm" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container-fluid" style="margin-left: 250px;">
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    @stack('scripts')
</body>
</html>

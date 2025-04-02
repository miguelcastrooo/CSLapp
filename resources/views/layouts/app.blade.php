<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CSL</title>

    <link rel="icon" href="{{ asset('img/san-luis_512%20(1).webp') }}" type="image/x-icon">

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Carga de estilos con Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')
    <link href="{{ asset('fontawesome/css/all.min.css') }}" rel="stylesheet">

    <style>
            html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(to right, rgba(255, 255, 255, 0.8), rgba(218, 236, 23, 0.8), rgba(44, 100, 51, 0.8), rgba(96, 61, 44, 0.8));
            background-size: cover; /* Asegura que el fondo se expanda */
            background-attachment: fixed; /* Mantiene el fondo fijo mientras se desplaza */
            color: #333;
            display: flex;
            flex-direction: column;
        }

        .d-flex {
            flex-grow: 1;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: black;
            color: white;
            overflow-y: auto; /* Permite el desplazamiento vertical */
            max-height: 100vh; /* Ajusta la altura para permitir el scroll */
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="p-3">
            <div class="d-flex justify-content-center align-items-center flex-column">
    <!-- Logo y nombre del colegio -->
    <div class="text-center mb-3">
        <center><img src="{{ asset('img/san-luis_512%20(1).webp') }}" alt="Icono" style="width: 100px; height: 100px; margin-bottom: 10px;"></center>
        <h3 class="text-white">
            <a class="nav-link text-white" href="{{ route('dashboard') }}">
                Colegio San Luis
            </a>
        </h3>
    </div>

    <!-- Foto de perfil -->
    <div class="d-flex justify-content-center">
        @if(Auth::user()->profile_picture)
            <img src="{{ asset(Auth::user()->profile_picture) }}" 
                 alt="Foto de perfil" 
                 class="rounded-circle img-thumbnail" 
                 style="width: 100px; height: 100px; object-fit: cover;">
        @else
            <i class="fas fa-user-circle fa-5x text-light"></i> <!-- Ícono si no hay foto -->
        @endif
    </div>
</div>

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
                @if (Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('ControlEscolar'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.select') }}">
                            <i class="fas fa-cogs"></i> Plataformas
                        </a>
                    </li>
                @endif

                @if (Auth::user()->hasRole('SuperAdmin'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.grupos.form') }}">
                            <i class="fas fa-user-plus"></i> Mover Grupos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.assignRoles') }}">
                            <i class="fas fa-user-cog"></i> Asignar Roles
                        </a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.selectadmin') }}">
                            <i class="fas fa-user"></i> Administrar Alumnos
                        </a>
                    </li>-->
                @endif

                @if (Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('CoordinacionPreescolar') || Auth::user()->hasRole('CoordinacionPrimaria') || Auth::user()->hasRole('CoordinacionSecundaria'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.selectGrado') }}">
                            <i class="fas fa-user-plus"></i> Plataformas Alumno
                        </a>
                    </li>
                @endif

                @if (Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('ControlEscolar'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('capturista.selectsearch') }}">
                            <i class="fas fa-search"></i> Ver Alumnos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('alumnos.select') }}">
                            <i class="fas fa-user-plus"></i> Registrar Alumno
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('index.baja') }}">
                            <i class="fas fa-user-minus"></i> Dar de Baja un Alumno
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('alumnos.archivados') }}">
                            <i class="fas fa-redo"></i> Alumnos Archivados
                        </a>
                    </li>
                @endif   
            </ul>

            <div class="d-flex justify-content-center mt-2">
                <a class="btn btn-danger btn-sm" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
            <br><br>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

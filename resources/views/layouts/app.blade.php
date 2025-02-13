<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    @yield('styles')
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
                    @if (Auth::user()->role === 'super_admin')
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('admin.select') }}">
                                <i class="fas fa-cogs"></i> Plataformas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('admin.index') }}">
                                <i class="fas fa-user"></i> Admin
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->role === 'super_admin' || Auth::user()->role === 'control_escolar')
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('alumnos.index') }}">
                                <i class="fas fa-search"></i> Buscar Alumnos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('alumnos.select') }}">
                                <i class="fas fa-users"></i> Dar de alta un alumno
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- Scripts adicionales -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS (asegúrate de que sea la versión correcta para tu proyecto) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>

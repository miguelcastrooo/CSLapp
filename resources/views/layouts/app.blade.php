<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CSL</title>
    <link rel="icon" href="{{ asset('img/san-luis_512%20(1).webp') }}" type="image/x-icon">

   <!-- Bootstrap CSS (solo CDN) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<!-- Bootstrap Bundle JS (solo CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <!-- FontAwesome -->
    <link href="{{ asset('fontawesome/css/all.min.css') }}" rel="stylesheet">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(-45deg,
                rgba(44, 100, 51, 0.8),
                rgba(255, 255, 255, 0.8),
                rgba(255, 193, 7, 0.7));
            background-size: 400% 400%;
            animation: animatedBackground 12s ease infinite;
            color: #2e2e2e;
        }
        @keyframes animatedBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #1f1f1f, #3c3c3c);
            color: #fff;
            padding: 1rem;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            animation: fadeInSidebar 0.6s ease forwards;
        }
        .sidebar.hidden {
            transform: translateX(-300px);
            opacity: 0;
            pointer-events: none;
        }
        @keyframes fadeInSidebar {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .sidebar a {
            color: #ddd;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: background 0.3s ease, color 0.3s ease, transform 0.2s ease;
        }
        .sidebar a:hover {
            background: #ffc107;
            color: #000;
            transform: scale(1.05);
            box-shadow: 0 0 8px #ffc107aa;
        }
        .sidebar .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }
        .sidebar a:hover .nav-link i {
            color: #000;
        }
        .sidebar .profile-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #2c6433;
            transition: border-color 0.3s ease, transform 0.3s ease;
        }
        .sidebar .profile-img:hover {
            border-color: #ffc107;
            transform: scale(1.1);
            box-shadow: 0 0 10px #ffc107aa;
        }
        .sidebar .logo {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
            filter: drop-shadow(0 0 3px #2c6433);
            transition: filter 0.3s ease;
        }
        .sidebar .logo:hover {
            filter: drop-shadow(0 0 6px #ffc107);
        }
        .sidebar .section-title {
            font-size: 0.8rem;
            text-transform: uppercase;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            color: #aaa;
            letter-spacing: 1px;
        }
        .btn-logout {
            background-color: #dc3545;
            border: none;
            width: 100%;
            margin-top: 1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-logout:hover {
            background-color: #c82333;
            transform: scale(1.05);
            box-shadow: 0 0 8px #c82333aa;
        }
        .main-content {
            margin-left: 260px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        .main-content.expanded {
            margin-left: 0;
        }
        #toggle-sidebar-btn {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1050;
            background-color: #ffc107;
            border: none;
            color: #000;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 0 8px #ffc107aa;
            transition: background-color 0.3s ease;
        }
        #toggle-sidebar-btn:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body class="font-sans antialiased">

    <!-- Botón para ocultar/mostrar sidebar -->
    <button id="toggle-sidebar-btn" aria-label="Toggle sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar usando el componente -->
    <x-sidebar />

    <!-- Contenido Principal -->
    <div class="main-content">
        @isset($header)
            <header class="bg-white shadow mb-3 p-3 rounded">
                {{ $header }}
            </header>
        @endisset

        <main>
            @yield('content')
        </main>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    @stack('scripts')

    <!-- Bootstrap Bundle JS (CDN con fallback local) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            onerror="this.onerror=null;this.src='{{ asset('js/bootstrap.bundle.min.js') }}';"></script>

    <!-- Script para toggle de sidebar -->
    <script>
        const toggleBtn = document.getElementById('toggle-sidebar-btn');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('expanded');
        });
    </script>
</body>
</html>

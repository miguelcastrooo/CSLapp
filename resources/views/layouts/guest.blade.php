<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Instituto Horizonte')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('img/demo-school.svg') }}" type="image/svg+xml">
    
    @yield('styles')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f3f6fa;
            color: #243442;
        }

        /* NAVBAR */
        .navbar {
            background: #fff;
            border-bottom: 1px solid #d6e0ea;
        }

        .navbar.scrolled {
            background: #fff;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #183444 !important;
        }

        .nav-link {
            color: #24536d !important;
            font-weight: 500;
            transition: color 0.3s ease-in-out;
        }

        .nav-link:hover {
            color: #176b62 !important;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/demo-school.svg') }}" alt="Instituto Horizonte" style="width: 42px; height: 42px; margin-right: 10px;">
            Instituto Horizonte
            </a>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <div style="margin-top: 70px;">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>

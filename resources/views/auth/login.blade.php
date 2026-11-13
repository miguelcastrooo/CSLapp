@extends('layouts.guest')

@section('title', 'Iniciar Sesión')

@section('styles')
    <style>
        body {
            background: #f3f6fa;
            color: #243442;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 70px);
            padding: 20px;
        }

        .login-card {
            display: flex;
            flex-direction: row;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 32px rgba(29, 52, 70, 0.12);
            max-width: 900px;
            width: 100%;
        }

        /* Sección de la imagen con información */
        .info-section {
            color: white;
            width: 50%;
        }

        .login-carousel, .login-carousel .carousel-inner, .login-carousel .carousel-item { height: 100%; }
        .login-carousel-slide { align-items: center; background-position: center; background-size: cover; display: flex; min-height: 520px; }
        .login-carousel-overlay { background: linear-gradient(135deg, rgba(24, 68, 91, .93), rgba(23, 107, 98, .72)); min-height: 520px; padding: 36px; text-align: center; width: 100%; }
        .login-carousel-overlay img { width: 92px; }
        .login-carousel-overlay p { margin: 0 auto; max-width: 300px; }

        .info-section img {
            width: 100px;
            height: auto;
            margin-bottom: 15px;
        }

        .info-section h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .info-section p {
            font-size: 1rem;
        }

        /* Sección del login */
        .form-section {
            width: 50%;
            padding: 30px;
        }

        .card-header {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #183444;
        }

        .form-control {
            border-radius: 9px;
            border: 1px solid #b9c9d6;
            min-height: 46px;
        }

        .form-control:focus {
            border-color: #238478;
            box-shadow: 0 0 0 .2rem rgba(35, 132, 120, .15);
        }

        .btn-primary {
            background-color: #176b62;
            border: none;
            border-radius: 9px;
            padding: 10px;
            font-size: 1rem;
        }

        .btn-primary:hover {
            background-color: #14534d;
        }

        .text-muted a {
            color: #176b62;
            text-decoration: none;
        }

        .text-muted a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
            }

            .info-section,
            .form-section {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-card">
        <div id="loginCarousel" class="carousel slide carousel-fade info-section login-carousel" data-bs-interval="false">
            <div class="carousel-indicators"><button type="button" data-bs-target="#loginCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Mensaje 1"></button><button type="button" data-bs-target="#loginCarousel" data-bs-slide-to="1" aria-label="Mensaje 2"></button><button type="button" data-bs-target="#loginCarousel" data-bs-slide-to="2" aria-label="Mensaje 3"></button></div>
            <div class="carousel-inner">
                <div class="carousel-item active"><div class="login-carousel-slide" style="background-image: url('{{ asset('img/login-educativo.png') }}');"><div class="login-carousel-overlay"><img src="{{ asset('img/demo-school.svg') }}" alt="Instituto Horizonte"><h2>Instituto Horizonte</h2><p>Una comunidad educativa organizada y cercana.</p></div></div></div>
                <div class="carousel-item"><div class="login-carousel-slide" style="background-image: url('{{ asset('img/dashboard-educativo.png') }}');"><div class="login-carousel-overlay"><img src="{{ asset('img/demo-school.svg') }}" alt="Instituto Horizonte"><h2>Información al alcance</h2><p>Consulta expedientes y plataformas de forma clara y segura.</p></div></div></div>
                <div class="carousel-item"><div class="login-carousel-slide" style="background-image: url('{{ asset('img/login-educativo.png') }}');"><div class="login-carousel-overlay"><img src="{{ asset('img/demo-school.svg') }}" alt="Instituto Horizonte"><h2>Trabajo que acompaña</h2><p>Herramientas sencillas para el equipo escolar.</p></div></div></div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#loginCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Anterior</span></button>
            <button class="carousel-control-next" type="button" data-bs-target="#loginCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Siguiente</span></button>
        </div>

        <!-- Sección del login -->
        <div class="form-section">
            <div class="card-header">
                <i class="fa fa-sign-in"></i> Iniciar Sesión
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-right-to-bracket me-2"></i>Entrar al sistema</button>
                </form>
            </div>
            <div class="card-footer text-center text-muted">
                <span>¿Necesitas acceso o restablecer tu contraseña? Solicítalo a la administración.</span>
            </div>
        </div>
    </div>
</div>
@endsection

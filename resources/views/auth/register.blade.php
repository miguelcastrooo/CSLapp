@extends('layouts.guest')

@section('title', 'Registrarse')

@section('styles')
    <style>
        body {
            background: linear-gradient(to right, rgb(255, 255, 255), rgb(218, 236, 23), rgb(44, 100, 51));
            color: white;
        }

        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .register-card {
            display: flex;
            flex-direction: row;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            width: 100%;
        }

        /* Sección de la imagen con información */
        .info-section {
            background: #2c6433;
            color: white;
            padding: 30px;
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

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

        /* Sección del registro */
        .form-section {
            width: 50%;
            padding: 30px;
        }

        .card-header {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #0f2027;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 1rem;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .text-muted a {
            color: #007bff;
            text-decoration: none;
        }

        .text-muted a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .register-card {
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
<div class="register-container">
    <div class="register-card">
        <!-- Sección de la imagen con información -->
        <div class="info-section">
            <img src="{{asset('img/sanluis.webp') }}" alt="Colegio San Luis">
            <h2>Bienvenido al Colegio San Luis</h2>
            <p>Forma parte de nuestra comunidad educativa y desarrolla tu máximo potencial en un entorno de aprendizaje innovador y con valores.</p>
        </div>

        <!-- Sección del registro -->
        <div class="form-section">
            <div class="card-header">
                <i class="fa fa-user-plus"></i> Regístrate
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                </form>
            </div>
            <div class="card-footer text-center text-muted">
                <a href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.guest')

@section('title', 'Iniciar Sesión')

@section('styles')
    <style>
        body {
            background: linear-gradient(to right,rgb(255, 255, 255),rgb(218, 236, 23),rgb(44, 100, 51));
            color: white;
        }
        
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            max-width: 380px;
            width: 100%;
            border-radius: 12px;
            background: #ffffff;
            padding: 25px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
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
    </style>
@endsection

@section('content')
<div class="login-container">
    <div class="card">
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

                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
        </div>
        <div class="card-footer text-center text-muted">
            <a href="{{ route('register') }}">¿No tienes cuenta? Regístrate</a>
        </div>
    </div>
</div>
@endsection

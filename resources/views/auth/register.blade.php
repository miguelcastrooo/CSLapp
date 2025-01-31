@extends('layouts.guest')

@section('title', 'Registrarse')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
        .card {
            max-width: 400px;
            margin: 0 auto;
            border-radius: 15px;
        }

        .card-header {
            background-color:rgb(41, 45, 44);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .card-footer {
            background-color: #f7f7f7;
            border-radius: 0 0 15px 15px;
        }

        .card-body {
            padding: 30px;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            background-color:rgb(41, 45, 44);
            border-color: #3E8E7E;
        }

        .btn-primary:hover {
            background-color: #2F6E6B;
        }

        .fa {
            margin-right: 8px;
        }
    </style>
@endsection

@section('content')
<div class="card shadow-lg">
    <div class="card-header text-center">
        <h4><i class="fa fa-user-plus"></i> Regístrate</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nombre Completo</label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus 
                       pattern="[A-Za-zÀ-ÿ\s]+" 
                       title="El nombre solo puede contener letras y espacios.">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        pattern="[a-zA-Z0-9._%+-]+@(gmail\.com|hotmail\.com|outlook\.com|yahoo\.com)" 
                        title="El correo debe ser válido y pertenecer a Gmail, Hotmail, Outlook o Yahoo.">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>



            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       required 
                       minlength="8" 
                       title="La contraseña debe tener al menos 8 caracteres.">
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input type="password" 
                       class="form-control" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Registrarse</button>
        </form>
    </div>
    <div class="card-footer text-center">
        <a href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
    </div>
</div>

</div>
@endsection

@extends('layouts.guest')

@section('title', 'Iniciar Sesión')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
        .card {
            max-width: 400px;
            margin: 0 auto;
            border-radius: 15px;
        }

        .card-header {
            background-color:rgb(0, 0, 0);
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
            background-color:rgb(0, 0, 0);
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
        <h4><i class="fa fa-sign-in"></i> Iniciar Sesión</h4>
    </div>
    <div class="card-body">
       <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               id="email" 
               name="email" 
               value="{{ old('email') }}" 
               required 
               autofocus 
               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
               title="Introduce un correo electrónico válido.">
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
               value="{{ old('password') }}" 
               required 
               minlength="8">
        @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100">Entrar</button>
</form>

    </div>
    <div class="card-footer text-center">
        <a href="{{ route('register') }}">¿No tienes cuenta? Regístrate</a>
    </div>
</div>
@endsection

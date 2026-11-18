@extends('layouts.guest')

@section('title', 'Iniciar Sesión')

@section('styles')
    <style>
        body {
            background: var(--csl-surface);
            color: var(--csl-text);
        }

        body .navbar { display: none; }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 0;
            margin-top: -70px;
        }

        .login-card {
            display: flex;
            flex-direction: row;
            background: #ffffff;
            border-radius: 0;
            min-height: 100vh;
            max-width: none;
            width: 100%;
        }

        /* Sección de la imagen con información */
        .info-section {
            color: white;
            width: 55%;
        }

        .login-carousel, .login-carousel .carousel-inner, .login-carousel .carousel-item { height: 100%; }
        .login-carousel-slide { align-items: center; background-position: center; background-size: cover; display: flex; min-height: 520px; }
        .login-carousel-overlay { background: linear-gradient(135deg, color-mix(in srgb, var(--csl-carousel) 94%, #000), color-mix(in srgb, var(--csl-accent) 76%, #000)); min-height: 100vh; padding: clamp(2.5rem, 8vw, 7rem); text-align: left; width: 100%; }
        .login-carousel-overlay img { width: 92px; }
        .login-carousel-overlay p { margin: 0 auto; max-width: 300px; }

        .info-section img {
            width: 100px;
            height: auto;
            margin-bottom: 15px;
        }

        .info-section h2 {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            margin-bottom: 10px;
            line-height: 1;
        }

        .info-section p {
            font-size: 1.12rem;
            max-width: 470px;
        }

        .login-welcome { background: linear-gradient(145deg, var(--csl-carousel), var(--csl-accent)); display: flex; flex-direction: column; justify-content: center; padding: clamp(2.5rem, 8vw, 7rem); }
        .login-brand-mark { align-items: center; background: #fff; border-radius: 16px; display: flex; height: 58px; justify-content: center; margin-bottom: 3rem; width: 58px; }
        .login-brand-mark img { height: 42px; margin: 0; max-width: 42px; object-fit: contain; width: 42px; }
        .login-eyebrow { font-size: .76rem !important; font-weight: 800; letter-spacing: .12em; margin: 0 0 .6rem !important; opacity: .8; }
        .login-eyebrow-dark { color: var(--csl-text-muted); opacity: 1; }

        /* Sección del login */
        .form-section {
            width: 45%;
            padding: clamp(2.5rem, 6vw, 6rem);
            align-self: center;
        }

        .card-header {
            text-align: left;
            font-size: 2rem;
            font-weight: bold;
            color: var(--csl-text);
        }

        .form-section .card-body { padding: 1.5rem 0 0; }
        .form-section .card-footer { background: transparent; border: 0; padding: 1.5rem 0 0; }

        .form-control {
            border-radius: 9px;
            border: 1px solid #b9c9d6;
            min-height: 46px;
        }

        .form-control:focus {
            border-color: var(--csl-accent);
            box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--csl-accent) 18%, transparent);
        }

        .btn-primary {
            background-color: var(--csl-accent);
            border: none;
            border-radius: 9px;
            padding: 10px;
            font-size: 1rem;
        }

        .btn-primary:hover {
            background-color: color-mix(in srgb, var(--csl-accent) 82%, #000);
        }

        .text-muted a {
            color: var(--csl-accent);
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
            .login-carousel-overlay { min-height: 320px; padding: 3rem 2rem; }
            .form-section { padding: 2.5rem 2rem 3.5rem; }
        }
    </style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="info-section login-welcome">
            <div class="login-brand-mark"><img src="{{ $configuracionInstitucional->logoUrl() }}" alt="Logotipo de {{ $configuracionInstitucional->nombre }}"></div>
            <p class="login-eyebrow">{{ strtoupper($configuracionInstitucional->nombre) }}</p>
            <h2>{{ $configuracionInstitucional->subtitulo ?: 'Portal académico' }}</h2>
            <p>Consulta y administra la información escolar de forma clara, segura y ordenada.</p>
        </div>

        <!-- Sección del login -->
        <div class="form-section">
            <p class="login-eyebrow login-eyebrow-dark">ACCESO DEL PERSONAL</p>
            <div class="card-header">Inicia sesión</div>
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
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            <button type="button" class="btn btn-outline-secondary" data-password-toggle="password" aria-label="Mostrar contraseña" title="Mostrar contraseña"><i class="fas fa-eye"></i></button>
                        </div>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-right-to-bracket me-2"></i>Entrar al sistema</button>
                </form>
            </div>
            <div class="card-footer text-start text-muted">
                <span>¿Necesitas acceso o restablecer tu contraseña? Solicítalo a la administración.</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const field = document.getElementById(button.dataset.passwordToggle);
        const visible = field.type === 'text';
        field.type = visible ? 'password' : 'text';
        button.querySelector('i').className = visible ? 'fas fa-eye' : 'fas fa-eye-slash';
        button.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        button.title = visible ? 'Mostrar contraseña' : 'Ocultar contraseña';
    });
});
</script>
@endsection

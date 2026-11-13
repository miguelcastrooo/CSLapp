@extends('layouts.app')

@section('content')
<div class="container-xl py-4">
    <div class="csl-module-header"><h1>Mi perfil</h1><p>Actualiza tus datos personales, imagen y contraseña de acceso.</p></div>
    <x-task-guide title="Actualiza tu información" description="Tu rol, contraseña y estado de acceso son administrados por SuperAdmin." :steps="['Actualiza solo tus datos personales.', 'Cambia tu fotografía si la necesitas.', 'Solicita a administración cualquier cambio de acceso.']" />
    <div class="card csl-data-card mb-4"><div class="card-body d-flex align-items-center gap-3"><span class="csl-management-icon"><i class="fas fa-id-badge"></i></span><div><div class="fw-bold">{{ $user->getRoleNames()->implode(' · ') ?: 'Sin rol asignado' }}</div><div class="text-secondary small">Estado de acceso: {{ $user->activo ? 'Cuenta activa' : 'Cuenta desactivada' }}</div></div></div></div>

        <!-- Mostrar alerta de éxito -->
        @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ __('Éxito!') }}</strong> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Formulario de Foto de perfil -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-camera"></i> {{ __('Foto de perfil') }}</h3>
        </div>
        <div class="card-body">
            <!-- Formulario solo para la imagen -->
            <form method="POST" action="{{ route('profile.update-picture') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

             <!-- Previsualización de la imagen -->
<div class="mb-3 text-center">
    <label for="profile_picture" class="form-label">{{ __('Seleccionar Foto') }}</label>
    <input type="file" id="profile_picture" name="profile_picture" class="form-control" onchange="previewImage(event)">

    <!-- Mostrar foto actual o ícono si no hay foto -->
    <div class="mt-3">
        @if($user->profile_picture)
          <center>  <img id="current_picture" src="{{ asset($user->profile_picture) }}"
                 alt="Foto de perfil" 
                 class="rounded-circle img-thumbnail" 
                 style="width: 150px; height: 150px; object-fit: cover;"></center>
        @else
            <i id="current_picture" class="fas fa-user-circle fa-5x text-muted"></i> <!-- Ícono si no hay foto -->
        @endif
    </div>

    <!-- Área de previsualización -->
    <div class="mt-2">
        <img id="preview_img" class="rounded-circle mt-2" style="width: 150px; height: 150px; object-fit: cover; display: none;">
    </div>
</div>
                </div>

                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2"><i class="fas fa-save"></i><span>{{ __('Guardar foto') }}</span></button>
            </form>
        </div>
    </div>

    <!-- Formulario de Datos de perfil -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user"></i> {{ __('Datos de perfil') }}</h3>
        </div>
        <div class="card-body">
            <!-- Formulario solo para los datos -->
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Nombre') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Correo Electrónico') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                    <div class="form-text">Si cambias el correo, podría requerirse verificación según la configuración de la escuela.</div>
                </div>

                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2"><i class="fas fa-save"></i><span>{{ __('Guardar cambios') }}</span></button>
            </form>
        </div>
    </div>

</div>

<script>
    // Función para previsualizar la imagen antes de enviarla
    function previewImage(event) {
        const reader = new FileReader();
        const preview = document.getElementById('preview_img');
        const currentPicture = document.getElementById('current_picture');
        
        // Cuando la imagen esté cargada, se mostrará
        reader.onload = function () {
            preview.style.display = 'block';
            preview.src = reader.result;
            currentPicture.style.display = 'none'; // Ocultamos la imagen actual o el ícono
        };

        if (event.target.files.length > 0) {
            reader.readAsDataURL(event.target.files[0]); // Leemos la imagen seleccionada
        }
    }
</script>

@endsection

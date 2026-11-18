@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
<div class="container-xl py-4 csl-profile-page">
    <div class="csl-module-header"><h1>Mi perfil</h1><p>Consulta tu acceso y actualiza sólo tus datos personales.</p></div>
    <x-task-guide title="Actualiza tu información" description="Tu rol, contraseña y estado de acceso son administrados por Admin." :steps="['Revisa los datos que aparecen en tu perfil.', 'Cambia tu foto si lo necesitas.', 'Guarda cada sección al terminar.']" />

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fas fa-circle-check me-2"></i>{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>
    @endif

    <div class="card csl-data-card mb-4">
        <div class="card-body d-flex align-items-center gap-3"><span class="csl-management-icon"><i class="fas fa-id-badge"></i></span><div><div class="fw-bold">{{ $user->getRoleNames()->map(fn ($rol) => \App\Support\RoleLabel::for($rol))->implode(' · ') ?: 'Sin rol asignado' }}</div><div class="text-secondary small">Estado de acceso: {{ $user->activo ? 'Cuenta activa' : 'Cuenta desactivada' }}</div></div></div>
    </div>

    <div class="card csl-data-card mb-4">
        <div class="card-header"><h2 class="card-title mb-0"><i class="fas fa-camera me-2"></i>Foto de perfil</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update-picture') }}" enctype="multipart/form-data" class="csl-profile-photo-form">
                @csrf
                @method('PATCH')
                <div class="csl-profile-photo-layout">
                    <div class="csl-profile-photo-frame" aria-label="Vista previa de foto de perfil">
                        <div id="current-picture" class="csl-profile-current-picture">
                            @if($user->profile_picture)
                                <img src="{{ asset($user->profile_picture) }}" alt="Foto de perfil actual">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                        </div>
                        <img id="preview_img" alt="Vista previa de la nueva foto" class="csl-profile-preview d-none">
                    </div>
                    <div class="flex-fill">
                        <label for="profile_picture" class="form-label">Selecciona una foto</label>
                        <input type="file" id="profile_picture" name="profile_picture" class="form-control @error('profile_picture') is-invalid @enderror" accept="image/png,image/jpeg,image/jpg,image/gif" onchange="previewImage(event)">
                        @error('profile_picture')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <p class="text-secondary small mb-0 mt-2"><i class="fas fa-circle-info me-1"></i>Usa una imagen JPG, PNG o GIF de hasta 2 MB. La vista previa se muestra antes de guardar.</p>
                        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-floppy-disk me-2"></i>Guardar foto</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card csl-data-card">
        <div class="card-header"><h2 class="card-title mb-0"><i class="fas fa-user me-2"></i>Datos de perfil</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}" class="row g-3">
                @csrf
                @method('PATCH')
                <div class="col-md-6"><label for="name" class="form-label">Nombre</label><input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required autofocus>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-6"><label for="email" class="form-label">Correo electrónico</label><input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror<p class="form-text mb-0">Si cambias el correo, podría requerirse verificación según la configuración de la escuela.</p></div>
                <div class="col-12"><button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk me-2"></i>Guardar cambios</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    const preview = document.getElementById('preview_img');
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('d-none');
    document.getElementById('current-picture').classList.add('d-none');
}
</script>
@endsection

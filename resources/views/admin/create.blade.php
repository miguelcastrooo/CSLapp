@extends('layouts.app')

@section('title', 'Crear usuario | Instituto Horizonte')

@section('content')
<div class="container-xl py-4">
    <div class="csl-module-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h1>Crear usuario</h1>
            <p>Registra una cuenta para una integrante del equipo y asigna su acceso inicial.</p>
        </div>
        <a href="{{ route('admin.assignRoles') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver a usuarios</a>
    </div>

    <div class="card csl-data-card col-lg-8 mx-auto">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('admin.storeAdmin') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label for="name" class="form-label">Nombre completo</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label">Correo institucional</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contraseña temporal</label>
                        <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required>
                        <div class="form-text">Usa 12 o más caracteres, mayúscula, minúscula, número y símbolo.</div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label for="role" class="form-label">Rol y alcance</label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Selecciona un rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role') === $role->name)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">El rol determina los niveles educativos que la cuenta puede consultar.</div>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-user-check me-2"></i>Crear usuario</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

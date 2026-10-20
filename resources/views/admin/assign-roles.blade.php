@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-black">Asignar Roles</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Formulario para asignar roles -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-user-cog"></i> Asignar Roles a un Usuario
        </div>
        <div class="card-body">
            <form action="{{ route('admin.saveAssignedRoles') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="user_id" class="form-label">Seleccionar Usuario</label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="">Seleccione un usuario</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="roles" class="form-label">Seleccionar Roles</label>
                    <select class="form-select" id="roles" name="roles[]" multiple required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-dark w-100">
                    <i class="fas fa-check-circle"></i> Asignar Roles
                </button>
            </form>
        </div>
    </div>

    <!-- Tabla de usuarios y sus roles -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-users"></i> Usuarios y Roles Asignados
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre de Usuario</th>
                        <th>Correo</th>
                        <th>Roles Asignados</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-success mb-1 me-1">
                                        <i class="fas fa-tag"></i> {{ $role->name }}
                                    </span>
                                    <form action="{{ route('admin.removeRole', [$user->id, $role->id]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Quitar rol {{ $role->name }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endforeach
                            </td>
                            <td>
                                <!-- Otras posibles acciones aquí -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

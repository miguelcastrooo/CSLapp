@extends('layouts.app')

@section('title', 'Usuarios y roles | Instituto Horizonte')

@section('content')
<div class="container-xl py-4">
    <div class="csl-module-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h1>Usuarios y roles</h1>
            <p>Administra quién puede ingresar al portal y el alcance de cada cuenta.</p>
        </div>
        <a href="{{ route('admin.admincreate') }}" class="btn btn-primary"><i class="fas fa-user-plus me-2"></i>Crear usuario</a>
    </div>
    <x-task-guide title="Administrar accesos" description="Los roles definen las secciones y niveles que puede consultar cada cuenta." :steps="['Crea la cuenta si aún no existe.', 'Selecciona la cuenta y sus roles.', 'Revisa el estado de acceso antes de salir.']" />

    <div class="card csl-data-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-start gap-3 mb-4">
                <span class="csl-management-icon"><i class="fas fa-user-shield"></i></span>
                <div>
                    <h2 class="h3 mb-1">Asignar o actualizar roles</h2>
                    <p class="text-secondary mb-0">Elige una cuenta y marca los roles que debe tener. Los cambios quedan registrados.</p>
                </div>
            </div>

            <form action="{{ route('admin.saveAssignedRoles') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-5">
                    <label for="user_id" class="form-label">Cuenta del personal</label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="">Selecciona una cuenta</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} · {{ $user->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="roles" class="form-label">Roles asignados</label>
                    <select class="form-select" id="roles" name="roles[]" multiple required aria-describedby="roles-help">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <div id="roles-help" class="form-text">Para seleccionar varios roles, mantén presionada la tecla Ctrl.</div>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check me-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card csl-data-card">
        <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap py-3">
            <div>
                <h2 class="card-title mb-1">Cuentas registradas</h2>
                <span class="text-secondary">Revisa los roles y el acceso de cada integrante.</span>
            </div>
            <span class="badge bg-azure-lt">{{ $users->count() }} cuentas</span>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-hover csl-users-table mb-0">
                <thead>
                    <tr>
                        <th>Personal</th>
                        <th>Estado</th>
                        <th>Roles y permisos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="avatar csl-user-avatar">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <div class="text-secondary small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($user->activo)
                                    <span class="badge bg-green-lt"><i class="fas fa-circle-check me-1"></i>Activa</span>
                                @else
                                    <span class="badge bg-secondary-lt"><i class="fas fa-ban me-1"></i>Desactivada</span>
                                @endif
                            </td>
                            <td class="csl-user-roles">
                                @forelse($user->roles as $role)
                                    <span class="csl-role-chip">
                                        <i class="fas fa-shield-halved"></i>
                                        <span>{{ $role->name }}</span>
                                        @if (! $user->is(auth()->user()))
                                            <form action="{{ route('admin.removeRole', [$user->id, $role->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="csl-role-remove" title="Quitar el rol {{ $role->name }}" aria-label="Quitar el rol {{ $role->name }}" onclick="return confirm('¿Deseas quitar el rol {{ $role->name }}?')"><i class="fas fa-xmark"></i></button>
                                            </form>
                                        @endif
                                    </span>
                                @empty
                                    <span class="text-secondary small">Sin roles asignados</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                @if (! $user->is(auth()->user()))
                                    <form action="{{ route('admin.user.status', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $user->activo ? 'btn-outline-warning' : 'btn-outline-success' }}" onclick="return confirm('¿Deseas {{ $user->activo ? 'desactivar' : 'reactivar' }} la cuenta de {{ $user->name }}?')">
                                            <i class="fas {{ $user->activo ? 'fa-user-slash' : 'fa-user-check' }} me-1"></i>{{ $user->activo ? 'Desactivar' : 'Reactivar' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-secondary small">Tu cuenta actual</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-5">Aún no hay cuentas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

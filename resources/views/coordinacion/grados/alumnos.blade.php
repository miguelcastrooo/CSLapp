@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="csl-module-header"><h1>Alumnos de {{ ($grado ?? $nivelEducativo)->nombre }}</h1><p>Consulta el expediente; sólo puedes actualizar contraseñas de las plataformas autorizadas.</p></div>
        <div class="card csl-data-card"><div class="table-responsive">
            <table class="table table-vcenter table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumnos as $alumno)
                        <tr>
                            <td>{{ $alumno->id }}</td>
                            <td>{{ $alumno->nombre }}</td>
                            <td>{{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</td>
                            <td>
                                <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-primary btn-sm"><i class="fas fa-folder-open me-1"></i>Abrir expediente</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>@if($alumnos->hasPages())<div class="card-footer">{{ $alumnos->links() }}</div>@endif</div>
    </div>
@endsection

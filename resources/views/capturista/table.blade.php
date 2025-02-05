@foreach($alumnos as $alumno)
    <tr>
        <td>{{ $alumno->id }}</td>
        <td>{{ $alumno->matricula }}</td>
        <td>{{ $alumno->nombre }}</td>
        <td>{{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</td>
        <td>{{ $alumno->contacto1nombre }} - {{ $alumno->telefono1 }}</td>
        <td>{{ $alumno->contacto2nombre ? $alumno->contacto2nombre : 'N/A' }} - {{ $alumno->telefono2 ? $alumno->telefono2 : 'N/A' }}</td>
        <td>{{ $alumno->nivel_educativo }}</td>
        <td>{{ $alumno->grado }}</td>
        <td>{{ $alumno->correo_familia }}</td>
        <td>{{ \Carbon\Carbon::parse($alumno->fecha_inscripcion)->format('d/m/Y') }}</td>
        <td class="text-center">
            <div class="d-flex justify-content-between">
                <a href="{{ route('capturista.edit', $alumno->id) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <form action="{{ route('capturista.destroy', $alumno->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este alumno?')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </div>
        </td>
    </tr>
@endforeach

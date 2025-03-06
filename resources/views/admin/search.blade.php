@extends('layouts.app')

@section('content')

   <!-- Mostrar mensaje flash de éxito -->
   @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
<div class="container">
    <h1>Lista de Alumnos</h1>
    <p>Escribe en el campo de búsqueda para filtrar alumnos por matrícula, nombre o apellidos:</p>

    <input type="text" id="search" class="form-control mb-3" placeholder="Buscar alumnos...">

    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Matricula</th>
                <th>Nivel Educativo</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="alumnos-list">
            <!-- Los alumnos se mostrarán aquí después de la búsqueda -->
        </tbody>
    </table>
</div>

<script>
    document.getElementById('search').addEventListener('input', function() {
        let query = this.value;

        if (query.length >= 1) {
            // Realizar la petición AJAX
            fetch(`{{ route('alumnos.search') }}?search=${query}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                let resultsHTML = '';
                if (data.length > 0) {
                    data.forEach(alumno => {
                        resultsHTML += `
                            <tr>
                                <td>${alumno.matricula}</td>
                                <td>${alumno.nivel_educativo ? alumno.nivel_educativo.nombre : 'Sin Nivel'}</td>
                                <td>${alumno.nombre}</td>
                                <td>${alumno.apellidopaterno}</td>
                                <td>${alumno.apellidomaterno}</td>
                                <td>
                                    <a href="/admin/${alumno.id}/edit" class="btn btn-primary">Editar</a>
                                </td>
                                <td>
                                    <a href="/admin/alumnos/pdf/${alumno.id}" class="btn btn-danger">Generar PDF</a>
                                </td>
                            </tr>
                        `;
                    });
                    document.getElementById('alumnos-list').innerHTML = resultsHTML;
                } else {
                    document.getElementById('alumnos-list').innerHTML = '<tr><td colspan="5">No se encontraron resultados.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error al realizar la búsqueda:', error);
                document.getElementById('alumnos-list').innerHTML = '<tr><td colspan="5">Error al buscar alumnos.</td></tr>';
            });
        } else {
            // Si no hay texto en el campo de búsqueda, no hacer nada o puedes mostrar un mensaje
            document.getElementById('alumnos-list').innerHTML = '';
        }
    });
</script>

@endsection

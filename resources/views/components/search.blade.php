<div class="container">
    <h1>Lista de Alumnos</h1>
    <p>Escribe en el campo de búsqueda para filtrar alumnos por matrícula, nombre o apellidos:</p>

    <input type="text" id="search" class="form-control mb-3" placeholder="Buscar alumnos...">

    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Matricula</th>
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
                                <td>${alumno.nombre}</td>
                                <td>${alumno.apellidopaterno}</td>
                                <td>${alumno.apellidomaterno}</td>
                                <td>
                                    <a href="{{ url('alumnos') }}/${alumno.id}/edit" class="btn btn-primary">Editar</a>
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
            document.getElementById('alumnos-list').innerHTML = '';
        }
    });
</script>

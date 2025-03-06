<div class="container">
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
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const nivel = urlParams.get('nivel'); // Captura el ID de nivel

        // Función para actualizar la lista de alumnos
        function updateAlumnosList(query = '') {
            // Si la búsqueda está vacía, muestra todos los resultados
            let url = `{{ route('alumnos.search') }}?search=${query}`;
            if (nivel) {
                url += `&nivel=${nivel}`; // Agregar nivel si está presente
            }

            fetch(url, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
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
                                <td class="d-none">${alumno.created_at}</td>
                                <td>
                                    <a href="{{ url('alumnos') }}/${alumno.id}/edit" class="btn btn-primary">Ver Alumno</a>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    resultsHTML = '<tr><td colspan="6">No se encontraron resultados.</td></tr>';
                }
                document.getElementById('alumnos-list').innerHTML = resultsHTML;
            })
            .catch(error => {
                console.error('Error al realizar la búsqueda:', error);
                document.getElementById('alumnos-list').innerHTML = '<tr><td colspan="6">Error al buscar alumnos.</td></tr>';
            });
        }

        // Escuchar el input de búsqueda
        document.getElementById('search').addEventListener('input', function() {
            let query = this.value;
            updateAlumnosList(query);
        });

        // Mostrar todos los alumnos al entrar a la vista
        updateAlumnosList('');  // Llamada con un query vacío para cargar todos los resultados

    });
</script>

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
                <th>Edicion</th>
            </tr>
        </thead>
        <tbody id="alumnos-list">
            <!-- Los alumnos se mostrarán aquí después de la búsqueda -->
        </tbody>
    </table>

    <!-- Contenedor de la paginación -->
    <div id="pagination-links" class="mt-4 d-flex justify-content-center">
        <!-- Los enlaces de paginación irán aquí -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const nivel = urlParams.get('nivel'); // Captura el ID de nivel

        // Función para actualizar la lista de alumnos
        function updateAlumnosList(query = '', page = 1) {
            let url = `{{ route('alumnos.search') }}?search=${query}&page=${page}`;
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
                if (data.data.length > 0) {
                    data.data.forEach(alumno => {
                        resultsHTML += ` 
                            <tr>
                                <td>${alumno.matricula}</td>
                                <td>${alumno.nivel_educativo ? alumno.nivel_educativo.nombre : 'Sin Nivel'}</td>
                                <td>${alumno.nombre}</td>
                                <td>${alumno.apellidopaterno}</td>
                                <td>${alumno.apellidomaterno}</td>
                                <td class="d-none">${alumno.created_at}</td>
                                <td>
                                    <a href="{{ url('alumnos') }}/${alumno.id}/edit" class="btn btn-primary">Detalles del Alumno</a>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    resultsHTML = '<tr><td colspan="6">No se encontraron resultados.</td></tr>';
                }

                document.getElementById('alumnos-list').innerHTML = resultsHTML;

                // Crear los enlaces de paginación
                let paginationHTML = '<nav><ul class="pagination">';
                
                // Página anterior
                if (data.prev_page_url) {
                    paginationHTML += `<li class="page-item"><a href="#" class="page-link" onclick="changePage(${data.current_page - 1})">&laquo; Anterior</a></li>`;
                } else {
                    paginationHTML += `<li class="page-item disabled"><a href="#" class="page-link">&laquo; Anterior</a></li>`;
                }

                // Páginas numeradas
                for (let i = 1; i <= data.last_page; i++) {
                    let activeClass = data.current_page === i ? ' active' : '';
                    paginationHTML += `<li class="page-item${activeClass}"><a href="#" class="page-link" onclick="changePage(${i})">${i}</a></li>`;
                }

                // Página siguiente
                if (data.next_page_url) {
                    paginationHTML += `<li class="page-item"><a href="#" class="page-link" onclick="changePage(${data.current_page + 1})">Siguiente &raquo;</a></li>`;
                } else {
                    paginationHTML += `<li class="page-item disabled"><a href="#" class="page-link">Siguiente &raquo;</a></li>`;
                }

                paginationHTML += '</ul></nav>';

                document.getElementById('pagination-links').innerHTML = paginationHTML;
            })
            .catch(error => {
                console.error('Error al realizar la búsqueda:', error);
                document.getElementById('alumnos-list').innerHTML = '<tr><td colspan="6">Error al buscar alumnos.</td></tr>';
            });
        }

        // Escuchar el input de búsqueda
        document.getElementById('search').addEventListener('input', function() {
            let query = this.value;
            updateAlumnosList(query, 1);  // Reiniciar la página a 1 cuando se realice una nueva búsqueda
        });

        // Función para cambiar de página
        window.changePage = function(page) {
            let query = document.getElementById('search').value;
            updateAlumnosList(query, page);
        };

        // Mostrar todos los alumnos al entrar a la vista
        updateAlumnosList('');  // Llamada con un query vacío para cargar todos los resultados
    });
</script>

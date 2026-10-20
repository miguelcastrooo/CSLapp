<div class="container">
    <p>Escribe en el campo de búsqueda para filtrar alumnos por matrícula, nombre o apellidos:</p>

    <input type="text" id="search" class="form-control mb-3" placeholder="Buscar alumnos...">

    <table class="table table-striped mt-4">
    <thead class="table-dark">
    <tr>
                <th>Matricula</th>
                <th>Nivel Educativo</th>
                <th>Alumno</th>
                <th>Grado y Seccion</th>
                <th>Ver</th>
            </tr>
        </thead>
        <tbody id="alumnos-list">
            <!-- Los alumnos se mostrarán aquí después de la búsqueda -->
        </tbody>
    </table>

    <!-- Contenedor de la paginación -->
    <div id="pagination-links" class="mt-4 d-flex justify-content-center">
    <ul class="pagination" id="pagination">
        <!-- Botón de "Anterior" -->
        <li class="page-item" id="prevBtn">
            <a class="page-link" href="#">Anterior</a>
        </li>

        <!-- Los botones de paginación se llenarán dinámicamente aquí -->
        
        <!-- Botón de "Siguiente" -->
        <li class="page-item" id="nextBtn">
            <a class="page-link" href="#">Siguiente</a>
        </li>
    </ul>
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
                                <td>${alumno.nombre} ${alumno.apellidopaterno} ${alumno.apellidomaterno}</td>
<td>${alumno.grado ? alumno.grado.nombre : 'Sin Grado'} - ${alumno.seccion ?? 'Sin Sección'}</td>
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

                // Páginas numeradas (limitar a 10 páginas visibles)
                let startPage = Math.max(1, data.current_page - 4);
                let endPage = Math.min(data.last_page, data.current_page + 5);

                if (startPage > 1) {
                    paginationHTML += `<li class="page-item"><a href="#" class="page-link" onclick="changePage(1)">1</a></li>`;
                    if (startPage > 2) paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }

                for (let i = startPage; i <= endPage; i++) {
                    let activeClass = data.current_page === i ? ' active' : '';
                    paginationHTML += `<li class="page-item${activeClass}"><a href="#" class="page-link" onclick="changePage(${i})">${i}</a></li>`;
                }

                if (endPage < data.last_page) {
                    if (endPage < data.last_page - 1) paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    paginationHTML += `<li class="page-item"><a href="#" class="page-link" onclick="changePage(${data.last_page})">${data.last_page}</a></li>`;
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

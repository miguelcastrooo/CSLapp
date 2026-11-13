<div>
    <div class="input-icon mb-4">
        <span class="input-icon-addon"><i class="fas fa-magnifying-glass"></i></span>
        <input type="text" id="search" value="{{ request('search', '') }}" class="form-control" placeholder="Buscar por matrícula, nombre o apellidos..." aria-label="Buscar alumnos">
    </div>

    <div class="row g-2 mb-4">
        <div class="col-md-6">
            <label class="form-label small" for="filter-grado">Grado</label>
            <select id="filter-grado" class="form-select">
                <option value="">Todos los grados</option>
                @foreach($grados ?? collect() as $grado)
                    <option value="{{ $grado->id }}" data-nivel="{{ $grado->nivel_educativo_id }}">{{ $grado->nivelEducativo?->nombre }} · {{ $grado->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label small" for="filter-seccion">Sección</label>
            <select id="filter-seccion" class="form-select" disabled>
                <option value="">Primero selecciona un grado</option>
                @foreach($secciones ?? collect() as $seccion)
                    <option value="{{ $seccion->nombre }}" data-grado="{{ $seccion->grado_id }}">{{ $seccion->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="table-responsive">
    <table class="table table-vcenter table-hover">
    <thead>
    <tr>
                <th>Matrícula</th>
                <th>Nivel Educativo</th>
                <th>Alumno</th>
                <th>Grado y Seccion</th>
                <th>Estado del expediente</th>
                <th>Ver</th>
            </tr>
        </thead>
        <tbody id="alumnos-list">
            <!-- Los alumnos se mostrarán aquí después de la búsqueda -->
        </tbody>
    </table>
    </div>

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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const nivel = urlParams.get('nivel'); // Captura el ID de nivel
        const initialQuery = urlParams.get('search') ?? '';
        const gradoFilter = document.getElementById('filter-grado');
        const seccionFilter = document.getElementById('filter-seccion');
        const sectionOptions = [...seccionFilter.options].slice(1);
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' })[character]);

        // Función para actualizar la lista de alumnos
        function updateAlumnosList(query = '', page = 1) {
            let url = `{{ route('alumnos.search') }}?search=${encodeURIComponent(query)}&page=${page}`;
            if (nivel) {
                url += `&nivel=${nivel}`; // Agregar nivel si está presente
            }
            if (gradoFilter.value) {
                url += `&grado=${encodeURIComponent(gradoFilter.value)}`;
            }
            if (seccionFilter.value) {
                url += `&seccion=${encodeURIComponent(seccionFilter.value)}`;
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
                        const pendientes = [];
                        if (alumno.contactos_count < 1) pendientes.push('sin contacto');
                        if (alumno.familiares_count < 1) pendientes.push('sin familiar');
                        if (alumno.alumno_plataforma_count < 1) pendientes.push('sin plataforma');
                        const estado = pendientes.length
                            ? `<span class="badge bg-yellow-lt text-dark">Pendiente: ${escapeHtml(pendientes.join(', '))}</span>`
                            : '<span class="badge bg-green-lt text-success">Completo</span>';
                        resultsHTML += ` 
                            <tr>
                                <td>${escapeHtml(alumno.matricula)}</td>
                                <td>${escapeHtml(alumno.nivel_educativo ? alumno.nivel_educativo.nombre : 'Sin nivel')}</td>
                                <td>${escapeHtml(alumno.nombre)} ${escapeHtml(alumno.apellidopaterno)} ${escapeHtml(alumno.apellidomaterno)}</td>
<td>${escapeHtml(alumno.grado ? alumno.grado.nombre : 'Sin grado')} - ${escapeHtml(alumno.seccion ?? 'Sin sección')}</td>
                                <td>${estado}</td>
                                <td class="d-none">${alumno.created_at}</td>
                                <td>
                                    <a href="{{ url('alumnos') }}/${alumno.id}/edit" class="btn btn-primary">Ver expediente</a>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    resultsHTML = '<tr><td colspan="7">No se encontraron resultados.</td></tr>';
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
                document.getElementById('alumnos-list').innerHTML = '<tr><td colspan="7">Error al buscar alumnos.</td></tr>';
            });
        }

        // Escuchar el input de búsqueda
        document.getElementById('search').addEventListener('input', function() {
            let query = this.value;
            updateAlumnosList(query, 1);  // Reiniciar la página a 1 cuando se realice una nueva búsqueda
        });

        gradoFilter.addEventListener('change', function() {
            const selectedGrade = this.value;
            seccionFilter.innerHTML = '<option value="">Todas las secciones</option>';
            sectionOptions
                .filter(option => option.dataset.grado === selectedGrade)
                .forEach(option => seccionFilter.add(option.cloneNode(true)));
            seccionFilter.disabled = !selectedGrade;
            updateAlumnosList(document.getElementById('search').value, 1);
        });

        seccionFilter.addEventListener('change', function() {
            updateAlumnosList(document.getElementById('search').value, 1);
        });

        // Función para cambiar de página
        window.changePage = function(page) {
            let query = document.getElementById('search').value;
            updateAlumnosList(query, page);
        };

        // Mostrar todos los alumnos al entrar a la vista
        updateAlumnosList(initialQuery);  // Conserva la búsqueda iniciada desde el tablero
    });
</script>

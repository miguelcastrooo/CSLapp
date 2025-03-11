@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <h1 class="mb-4">Mover alumnos</h1>

    <!-- Filtros de Grado, Sección y Nivel -->
    <div class="filters mb-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Filtros de Alumnos</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Filtro de Nivel Educativo -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nivel-filter" class="font-weight-bold"><strong>Filtrar por Nivel Educativo</strong></label>
                            <div id="nivel-filter" class="mb-3">
                                @foreach($niveles as $nivelItem)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input nivel-checkbox" id="nivel-{{ $nivelItem->id }}" value="{{ $nivelItem->id }}" onchange="filterGrados()">
                                        <label class="form-check-label" for="nivel-{{ $nivelItem->id }}">{{ $nivelItem->nombre }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Filtro de Grado -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="grado-filter" class="font-weight-bold"><strong>Filtrar por Grado</strong></label>
                            <div id="grado-filter">
                                <!-- Los grados se cargarán dinámicamente según el nivel seleccionado -->
                            </div>
                        </div>
                    </div>

                    <!-- Filtro de Sección -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="seccion-filter" class="font-weight-bold"><strong>Filtrar por Sección</strong></label>
                            <div id="seccion-filter" class="d-flex flex-wrap">
                                <div class="form-check mr-3">
                                    <input type="checkbox" class="form-check-input" id="seccion-A" value="A" onchange="filterAlumnos()">
                                    <label class="form-check-label" for="seccion-A">A</label>
                                </div>
                                <div class="form-check mr-3">
                                    <input type="checkbox" class="form-check-input" id="seccion-B" value="B" onchange="filterAlumnos()">
                                    <label class="form-check-label" for="seccion-B">B</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor de la tabla con scroll horizontal -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="text-center">
                <tr>
                    <th class="p-3 text-nowrap">ID</th>
                    <th class="p-3 text-nowrap">Matrícula</th>
                    <th class="p-3 text-nowrap">Nombre</th>
                    <th class="p-3 text-nowrap">Apellido Paterno</th>
                    <th class="p-3 text-nowrap">Apellido Materno</th>
                    <th class="p-3 text-nowrap">Nivel</th>
                    <th class="p-3 text-nowrap">Grado</th>
                    <th class="p-3 text-nowrap">Sección</th>
                    <th class="p-3 text-nowrap">Seleccionar</th>
                </tr>
            </thead>
            <tbody id="alumnos-table">
                @foreach ($alumnos as $alumno)
                    <tr class="alumno-row" data-grado="{{ $alumno->grado_id }}" data-seccion="{{ $alumno->seccion }}" data-nivel="{{ $alumno->nivel_educativo_id }}">
                        <td class="p-3">{{ $alumno->id }}</td>
                        <td class="p-3">{{ $alumno->matricula }}</td>
                        <td class="p-3">{{ $alumno->nombre }}</td>
                        <td class="p-3">{{ $alumno->apellidopaterno }}</td>
                        <td class="p-3">{{ $alumno->apellidomaterno }}</td>
                        <td class="p-3">{{ $alumno->nivelEducativo ? $alumno->nivelEducativo->nombre : 'N/A' }}</td>
                        <td class="p-3">{{ $alumno->grado ? $alumno->grado->nombre : 'N/A' }}</td>
                        <td class="p-3">{{ $alumno->seccion ?? 'N/A' }}</td>
                        <td class="p-3">
                            <input type="checkbox" class="alumno-checkbox" data-id="{{ $alumno->id }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Selección de cambios: Nivel y Grado -->
    <div class="col-md-6">
    <label for="nivel-change" class="font-weight-bold">Seleccionar una opcion para el cambio:</label>
    <select id="nivel-change" class="form-control" onchange="toggleChangeButtons()">
        <option value="">Seleccione una opcion</option>
        <option value="nivel">Nivel</option>
        <option value="grado">Grado</option>
        <option value="ambos">Nivel y Grado</option>
    </select>
</div>

<div class="col-md-6" id="nivel-select-container" style="display:none;">
    <label for="nivel" class="font-weight-bold">Seleccionar Nivel:</label>
    <select id="nivel" class="form-control">
        <option value="">Seleccione un Nivel</option>
        <!-- Los niveles se cargarán dinámicamente aquí -->
    </select>
</div>

<div class="col-md-6" id="grado-select-container" style="display:none;">
    <label for="grado-change" class="font-weight-bold">Seleccionar Grado:</label>
    <select id="grado-change" class="form-control"  onchange="toggleChangeButtons()">
        <option value="">Seleccione un Grado</option>
        <!-- Los grados se cargarán dinámicamente aquí -->
    </select>
</div>

<div class="col-md-6" id="nivel-grado-select-container" style="display:none;">
    <label for="nivel" class="font-weight-bold">Seleccionar Nivel:</label>
    <select id="nivel" class="form-control">
        <option value="">Seleccione un Nivel</option>
        <!-- Los niveles se cargarán dinámicamente aquí -->
    </select>
    <label for="grado" class="font-weight-bold">Seleccionar Grado:</label>
    <select id="grado" class="form-control">
        <option value="">Seleccione un Grado</option>
        <!-- Los grados se cargarán dinámicamente aquí -->
    </select>
</div>
<!-- Checkbox para seleccionar todos los resultados filtrados -->
<div class="mb-4">
    <input type="checkbox" id="select-all" onchange="selectAllAlumnos()"> Seleccionar todos los alumnos filtrados
</div>

<!-- Botón para mover los grupos -->
<div class="mb-4">
    <button class="btn btn-primary" id="move-group" onclick="showChangeModal()" disabled>Mover Grupo(s)</button>
</div>

<!-- Modal de Confirmación -->
<div class="modal" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea mover los alumnos seleccionados?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="moveAlumnos()">Confirmar</button>
            </div>
        </div>
    </div>
</div>


    <a href="{{ route('admin.selectadmin') }}" class="btn btn-primary mt-3">Volver</a>
</div>

<script>
    let gradosData = @json($grados);  // Asegúrate de pasar los grados desde el backend
    let alumnosData = @json($alumnos);  // Asegúrate de pasar los alumnos desde el backend
    let nivelesData = @json($niveles);  // Asegúrate de pasar los niveles desde el backend

    // Cargar los grados según el nivel seleccionado
    function filterGrados() {
        let selectedNiveles = [];
        document.querySelectorAll('.nivel-checkbox:checked').forEach(function (checkbox) {
            selectedNiveles.push(checkbox.value);
        });

        // Filtrar los grados según los niveles seleccionados
        let gradosFiltered = gradosData.filter(grado => selectedNiveles.includes(grado.nivel_educativo_id.toString()));

        // Limpiar y cargar los grados filtrados
        let gradoFilterDiv = document.getElementById('grado-filter');
        gradoFilterDiv.innerHTML = '';  // Limpiar los grados previos

        gradosFiltered.forEach(function (grado) {
            let gradoDiv = document.createElement('div');
            gradoDiv.classList.add('form-check');
            gradoDiv.innerHTML = ` 
                <input type="checkbox" class="form-check-input grado-checkbox" id="grado-${grado.id}" value="${grado.id}" onchange="filterAlumnos()">
                <label class="form-check-label" for="grado-${grado.id}">${grado.nombre}</label>
            `;
            gradoFilterDiv.appendChild(gradoDiv);
        });

        filterAlumnos();  // Volver a filtrar los alumnos después de actualizar los grados
    }

    // Filtrar los alumnos según los filtros de nivel, grado y sección
    function filterAlumnos() {
        let selectedGrados = [];
        document.querySelectorAll('.grado-checkbox:checked').forEach(function (checkbox) {
            selectedGrados.push(checkbox.value);
        });

        let selectedSecciones = [];
        document.querySelectorAll('#seccion-filter .form-check-input:checked').forEach(function (checkbox) {
            selectedSecciones.push(checkbox.value);
        });

        // Filtrar los alumnos según los criterios seleccionados
        document.querySelectorAll('.alumno-row').forEach(function (row) {
            let rowGrado = row.getAttribute('data-grado');
            let rowSeccion = row.getAttribute('data-seccion');
            let rowNivel = row.getAttribute('data-nivel');

            let showRow = (selectedGrados.length === 0 || selectedGrados.includes(rowGrado)) &&
                          (selectedSecciones.length === 0 || selectedSecciones.includes(rowSeccion));

            if (showRow) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        toggleButtons(); // Verificar si habilitar los botones
    }

    // Función para seleccionar todos los alumnos filtrados
    function selectAllAlumnos() {
        let selectAllCheckbox = document.getElementById('select-all');
        let checkboxes = document.querySelectorAll('.alumno-checkbox');
        
        checkboxes.forEach(function (checkbox) {
            if (checkbox.closest('tr').style.display !== 'none') { // Solo seleccionar los visibles
                checkbox.checked = selectAllCheckbox.checked;
            }
        });

        toggleButtons(); // Verificar si habilitar los botones
    }

    // Función para habilitar los botones de cambiar nivel y grado
function toggleChangeButtons() {
    let cambioSeleccionado = document.getElementById('nivel-change').value;
    let nivelSelectContainer = document.getElementById('nivel-select-container');
    let gradoSelectContainer = document.getElementById('grado-select-container');
    let nivelGradoSelectContainer = document.getElementById('nivel-grado-select-container');

    // Ocultar todos los select por defecto
    nivelSelectContainer.style.display = 'none';
    gradoSelectContainer.style.display = 'none';
    nivelGradoSelectContainer.style.display = 'none';

    // Habilitar o deshabilitar según la selección
    if (cambioSeleccionado === "nivel") {
        nivelSelectContainer.style.display = 'block';  // Mostrar solo el select de nivel
        gradoSelectContainer.style.display = 'none';
        nivelGradoSelectContainer.style.display = 'none';

        loadNiveles();  // Esta función debe cargar los niveles que necesitas
    } else if (cambioSeleccionado === "grado") {
        nivelSelectContainer.style.display = 'none';
        gradoSelectContainer.style.display = 'block';  // Mostrar solo el select de grado
        nivelGradoSelectContainer.style.display = 'none';

        loadGrados();  // Esta función debe cargar los grados que necesitas
    } else if (cambioSeleccionado === "ambos") {
        nivelSelectContainer.style.display = 'block';  // Mostrar ambos selectores
        gradoSelectContainer.style.display = 'block';
        nivelGradoSelectContainer.style.display = 'none';

        loadNiveles();  // Cargar los niveles
        loadGrados();  // Cargar los grados
    }
    toggleButtons(); // Verificar si habilitar los botones
}

// Función para cargar los niveles
function loadNiveles() {
    let nivelSelect = document.getElementById('nivel');
    nivelSelect.innerHTML = '<option value="">Seleccione un Nivel</option>';

    if (!nivelesData || nivelesData.length === 0) {
        console.log('No hay datos de niveles disponibles.');
        nivelSelect.innerHTML += '<option value="">No hay niveles disponibles</option>';
    } else {
        nivelesData.forEach(function(nivel) {
            console.log('Cargando nivel:', nivel);
            nivelSelect.innerHTML += `<option value="${nivel.id}">${nivel.nombre}</option>`;
        });

        // Agregar evento para actualizar grados cuando cambia nivel
        nivelSelect.addEventListener('change', loadGrados);
    }
}


// Función para cargar los grados
function loadGrados() {
    let gradoSelect = document.getElementById('grado-change'); // Asegura que es el select correcto
    let selectedNivel = document.getElementById('nivel').value; // Obtener nivel seleccionado
    
    // Limpiar las opciones previas
    gradoSelect.innerHTML = '<option value="">Seleccione un Grado</option>';

    // Si no hay datos de grados
    if (!gradosData || gradosData.length === 0) {
        console.log('No hay datos de grados disponibles.');
        gradoSelect.innerHTML += '<option value="">No hay grados disponibles</option>';
    } else {
        // Filtrar grados según el nivel seleccionado
        let gradosFiltrados = gradosData.filter(grado => grado.nivel_educativo_id == selectedNivel);
        
        // Si no hay grados para el nivel seleccionado
        if (gradosFiltrados.length === 0) {
            gradoSelect.innerHTML += '<option value="">No hay grados para este nivel</option>';
        } else {
            // Si hay grados, mostrarlos en el select
            gradosFiltrados.forEach(function(grado) {
                console.log('Cargando grado:', grado);
                gradoSelect.innerHTML += `<option value="${grado.id}">${grado.nombre}</option>`;
            });
        }
    }
}


// Verifica si el botón de cambio se puede habilitar
function toggleButtons() {
    let cambioSeleccionado = document.getElementById('nivel-change').value;
    let cambioNivel = document.getElementById('nivel');
    let cambioGrado = document.getElementById('grado');

    if ((cambioSeleccionado === 'nivel' && cambioNivel.value !== '') ||
        (cambioSeleccionado === 'grado' && cambioGrado.value !== '') ||
        (cambioSeleccionado === 'ambos' && cambioNivel.value !== '' && cambioGrado.value !== '')) {
        document.getElementById('cambiar-btn').disabled = false;
    } else {
        document.getElementById('cambiar-btn').disabled = true;
    }
}


    // Función para habilitar el botón "Mover Grupo(s)" cuando se seleccionan alumnos
    function toggleButtons() {
        let anyAlumnoSelected = false;
        
        // Verificar si hay algún alumno seleccionado
        document.querySelectorAll('.alumno-checkbox').forEach(function (checkbox) {
            if (checkbox.checked) {
                anyAlumnoSelected = true;
            }
        });
        
        let moveGroupButton = document.getElementById('move-group');
        
        // Habilitar o deshabilitar el botón dependiendo si hay alumnos seleccionados
        if (anyAlumnoSelected) {
            moveGroupButton.disabled = false;
        } else {
            moveGroupButton.disabled = true;
        }
    }

    // Función para mostrar el modal de confirmación
    function showChangeModal() {
        $('#confirmModal').modal('show');
    }

    // Función para mover los alumnos seleccionados
    function moveAlumnos() {
        let selectedAlumnos = [];
        
        // Obtener los alumnos seleccionados
        document.querySelectorAll('.alumno-checkbox:checked').forEach(function (checkbox) {
            selectedAlumnos.push(checkbox.getAttribute('data-id'));
        });

        // Asegurarse de que al menos un alumno haya sido seleccionado
        if (selectedAlumnos.length > 0) {
            // Enviar la solicitud AJAX para mover los alumnos al nuevo grado
            $.ajax({
                url: '/admin/mover-grupos',  // Ruta para mover los grupos
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    grado_id: $('#grado').val(),
                    nivel_educativo_id: $('#nivel').val(),
                    alumnos: selectedAlumnos  // Alumnos seleccionados
                },
                success: function(response) {
                    alert('Alumnos movidos: ' + selectedAlumnos.join(', '));
                    $('#confirmModal').modal('hide');
                    // Aquí puedes actualizar la lista de alumnos o realizar otra acción
                },
                error: function() {
                    alert('Error al mover los alumnos.');
                }
            });
        }
    }
</script>


@endsection

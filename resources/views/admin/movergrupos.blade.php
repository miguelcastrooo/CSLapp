@extends('layouts.app')

@section('content')

 @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

    <center>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Checkbox para seleccionar todos los resultados filtrados -->
                <div class="mb-4">
                    <input type="checkbox" id="select-all" onchange="selectAllAlumnos()"> Seleccionar todos los alumnos filtrados
                </div>

                <!-- Contador de alumnos seleccionados -->
                <div class="mb-4">
                    <strong>Total seleccionados:</strong> <span id="total-seleccionados">0</span>
                </div>

                <div class="mb-4">
                    <button class="btn btn-success" id="promover-group" onclick="promover()">Promover</button>
                </div>
            </div>
        </div>
    </div><br>
</center>

    <!-- Contenedor de la tabla con scroll horizontal -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="text-center bg-dark">
                <tr>
                    <th class="p-3 text-nowrap">ID</th>
                    <th class="p-3 text-nowrap">Matrícula</th>
                    <th class="p-3 text-nowrap">Alumno</th>
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
                        <td class="p-3">{{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</td>
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



    <!-- Modal de Confirmación Mejorado -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Promoción</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                <!-- Aquí se inyecta el número de alumnos seleccionados -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmPromoteBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

    <a href="{{ route('admin.selectadmin') }}" class="btn btn-primary mt-3">Volver</a>
</div>
<style>
    /* Estilos personalizados para la tabla */
.table th {
    background-color: #343a40 !important; /* Fondo oscuro en el encabezado */
    color: white; /* Texto blanco en el encabezado */
    text-align: center; /* Centrado de texto en el encabezado */
}

.table td, .table th {
    padding: 1rem; /* Añadir más espacio para una mejor legibilidad */
}

.table-hover tbody tr:hover {
    background-color: #f1f1f1; /* Color de fondo para cuando el ratón pasa sobre las filas */
}

.table-bordered th, .table-bordered td {
    border: 1px solid #dee2e6; /* Bordes suaves y claros */
}

/* Estilo para la tabla de alumnos */
.table {
    width: 100%; /* Asegura que la tabla ocupe todo el espacio disponible */
    border-collapse: collapse; /* Asegura que no haya doble borde */
}

/* Estilos adicionales para las filas al pasar el ratón */
.table tbody tr:hover {
    background-color: #e9ecef; /* Color más claro cuando se pasa el ratón sobre una fila */
}

.table .alumno-checkbox {
    cursor: pointer; /* Cambiar el cursor al pasar por encima del checkbox */
}

/* Estilo para las celdas del encabezado de la tabla */
.table th {
    background-color:rgb(0, 0, 0) !important; /* Fondo oscuro en el encabezado */
    color: white; /* Texto blanco en el encabezado */
    text-align: center; /* Centrado de texto en el encabezado */
}

.table td {
    text-align: center; /* Centrar los textos en las celdas */
}

    </style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    const gradosData = @json($grados);

    function filterGrados() {
        const selectedNiveles = Array.from(document.querySelectorAll('.nivel-checkbox:checked'))
            .map(cb => cb.value);

        const gradosFiltrados = gradosData.filter(grado =>
            selectedNiveles.includes(grado.nivel_educativo_id.toString())
        );

        const gradoFilterDiv = document.getElementById('grado-filter');
        gradoFilterDiv.innerHTML = '';

        gradosFiltrados.forEach(grado => {
            gradoFilterDiv.innerHTML += `
                <div class="form-check">
                    <input type="checkbox" class="form-check-input grado-checkbox" id="grado-${grado.id}" value="${grado.id}" onchange="filterAlumnos()">
                    <label class="form-check-label" for="grado-${grado.id}">${grado.nombre}</label>
                </div>`;
        });

        filterAlumnos();
    }

    function filterAlumnos() {
        const selectedNiveles = Array.from(document.querySelectorAll('.nivel-checkbox:checked')).map(cb => cb.value);
        const selectedGrados = Array.from(document.querySelectorAll('.grado-checkbox:checked')).map(cb => cb.value);
        const selectedSecciones = Array.from(document.querySelectorAll('#seccion-filter .form-check-input:checked')).map(cb => cb.value);

        document.querySelectorAll('.alumno-row').forEach(row => {
            const rowNivel = row.dataset.nivel;
            const rowGrado = row.dataset.grado;
            const rowSeccion = row.dataset.seccion;

            const nivelMatch = selectedNiveles.length === 0 || selectedNiveles.includes(rowNivel);
            const gradoMatch = selectedGrados.length === 0 || selectedGrados.includes(rowGrado);
            const seccionMatch = selectedSecciones.length === 0 || selectedSecciones.includes(rowSeccion);

            row.style.display = (nivelMatch && gradoMatch && seccionMatch) ? '' : 'none';
        });

        toggleButtons();
    }

    function selectAllAlumnos() {
        const selectAll = document.getElementById('select-all').checked;
        document.querySelectorAll('.alumno-row').forEach(row => {
            if (row.style.display !== 'none') {
                row.querySelector('.alumno-checkbox').checked = selectAll;
            }
        });

        toggleButtons();
    }

    function toggleButtons() {
        const promoteBtn = document.getElementById('promover-group');
        const checkboxes = Array.from(document.querySelectorAll('.alumno-checkbox'));
        const anyChecked = checkboxes.some(cb => cb.checked && cb.closest('tr').style.display !== 'none');
        promoteBtn.disabled = !anyChecked;

        actualizarContadorSeleccionados();
    }

    function actualizarContadorSeleccionados() {
        const count = Array.from(document.querySelectorAll('.alumno-checkbox'))
            .filter(cb => cb.checked && cb.closest('tr').style.display !== 'none')
            .length;
        document.getElementById('total-seleccionados').textContent = count;
    }

    function promover() {
        const selectedIds = Array.from(document.querySelectorAll('.alumno-checkbox'))
            .filter(cb => cb.checked && cb.closest('tr').style.display !== 'none')
            .map(cb => cb.dataset.id);

        if (selectedIds.length === 0) {
            alert('No hay alumnos seleccionados para promover.');
            return;
        }

        document.getElementById('confirmModalBody').innerText = `¿Está seguro de promover a ${selectedIds.length} alumno(s)?`;
        document.getElementById('confirmPromoteBtn').dataset.ids = selectedIds.join(',');

        $('#confirmModal').modal('show');
    }

    document.getElementById('confirmPromoteBtn').addEventListener('click', function () {
        const ids = this.dataset.ids.split(',');

        fetch("{{ route('admin.promover') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ alumnos_ids: ids })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Respuesta del servidor no fue OK');
            }
            return response.json();
        })
        .then(data => {
            $('#confirmModal').modal('hide');
            if (data.success) {
                alert(data.message || 'Alumnos promovidos con éxito.');
                location.reload();
            } else {
                alert(data.message || 'Ocurrió un error al promover.');
            }
        })
        .catch(error => {
            console.error("Error al promover:", error);
            alert("Ocurrió un error inesperado al promover los alumnos.");
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.alumno-checkbox').forEach(cb => {
            cb.addEventListener('change', toggleButtons);
        });

        actualizarContadorSeleccionados(); // Inicializa en 0 al cargar
    });
      document.addEventListener("DOMContentLoaded", function () {
        const alert = document.getElementById('success-alert');
        if (alert) {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 4000); // 4000 milisegundos = 4 segundos
        }
    });
</script>
@endsection

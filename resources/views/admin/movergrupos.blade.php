@extends('layouts.app')

@section('title', 'Mover alumnos | Instituto Horizonte')

@section('content')
<div class="container-xl py-4">
    <div class="csl-module-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h1>Promover alumnos</h1>
            <p>Filtra, selecciona y mueve al siguiente grado a los alumnos elegidos.</p>
        </div>
        <a href="{{ route('admin.selectadmin') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Volver</a>
    </div>

    <div class="csl-process-guide mb-4">
        <div class="csl-process-intro"><i class="fas fa-circle-info"></i><div><strong>¿Cómo promover un grupo?</strong><span>Realiza estos pasos en orden. El sistema solo permitirá mover un grupo completo.</span></div></div>
        <div class="csl-process-steps">
            <div class="csl-process-step"><span>1</span><div><strong>Filtra el grupo</strong><small>Elige nivel, grado y sección.</small></div></div>
            <div class="csl-process-step"><span>2</span><div><strong>Selecciona a todos</strong><small>Usa “Seleccionar todos” para incluir al grupo completo.</small></div></div>
            <div class="csl-process-step"><span>3</span><div><strong>Revisa y confirma</strong><small>Verifica el grupo antes de promoverlo.</small></div></div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger"><div class="d-flex gap-2"><i class="fas fa-circle-exclamation mt-1"></i><div><strong>Revisa la información.</strong><ul class="mb-0 mt-1 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div></div>
    @endif

    <div class="card csl-data-card mb-4">
        <div class="card-header py-3"><h2 class="card-title mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filtrar alumnos</h2></div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label">Nivel educativo</label>
                    <div id="nivel-filter">
                        @foreach($niveles as $nivelItem)
                            <div class="csl-filter-option"><input type="checkbox" class="form-check-input nivel-checkbox" id="nivel-{{ $nivelItem->id }}" value="{{ $nivelItem->id }}" onchange="filterGrados()"><label class="form-check-label" for="nivel-{{ $nivelItem->id }}">{{ $nivelItem->nombre }}</label></div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Grado</label>
                    <div id="grado-filter"><span class="text-secondary small">Selecciona primero un nivel educativo.</span></div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sección</label>
                    <div id="seccion-filter" class="d-flex flex-wrap gap-2"><span class="text-secondary small">Selecciona primero un grado.</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card csl-data-card">
        <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap py-3">
            <div><h2 class="card-title mb-1"><i class="fas fa-users me-2 text-primary"></i>Alumnos disponibles</h2><span class="text-secondary">Revisa el grupo y marca a todos sus integrantes.</span></div>
            <label class="form-check mb-0 fw-semibold"><input type="checkbox" class="form-check-input me-2" id="select-all" onchange="selectAllAlumnos()">Seleccionar todos los alumnos visibles</label>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-hover csl-promote-table mb-0">
                <thead><tr><th>Alumno</th><th>Matrícula</th><th>Nivel</th><th>Grado</th><th>Sección</th><th class="text-center">Seleccionar</th></tr></thead>
                <tbody id="alumnos-table">
                    @forelse ($alumnos as $alumno)
                        <tr class="alumno-row" data-grado="{{ $alumno->grado_id }}" data-seccion="{{ $alumno->seccion }}" data-nivel="{{ $alumno->nivel_educativo_id }}">
                            <td class="fw-bold">{{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</td>
                            <td>{{ $alumno->matricula }}</td>
                            <td>{{ $alumno->nivelEducativo?->nombre ?? 'Sin nivel' }}</td>
                            <td>{{ $alumno->grado?->nombre ?? 'Sin grado' }}</td>
                            <td>{{ $alumno->seccion ?? 'Sin sección' }}</td>
                            <td class="text-center"><input type="checkbox" class="form-check-input alumno-checkbox" data-id="{{ $alumno->id }}" aria-label="Seleccionar a {{ $alumno->nombre }}"></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary py-5">No hay alumnos para mostrar con tu acceso actual.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body p-4 border-top">
            <div class="csl-selection-panel">
                <div><strong class="d-block text-dark">¿Ya revisaste el grupo?</strong><span class="text-secondary small">Solo podrás continuar cuando estén seleccionados todos sus alumnos.</span></div>
                <div class="csl-selection-count"><span id="total-seleccionados">0</span> alumno(s) seleccionado(s)</div>
                <button class="btn btn-primary" id="promover-group" type="button" onclick="promover()" disabled><i class="fas fa-arrow-up-right-dots me-2"></i>Revisar y promover</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h2 class="modal-title h3" id="confirmModalLabel">Confirmar promoción</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div><div class="modal-body" id="confirmModalBody"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="confirmPromoteBtn">Confirmar promoción</button></div></div></div>
</div>
@endsection

@section('scripts')
<script>
    const gradosData = @json($grados);
    const seccionesData = @json($secciones);
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

    function filterGrados() {
        const selectedNiveles = Array.from(document.querySelectorAll('.nivel-checkbox:checked')).map((checkbox) => checkbox.value);
        const gradoFilterDiv = document.getElementById('grado-filter');
        const gradosFiltrados = gradosData.filter((grado) => selectedNiveles.includes(grado.nivel_educativo_id.toString()));
        gradoFilterDiv.innerHTML = gradosFiltrados.length ? '' : '<span class="text-secondary small">Selecciona primero un nivel educativo.</span>';
        gradosFiltrados.forEach((grado) => {
            gradoFilterDiv.insertAdjacentHTML('beforeend', `<div class="csl-filter-option"><input type="checkbox" class="form-check-input grado-checkbox" id="grado-${grado.id}" value="${grado.id}" onchange="filterAlumnos()"><label class="form-check-label" for="grado-${grado.id}">${grado.nombre}</label></div>`);
        });
        actualizarSecciones();
        filterAlumnos();
    }

    function actualizarSecciones() {
        const gradosSeleccionados = Array.from(document.querySelectorAll('.grado-checkbox:checked')).map((checkbox) => checkbox.value);
        const seccionFilterDiv = document.getElementById('seccion-filter');
        const seccionesFiltradas = seccionesData.filter((seccion) => gradosSeleccionados.includes(seccion.grado_id.toString()));

        seccionFilterDiv.innerHTML = seccionesFiltradas.length ? '' : '<span class="text-secondary small">Selecciona primero un grado.</span>';
        seccionesFiltradas.forEach((seccion) => {
            seccionFilterDiv.insertAdjacentHTML('beforeend', `<div class="csl-filter-option mb-0"><input type="checkbox" class="form-check-input seccion-checkbox" id="seccion-${seccion.id}" value="${seccion.nombre}" onchange="filterAlumnos()"><label class="form-check-label" for="seccion-${seccion.id}">Sección ${seccion.nombre}</label></div>`);
        });
    }

    function filterAlumnos() {
        const niveles = Array.from(document.querySelectorAll('.nivel-checkbox:checked')).map((checkbox) => checkbox.value);
        const grados = Array.from(document.querySelectorAll('.grado-checkbox:checked')).map((checkbox) => checkbox.value);
        const secciones = Array.from(document.querySelectorAll('.seccion-checkbox:checked')).map((checkbox) => checkbox.value);
        document.querySelectorAll('.alumno-row').forEach((row) => {
            const visible = (!niveles.length || niveles.includes(row.dataset.nivel)) && (!grados.length || grados.includes(row.dataset.grado)) && (!secciones.length || secciones.includes(row.dataset.seccion));
            row.style.display = visible ? '' : 'none';
        });
        document.getElementById('select-all').checked = false;
        toggleButtons();
    }

    function selectAllAlumnos() {
        const seleccionar = document.getElementById('select-all').checked;
        document.querySelectorAll('.alumno-row').forEach((row) => {
            if (row.style.display !== 'none') row.querySelector('.alumno-checkbox').checked = seleccionar;
        });
        toggleButtons();
    }

    function toggleButtons() {
        const seleccionados = Array.from(document.querySelectorAll('.alumno-checkbox')).filter((checkbox) => checkbox.checked && checkbox.closest('tr').style.display !== 'none');
        document.getElementById('promover-group').disabled = seleccionados.length === 0;
        document.getElementById('total-seleccionados').textContent = seleccionados.length;
    }

    function promover() {
        const ids = Array.from(document.querySelectorAll('.alumno-checkbox')).filter((checkbox) => checkbox.checked && checkbox.closest('tr').style.display !== 'none').map((checkbox) => checkbox.dataset.id);
        if (!ids.length) return;
        const primerAlumno = document.querySelector(`.alumno-checkbox[data-id="${ids[0]}"]`).closest('tr');
        const nivel = primerAlumno.cells[2].textContent.trim();
        const grado = primerAlumno.cells[3].textContent.trim();
        const seccion = primerAlumno.cells[4].textContent.trim();
        document.getElementById('confirmModalBody').innerHTML = `<p>Vas a procesar al grupo completo:</p><div class="alert alert-info mb-0"><strong>${nivel} · ${grado} · Sección ${seccion}</strong><br>${ids.length} alumno(s) pasarán al siguiente grado. Si concluyeron el último grado, quedarán registrados como egresados.</div>`;
        document.getElementById('confirmPromoteBtn').dataset.ids = ids.join(',');
        confirmModal.show();
    }

    document.getElementById('confirmPromoteBtn').addEventListener('click', function () {
        fetch("{{ route('admin.promover') }}", { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ alumnos_ids: this.dataset.ids.split(',') }) })
            .then((response) => response.json().then((data) => ({ ok: response.ok, data })))
            .then(({ ok, data }) => { if (!ok || !data.success) throw new Error(data.message || 'No fue posible promover a los alumnos.'); confirmModal.hide(); window.alert(data.message); window.location.reload(); })
            .catch((error) => window.alert(error.message));
    });

    document.querySelectorAll('.alumno-checkbox').forEach((checkbox) => checkbox.addEventListener('change', toggleButtons));
    document.getElementById('grado-filter').addEventListener('change', actualizarSecciones);
</script>
@endsection

@extends('layouts.app')

@section('title', 'Importar y exportar alumnos')

@section('content')
    <div class="csl-module-header"><h1>Importar y exportar alumnos</h1><p>Trabaja con Excel sin alterar automáticamente nombres ni apellidos.</p></div>

    <x-task-guide title="Importación segura en tres pasos" description="La importación sólo se guarda después de que revises la vista previa." :steps="['Descarga la plantilla y conserva las columnas separadas de nombres y apellidos.', 'Carga el archivo .xlsx y revisa los alumnos válidos y los errores.', 'Corrige el Excel si hace falta y confirma únicamente cuando no haya errores.']" />

    <div class="card csl-data-card mb-4">
        <div class="card-header"><div><h2 class="card-title mb-1">Exportar alumnos</h2><div class="text-secondary">Deja los filtros vacíos para descargar todos los alumnos disponibles.</div></div></div>
        <div class="card-body">
            <form method="GET" action="{{ route('alumnos.excel.exportar') }}" class="row g-3 align-items-end" id="export-form">
                <div class="col-md-3"><label class="form-label" for="export-nivel">Nivel educativo</label><select class="form-select" name="nivel_id" id="export-nivel"><option value="">Todos los niveles</option>@foreach($niveles as $nivel)<option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>@endforeach</select></div>
                <div class="col-md-3"><label class="form-label" for="export-grado">Grado</label><select class="form-select" name="grado_id" id="export-grado"><option value="">Todos los grados</option>@foreach($grados as $grado)<option value="{{ $grado->id }}" data-nivel="{{ $grado->nivel_educativo_id }}">{{ $grado->nombre }}</option>@endforeach</select></div>
                <div class="col-md-2"><label class="form-label" for="export-seccion">Sección</label><select class="form-select" name="seccion" id="export-seccion"><option value="">Todas</option>@foreach($secciones as $seccion)<option value="{{ $seccion->nombre }}" data-grado="{{ $seccion->grado_id }}">{{ $seccion->nombre }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label" for="export-alumno">Alumno específico</label><select class="form-select" name="alumno_id" id="export-alumno"><option value="">Todos los alumnos</option>@foreach($alumnos as $alumno)<option value="{{ $alumno->id }}" data-nivel="{{ $alumno->nivel_educativo_id }}" data-grado="{{ $alumno->grado_id }}" data-seccion="{{ $alumno->seccion }}">{{ $alumno->matricula }} · {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}, {{ $alumno->nombre }}</option>@endforeach</select></div>
                <div class="col-12"><button class="btn btn-outline-primary" type="submit"><i class="fas fa-file-excel me-2"></i>Exportar selección</button></div>
            </form>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-lg-7">
            <div class="card csl-data-card">
                <div class="card-header"><div><h2 class="card-title mb-1">Cargar lista de alumnos</h2><div class="text-secondary">Puedes revisar hasta 500 filas antes de confirmar la importación.</div></div></div>
                <div class="card-body">
                    <div class="alert alert-info"><i class="fas fa-circle-info me-2"></i>Para conservar correctamente apellidos como <strong>De la Cruz</strong> o <strong>Del Valle</strong>, escríbelos completos en su propia columna. Al cargar el archivo podrás revisar todo antes de guardar.</div>
                    <form method="POST" action="{{ route('alumnos.excel.previsualizar') }}" enctype="multipart/form-data">
                        @csrf
                        <label for="archivo" class="form-label fw-semibold">Archivo de Excel</label>
                        <input id="archivo" name="archivo" type="file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" class="form-control @error('archivo') is-invalid @enderror" required>
                        @error('archivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="d-flex flex-wrap gap-2 mt-3"><button class="btn btn-primary" type="submit"><i class="fas fa-eye me-2"></i>Revisar archivo</button><a href="{{ route('alumnos.excel.plantilla') }}" class="btn btn-outline-secondary"><i class="fas fa-file-arrow-down me-2"></i>Descargar plantilla</a></div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card csl-data-card h-100"><div class="card-body"><h2 class="h3">Qué incluye la plantilla</h2><p class="text-secondary">Sólo los datos escolares esenciales para dar de alta y localizar al alumno. El resto se completa desde el expediente.</p><ul class="mb-0"><li>Matrícula, nombres y apellidos</li><li>Nivel, grado y sección existentes</li><li>Fechas con formato <code>AAAA-MM-DD</code></li><li>Historial de alta y estado pendiente de completar</li></ul></div></div>
        </div>
    </div>

    @if($preview)
        <div class="card csl-data-card mt-4">
            <div class="card-header"><div><h2 class="card-title mb-1">Resultado de la revisión</h2><div class="text-secondary">{{ count($preview['validos']) }} alumno(s) listo(s) y {{ count($preview['errores']) }} fila(s) con errores.</div></div></div>
            <div class="card-body">
                @if($preview['errores'])
                    <div class="alert alert-warning"><strong>Encontramos algunos datos por revisar.</strong> Corrige las filas indicadas y vuelve a cargar el archivo; cuando esté listo podrás confirmar la importación.</div>
                    <div class="table-responsive"><table class="table table-vcenter"><thead><tr><th>Fila</th><th>Alumno</th><th>Detalles a corregir</th></tr></thead><tbody>@foreach($preview['errores'] as $error)<tr><td>{{ $error['linea'] }}</td><td>{{ $error['nombre'] ?: 'Sin nombre' }}</td><td><ul class="mb-0 ps-3">@foreach($error['mensajes'] as $mensaje)<li>{{ $mensaje }}</li>@endforeach</ul></td></tr>@endforeach</tbody></table></div>
                @elseif($preview['validos'])
                    <div class="alert alert-success"><i class="fas fa-circle-check me-2"></i>Todo está listo. Al confirmar se crearán los expedientes y su historial.</div>
                    <div class="table-responsive"><table class="table table-vcenter"><thead><tr><th>Fila</th><th>Matrícula</th><th>Alumno</th></tr></thead><tbody>@foreach(array_slice($preview['validos'], 0, 15) as $fila)<tr><td>{{ $fila['linea'] }}</td><td>{{ $fila['data']['matricula'] }}</td><td>{{ $fila['data']['alumno']['nombre'] }} {{ $fila['data']['alumno']['apellidopaterno'] }} {{ $fila['data']['alumno']['apellidomaterno'] }}</td></tr>@endforeach</tbody></table></div>
                    @if(count($preview['validos']) > 15)<p class="text-secondary small">Se muestran las primeras 15 filas de {{ count($preview['validos']) }}.</p>@endif
                    <form method="POST" action="{{ route('alumnos.excel.confirmar') }}" data-confirm="Se crearán los expedientes mostrados y quedarán pendientes de completar desde su expediente." data-confirm-title="Confirmar importación" data-confirm-button="Sí, importar" data-confirm-variant="success">@csrf<button class="btn btn-success" type="submit"><i class="fas fa-file-import me-2"></i>Confirmar importación</button></form>
                @else
                    <div class="alert alert-warning mb-0">El archivo no contiene filas con alumnos.</div>
                @endif
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const nivel = document.getElementById('export-nivel');
    const grado = document.getElementById('export-grado');
    const seccion = document.getElementById('export-seccion');
    const alumno = document.getElementById('export-alumno');
    const filter = () => {
        [...grado.options].forEach(option => option.hidden = !!option.dataset.nivel && option.dataset.nivel !== nivel.value);
        if (grado.selectedOptions[0]?.hidden) grado.value = '';
        [...seccion.options].forEach(option => option.hidden = !!option.dataset.grado && option.dataset.grado !== grado.value);
        if (seccion.selectedOptions[0]?.hidden) seccion.value = '';
        [...alumno.options].forEach(option => option.hidden = !!option.dataset.nivel && ((nivel.value && option.dataset.nivel !== nivel.value) || (grado.value && option.dataset.grado !== grado.value) || (seccion.value && option.dataset.seccion !== seccion.value)));
        if (alumno.selectedOptions[0]?.hidden) alumno.value = '';
    };
    [nivel, grado, seccion].forEach(element => element.addEventListener('change', filter));
    alumno.addEventListener('change', () => {
        const option = alumno.selectedOptions[0];
        if (!option?.value) return;
        nivel.value = option.dataset.nivel; grado.value = option.dataset.grado; seccion.value = option.dataset.seccion || ''; filter();
    });
    filter();
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'Bitácora de familias | Instituto Horizonte')

@section('content')
<div class="csl-module-header">
    <h1>Bitácora de familias</h1>
    <p>Consulta en un solo lugar las llamadas, correos, reuniones y acuerdos registrados en los expedientes.</p>
</div>

<x-task-guide
    title="Cómo consultar el seguimiento"
    description="Usa los filtros para localizar una comunicación y abre el expediente sólo cuando necesites revisar sus datos completos."
    :steps="['Busca por nombre, apellido o matrícula.', 'Filtra por nivel, medio, responsable o fecha si lo necesitas.', 'Abre el expediente del alumno para continuar el seguimiento.']"
/>

<div class="card csl-data-card">
    <div class="card-header">
        <form id="bitacora-filtros" method="GET" class="row g-2 align-items-end w-100">
            <div class="col-md-3"><label class="form-label small" for="bitacora-buscar">Alumno</label><input id="bitacora-buscar" name="buscar" class="form-control" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Nombre o matrícula" autocomplete="off"><small class="text-secondary">Escribe para buscar automáticamente.</small></div>
            <div class="col-md-2"><label class="form-label small" for="bitacora-nivel">Nivel</label><select id="bitacora-nivel" name="nivel_id" class="form-select"><option value="">Todos</option>@foreach($niveles as $nivel)<option value="{{ $nivel->id }}" @selected((string) ($filtros['nivel_id'] ?? '') === (string) $nivel->id)>{{ $nivel->nombre }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label small" for="bitacora-tipo">Medio</label><select id="bitacora-tipo" name="tipo" class="form-select"><option value="">Todos</option>@foreach(['llamada' => 'Llamada', 'correo' => 'Correo', 'reunion' => 'Reunión', 'mensaje' => 'Mensaje', 'otro' => 'Otro'] as $valor => $etiqueta)<option value="{{ $valor }}" @selected(($filtros['tipo'] ?? '') === $valor)>{{ $etiqueta }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label small" for="bitacora-usuario">Registró</label><select id="bitacora-usuario" name="usuario_id" class="form-select"><option value="">Todos</option>@foreach($usuariosBitacora as $usuario)<option value="{{ $usuario->id }}" @selected((string) ($filtros['usuario_id'] ?? '') === (string) $usuario->id)>{{ $usuario->name }}</option>@endforeach</select></div>
            <div class="col-md-1"><label class="form-label small" for="bitacora-desde">Desde</label><input id="bitacora-desde" type="date" name="desde" class="form-control" value="{{ $filtros['desde'] ?? '' }}"></div>
            <div class="col-md-1"><label class="form-label small" for="bitacora-hasta">Hasta</label><input id="bitacora-hasta" type="date" name="hasta" class="form-control" value="{{ $filtros['hasta'] ?? '' }}"></div>
            <div class="col-md-1 d-flex"><a href="{{ route('admin.bitacoraFamilias') }}" class="btn btn-outline-secondary" aria-label="Limpiar filtros" title="Limpiar filtros"><i class="fas fa-rotate-left"></i></a></div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover mb-0">
            <thead><tr><th>Fecha</th><th>Alumno</th><th>Nivel y grupo</th><th>Medio</th><th>Resumen</th><th>Registró</th><th></th></tr></thead>
            <tbody id="bitacora-resultados">
                @forelse($bitacoras as $registro)
                    @php($alumno = $registro->alumno)
                    <tr>
                        <td class="text-nowrap">{{ $registro->fecha_contacto->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $alumno?->nombre }} {{ $alumno?->apellidopaterno }} {{ $alumno?->apellidomaterno }}</strong><div class="text-secondary small">{{ $alumno?->matricula }}</div></td>
                        <td>{{ $alumno?->nivelEducativo?->nombre ?? 'Sin nivel' }}<div class="text-secondary small">{{ $alumno?->grado?->nombre ?? 'Sin grado' }} · {{ $alumno?->seccion ?? 'Sin sección' }}</div></td>
                        <td><span class="badge bg-azure-lt">{{ ucfirst($registro->tipo) }}</span></td>
                        <td class="text-wrap" style="min-width: 220px">{{ $registro->resumen }}</td>
                        <td>{{ $registro->usuario?->name ?? 'Sin registro' }}</td>
                        <td>@if($alumno)<a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-sm btn-outline-primary">Ver expediente</a>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-secondary py-5">No hay comunicaciones que coincidan con los filtros actuales.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="bitacora-paginacion" class="card-footer @if(! $bitacoras->hasPages()) d-none @endif">{{ $bitacoras->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bitacora-filtros');
    if (!form) return;

    const resultados = document.getElementById('bitacora-resultados');
    const paginacion = document.getElementById('bitacora-paginacion');
    let espera;
    const escapar = (valor) => String(valor ?? '').replace(/[&<>'"]/g, (caracter) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' })[caracter]);
    const fecha = (valor) => valor ? new Intl.DateTimeFormat('es-MX', { dateStyle: 'short', timeStyle: 'short' }).format(new Date(valor)) : '—';

    const dibujarPaginacion = (data, parametros) => {
        if (data.last_page <= 1) {
            paginacion.classList.add('d-none');
            paginacion.innerHTML = '';
            return;
        }
        const boton = (pagina, etiqueta, activo = false, deshabilitado = false) => `<li class="page-item ${activo ? 'active' : ''} ${deshabilitado ? 'disabled' : ''}"><button type="button" class="page-link" data-pagina="${pagina}" ${deshabilitado ? 'disabled' : ''}>${etiqueta}</button></li>`;
        let html = '<nav aria-label="Paginación de bitácora"><ul class="pagination mb-0">';
        html += boton(data.current_page - 1, '&laquo; Anterior', false, data.current_page === 1);
        for (let pagina = 1; pagina <= data.last_page; pagina++) html += boton(pagina, pagina, pagina === data.current_page);
        html += boton(data.current_page + 1, 'Siguiente &raquo;', false, data.current_page === data.last_page);
        html += '</ul></nav>';
        paginacion.innerHTML = html;
        paginacion.classList.remove('d-none');
        paginacion.querySelectorAll('[data-pagina]').forEach((botonPagina) => botonPagina.addEventListener('click', () => aplicar(Number(botonPagina.dataset.pagina))));
    };

    const dibujarResultados = (data) => {
        if (!data.data.length) {
            resultados.innerHTML = '<tr><td colspan="7" class="text-center text-secondary py-5">No hay comunicaciones que coincidan con los filtros actuales.</td></tr>';
            return;
        }
        resultados.innerHTML = data.data.map((registro) => {
            const alumno = registro.alumno ?? {};
            const nivel = alumno.nivel_educativo?.nombre ?? 'Sin nivel';
            const grado = alumno.grado?.nombre ?? 'Sin grado';
            const alumnoNombre = [alumno.nombre, alumno.apellidopaterno, alumno.apellidomaterno].filter(Boolean).join(' ');
            const medio = { llamada: 'Llamada', correo: 'Correo', reunion: 'Reunión', mensaje: 'Mensaje', otro: 'Otro' }[registro.tipo] ?? registro.tipo;
            const enlace = alumno.id ? `<a href="{{ url('alumnos') }}/${alumno.id}/edit" class="btn btn-sm btn-outline-primary">Ver expediente</a>` : '';
            return `<tr><td class="text-nowrap">${fecha(registro.fecha_contacto)}</td><td><strong>${escapar(alumnoNombre)}</strong><div class="text-secondary small">${escapar(alumno.matricula)}</div></td><td>${escapar(nivel)}<div class="text-secondary small">${escapar(grado)} · ${escapar(alumno.seccion ?? 'Sin sección')}</div></td><td><span class="badge bg-azure-lt">${escapar(medio)}</span></td><td class="text-wrap" style="min-width: 220px">${escapar(registro.resumen)}</td><td>${escapar(registro.usuario?.name ?? 'Sin registro')}</td><td>${enlace}</td></tr>`;
        }).join('');
    };

    const aplicar = (pagina = 1) => {
        const parametros = new URLSearchParams(new FormData(form));
        parametros.set('page', String(pagina));
        const url = `${window.location.pathname}?${parametros.toString()}`;
        resultados.setAttribute('aria-busy', 'true');
        fetch(url, { headers: { Accept: 'application/json' } })
            .then((respuesta) => {
                if (!respuesta.ok) throw new Error('No fue posible actualizar la bitácora.');
                return respuesta.json();
            })
            .then((data) => {
                dibujarResultados(data);
                dibujarPaginacion(data, parametros);
                window.history.replaceState({}, '', url);
            })
            .catch(() => {
                resultados.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">No fue posible actualizar los resultados. Inténtalo nuevamente.</td></tr>';
            })
            .finally(() => resultados.removeAttribute('aria-busy'));
    };

    document.getElementById('bitacora-buscar')?.addEventListener('input', () => {
        window.clearTimeout(espera);
        espera = window.setTimeout(aplicar, 400);
    });
    form.querySelectorAll('select, input[type="date"]').forEach((campo) => campo.addEventListener('change', aplicar));
    form.addEventListener('submit', (evento) => { evento.preventDefault(); aplicar(); });
});
</script>
@endpush

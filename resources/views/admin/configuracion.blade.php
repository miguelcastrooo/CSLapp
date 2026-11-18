@extends('layouts.app')

@section('title', 'Configuración académica | Instituto Horizonte')

@section('content')
<div class="container-xl py-4">
    <div class="csl-module-header">
        <h1>Configuración académica</h1>
        <p>Prepara la estructura de la escuela y sus plataformas. No necesitas modificar el sistema.</p>
    </div>

    <nav class="csl-form-nav" aria-label="Accesos rápidos de configuración">
        <a href="#agregar-nivel"><i class="fas fa-layer-group" aria-hidden="true"></i>Nivel</a>
        <a href="#agregar-grado"><i class="fas fa-list-ol" aria-hidden="true"></i>Grado</a>
        <a href="#agregar-seccion"><i class="fas fa-table-cells-large" aria-hidden="true"></i>Sección</a>
        <a href="#agregar-plataforma"><i class="fas fa-laptop" aria-hidden="true"></i>Plataforma</a>
        <a href="#asignar-plataformas"><i class="fas fa-link" aria-hidden="true"></i>Asignación</a>
        <a href="#administrar-configuracion"><i class="fas fa-gear" aria-hidden="true"></i>Administrar</a>
    </nav>

    @if(false){{-- La identidad visual se administra en Diseño del portal. --}}
    <div class="card csl-data-card mb-4" id="identidad-institucional">
        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <div>
                <h2 class="card-title mb-1"><i class="fas fa-palette me-2" aria-hidden="true"></i>Identidad institucional</h2>
                <p class="text-secondary small mb-0">Personaliza el nombre, logotipo y colores del portal. Los cambios se ven en el inicio de sesión, menú, panel y documentos PDF.</p>
            </div>
            <span class="badge bg-primary-lt text-primary">Sólo Admin</span>
        </div>
        <div class="card-body">
            <div class="alert alert-info d-flex gap-2 align-items-start" role="note">
                <i class="fas fa-circle-info mt-1" aria-hidden="true"></i>
                <div><strong>Recomendación:</strong> elige tonos oscuros o medios para el menú y botones; así los textos blancos siempre se distinguen bien.</div>
            </div>
            <form method="POST" action="{{ route('admin.configuracion.institucion') }}" enctype="multipart/form-data" class="row g-3" id="institution-brand-form">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <label class="form-label" for="institution-name">Nombre de la escuela</label>
                    <input id="institution-name" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $configuracionInstitucional->nombre) }}" maxlength="100" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="institution-subtitle">Subtítulo del portal <span class="text-secondary">(opcional)</span></label>
                    <input id="institution-subtitle" name="subtitulo" class="form-control @error('subtitulo') is-invalid @enderror" value="{{ old('subtitulo', $configuracionInstitucional->subtitulo) }}" maxlength="100" placeholder="Ej. Portal académico">
                    @error('subtitulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="institution-logo">Logotipo o ícono</label>
                    <input id="institution-logo" name="logo" type="file" class="form-control @error('logo') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                    <small class="text-secondary">PNG, JPG o WEBP de máximo 2 MB. Si no eliges un archivo, se conserva el logotipo actual.</small>
                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Vista previa</label>
                    <div class="csl-brand-preview" id="institution-preview" style="--preview-primary: {{ $configuracionInstitucional->color_primario }}; --preview-accent: {{ $configuracionInstitucional->color_acento }};">
                        <img id="institution-preview-logo" src="{{ $configuracionInstitucional->logoUrl() }}" alt="Vista previa del logotipo">
                        <div><strong id="institution-preview-name">{{ $configuracionInstitucional->nombre }}</strong><span id="institution-preview-subtitle">{{ $configuracionInstitucional->subtitulo ?: 'Portal académico' }}</span></div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="primary-color">Color de encabezados y tablas</label>
                    <input id="primary-color" name="color_primario" type="color" class="form-control form-control-color w-100 @error('color_primario') is-invalid @enderror" value="{{ old('color_primario', $configuracionInstitucional->color_primario) }}" title="Color de encabezados y tablas">
                    @error('color_primario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="sidebar-color">Color del menú lateral</label>
                    <input id="sidebar-color" name="color_sidebar" type="color" class="form-control form-control-color w-100 @error('color_sidebar') is-invalid @enderror" value="{{ old('color_sidebar', $configuracionInstitucional->color_sidebar) }}" title="Color del menú lateral">
                    @error('color_sidebar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="accent-color">Color de botones</label>
                    <input id="accent-color" name="color_acento" type="color" class="form-control form-control-color w-100 @error('color_acento') is-invalid @enderror" value="{{ old('color_acento', $configuracionInstitucional->color_acento) }}" title="Color de botones">
                    @error('color_acento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="surface-color">Color de fondo</label>
                    <input id="surface-color" name="color_fondo" type="color" class="form-control form-control-color w-100 @error('color_fondo') is-invalid @enderror" value="{{ old('color_fondo', $configuracionInstitucional->color_fondo) }}" title="Color de fondo">
                    @error('color_fondo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="text-color">Color del texto principal</label>
                    <input id="text-color" name="color_texto" type="color" class="form-control form-control-color w-100 @error('color_texto') is-invalid @enderror" value="{{ old('color_texto', $configuracionInstitucional->color_texto ?? '#183444') }}">
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="muted-text-color">Color del texto secundario</label>
                    <input id="muted-text-color" name="color_texto_secundario" type="color" class="form-control form-control-color w-100 @error('color_texto_secundario') is-invalid @enderror" value="{{ old('color_texto_secundario', $configuracionInstitucional->color_texto_secundario ?? '#526474') }}">
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="carousel-color">Tono de carruseles</label>
                    <input id="carousel-color" name="color_carrusel" type="color" class="form-control form-control-color w-100 @error('color_carrusel') is-invalid @enderror" value="{{ old('color_carrusel', $configuracionInstitucional->color_carrusel ?? '#173D5B') }}">
                    <small class="text-secondary">Aplica al carrusel del panel y al de inicio de sesión.</small>
                </div>
                <div class="col-12 d-flex flex-column flex-sm-row gap-2 pt-2">
                    <button class="btn btn-primary"><i class="fas fa-floppy-disk me-2" aria-hidden="true"></i>Guardar identidad institucional</button>
                </div>
            </form>
            <form id="restore-institution-form" method="POST" action="{{ route('admin.configuracion.institucion.restaurar') }}" class="mt-2" data-confirm="Se restaurarán el nombre, los colores, la tipografía y el logotipo de ejemplo. La información escolar no se modificará." data-confirm-title="Restaurar diseño predeterminado" data-confirm-button="Sí, restaurar">
                @csrf
                <button type="submit" formmethod="post" formaction="{{ route('admin.configuracion.institucion.restaurar') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-rotate-left me-1" aria-hidden="true"></i>Restaurar valores de ejemplo</button>
            </form>
        </div>
    </div>

    @endif
    <x-task-guide
        title="Cómo agregar una nueva área escolar"
        description="Sigue este orden para que los registros queden organizados y sea fácil elegirlos al registrar alumnos."
        :steps="[
            'Crea el nivel educativo, por ejemplo: Preparatoria.',
            'Agrega los grados que pertenecen a ese nivel, por ejemplo: 1.º, 2.º y 3.º.',
            'Agrega las secciones de cada grado, por ejemplo: A, B o C.',
            'Crea las plataformas y marca en qué nivel se utilizan.'
        ]"
    />

    <div class="alert alert-info d-flex gap-3 align-items-start" role="note">
        <i class="fas fa-sitemap mt-1" aria-hidden="true"></i>
        <div>
            <strong>La estructura siempre va de lo general a lo específico:</strong>
            <div class="mt-1"><b>Nivel educativo</b> <i class="fas fa-arrow-right mx-1" aria-hidden="true"></i> <b>Grado</b> <i class="fas fa-arrow-right mx-1" aria-hidden="true"></i> <b>Sección</b></div>
            <small>Ejemplo: <b>Preparatoria</b> → <b>1.º</b> → <b>A</b>. Por eso el nivel se elige al crear un grado, y el grado se elige al crear una sección.</small>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card csl-data-card h-100" id="agregar-nivel">
                <div class="card-body">
                    <h2 class="h3">1. Agregar nivel educativo</h2>
                    <p class="text-secondary small">Es la etapa escolar más amplia, como Preescolar, Primaria, Secundaria o Preparatoria.</p>
                    <form method="POST" action="{{ route('admin.configuracion.niveles') }}" class="d-flex gap-2">
                        @csrf
                        <input name="nombre" class="form-control" placeholder="Ej. Preparatoria" required>
                        <button class="btn btn-primary text-nowrap">Agregar nivel</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card csl-data-card h-100" id="agregar-grado">
                <div class="card-body">
                    <h2 class="h3">2. Agregar grado</h2>
                    <p class="text-secondary small">Un grado siempre pertenece a un nivel. Ejemplo: 1.º pertenece a Preparatoria.</p>
                    <form method="POST" action="{{ route('admin.configuracion.grados') }}" class="row g-2">
                        @csrf
                        <div class="col-sm-6">
                            <label class="form-label small" for="grade-level">Nivel educativo</label>
                            <select id="grade-level" name="nivel_educativo_id" class="form-select" required>
                                <option value="">Selecciona un nivel</option>
                                @foreach($niveles as $nivel)
                                    <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small" for="grade-name">Nombre del grado</label>
                            <input id="grade-name" name="nombre" class="form-control" placeholder="Ej. 1.º" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small" for="promotion-order">Orden de promoción <span class="text-secondary">(opcional)</span></label>
                            <input id="promotion-order" name="orden_promocion" type="number" min="1" max="999" class="form-control" placeholder="Ej. 13. Si se deja vacío, se asigna el siguiente orden disponible.">
                            <small class="text-secondary">El sistema usa este número para saber a qué grado pasa un grupo. No repitas un número ya usado.</small>
                        </div>
                        <div class="col-12"><button class="btn btn-primary">Agregar grado</button></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card csl-data-card h-100" id="agregar-seccion">
                <div class="card-body">
                    <h2 class="h3">3. Agregar sección</h2>
                    <p class="text-secondary small">Primero elige el nivel. Después aparecerán solamente los grados de ese nivel.</p>
                    <form method="POST" action="{{ route('admin.configuracion.secciones') }}" class="row g-2">
                        @csrf
                        <div class="col-sm-5">
                            <label class="form-label small" for="section-level">Nivel educativo</label>
                            <select id="section-level" class="form-select">
                                <option value="">Selecciona un nivel</option>
                                @foreach($niveles as $nivel)
                                    <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small" for="section-grade">Grado</label>
                            <select id="section-grade" name="grado_id" class="form-select" required disabled>
                                <option value="">Primero elige nivel</option>
                                @foreach($grados as $grado)
                                    <option value="{{ $grado->id }}" data-level="{{ $grado->nivel_educativo_id }}">{{ $grado->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label small" for="section-name">Sección</label>
                            <input id="section-name" name="nombre" class="form-control" placeholder="Ej. A" required>
                        </div>
                        <div class="col-12"><button class="btn btn-primary">Agregar sección</button></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card csl-data-card h-100" id="agregar-plataforma">
                <div class="card-body">
                    <h2 class="h3">4. Agregar plataforma</h2>
                    <p class="text-secondary small">Crea la plataforma primero. Después la asignas al nivel correspondiente en el apartado de abajo.</p>
                    <form method="POST" action="{{ route('admin.configuracion.plataformas') }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-12 col-sm">
                            <label class="form-label small" for="platform-name">Nombre de la plataforma</label>
                            <input id="platform-name" name="nombre" class="form-control" placeholder="Ej. Microsoft Teams" required>
                        </div>
                        <div class="col-12 col-sm-auto d-grid">
                            <button class="btn btn-primary px-3">Agregar plataforma</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card csl-data-card mt-4" id="asignar-plataformas">
        <div class="card-header">
            <div>
                <h2 class="card-title mb-1">5. Indicar qué plataformas usa cada nivel</h2>
                <p class="text-secondary small mb-0">Marca todas las plataformas que deban aparecer para cada nivel y guarda ese nivel.</p>
            </div>
        </div>
        <div class="card-body row g-3">
            @forelse($niveles as $nivel)
                <div class="col-md-6">
                    @php
                        $plataformasSeleccionadas = old('plataformas', $nivel->plataformas->pluck('id')->all());
                    @endphp
                    <form method="POST" action="{{ route('admin.configuracion.nivel-plataformas', $nivel) }}" class="border rounded p-3 h-100 platform-level-form" data-level-name="{{ $nivel->nombre }}">
                        @csrf
                        <input type="hidden" name="confirmar_retiro" value="0" class="platform-removal-confirmation">
                        <strong>{{ $nivel->nombre }}</strong>
                        <div class="mt-2">
                            @forelse($plataformas as $plataforma)
                                <label class="form-check">
                                    <input
                                        class="form-check-input platform-checkbox"
                                        type="checkbox"
                                        name="plataformas[]"
                                        value="{{ $plataforma->id }}"
                                        data-platform-name="{{ $plataforma->nombre }}"
                                        data-originally-assigned="{{ $nivel->plataformas->contains($plataforma) ? '1' : '0' }}"
                                        data-student-records="{{ $credencialesPorNivelYPlataforma->get($nivel->id . '-' . $plataforma->id, 0) }}"
                                        @checked(in_array($plataforma->id, $plataformasSeleccionadas))
                                    >
                                    {{ $plataforma->nombre }}
                                </label>
                            @empty
                                <span class="text-secondary small">Aún no hay plataformas creadas.</span>
                            @endforelse
                        </div>
                        <p class="text-secondary small mt-3 mb-0">
                            <i class="fas fa-shield-halved me-1" aria-hidden="true"></i>
                            Si desmarcas una plataforma, sus credenciales y registros de alumnos no se borran; únicamente deja de estar disponible para este nivel.
                        </p>
                        <button class="btn btn-sm btn-primary mt-3">Guardar plataformas de este nivel</button>
                    </form>
                </div>
            @empty
                <p class="text-secondary mb-0">Crea primero un nivel educativo para poder asignarle plataformas.</p>
            @endforelse
        </div>
    </div>

    <div class="card csl-data-card mt-4" id="administrar-configuracion">
        <div class="card-header">
            <div>
                <h2 class="card-title mb-1">Administrar lo creado</h2>
                <p class="text-secondary small mb-0">Desactivar oculta una opción para nuevos registros; nunca borra información. Si ya tiene alumnos o credenciales, el sistema la protege y no permite desactivarla.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter table-hover mb-0">
                <thead><tr><th>Tipo</th><th>Nombre</th><th>Pertenece a</th><th>Estado</th><th class="text-end">Acción</th></tr></thead>
                <tbody>
                    @foreach($niveles as $nivel)
                        <tr>
                            <td>Nivel educativo</td><td class="fw-semibold">{{ $nivel->nombre }}</td><td><span class="badge bg-secondary-lt text-secondary">Nivel principal</span></td>
                            <td><span class="badge {{ $nivel->activo ? 'bg-success-lt text-success' : 'bg-secondary-lt text-secondary' }}">{{ $nivel->activo ? 'Activo' : 'Inactivo' }}</span></td>
                            <td class="text-end"><div class="d-inline-flex flex-wrap gap-1 justify-content-end"><div class="btn-group" role="group" aria-label="Cambiar posición de {{ $nivel->nombre }}"><form method="POST" action="{{ route('admin.configuracion.niveles.mover', $nivel) }}">@csrf<input type="hidden" name="direccion" value="subir"><button class="btn btn-sm btn-outline-secondary" title="Mover hacia arriba" @disabled($loop->first)><i class="fas fa-arrow-up"></i><span class="visually-hidden">Subir</span></button></form><form method="POST" action="{{ route('admin.configuracion.niveles.mover', $nivel) }}">@csrf<input type="hidden" name="direccion" value="bajar"><button class="btn btn-sm btn-outline-secondary" title="Mover hacia abajo" @disabled($loop->last)><i class="fas fa-arrow-down"></i><span class="visually-hidden">Bajar</span></button></form></div><form method="POST" action="{{ route('admin.configuracion.estado', ['tipo' => 'nivel', 'id' => $nivel->id]) }}">@csrf<button class="btn btn-sm {{ $nivel->activo ? 'btn-outline-danger' : 'btn-outline-success' }}">{{ $nivel->activo ? 'Desactivar' : 'Activar' }}</button></form></div></td>
                        </tr>
                    @endforeach
                    @foreach($grados as $grado)
                        <tr>
                            <td>Grado</td><td class="fw-semibold">{{ $grado->nombre }}</td><td>{{ $grado->nivelEducativo?->nombre ?? 'Sin nivel' }}</td>
                            <td><span class="badge {{ $grado->activo ? 'bg-success-lt text-success' : 'bg-secondary-lt text-secondary' }}">{{ $grado->activo ? 'Activo' : 'Inactivo' }}</span></td>
                            <td class="text-end"><form method="POST" action="{{ route('admin.configuracion.estado', ['tipo' => 'grado', 'id' => $grado->id]) }}">@csrf<button class="btn btn-sm {{ $grado->activo ? 'btn-outline-danger' : 'btn-outline-success' }}">{{ $grado->activo ? 'Desactivar' : 'Activar' }}</button></form></td>
                        </tr>
                    @endforeach
                    @foreach($secciones as $seccion)
                        <tr>
                            <td>Sección</td><td class="fw-semibold">{{ $seccion->nombre }}</td><td>{{ $seccion->grado?->nivelEducativo?->nombre ?? 'Sin nivel' }} · {{ $seccion->grado?->nombre ?? 'Sin grado' }}</td>
                            <td><span class="badge {{ $seccion->activo ? 'bg-success-lt text-success' : 'bg-secondary-lt text-secondary' }}">{{ $seccion->activo ? 'Activa' : 'Inactiva' }}</span></td>
                            <td class="text-end"><form method="POST" action="{{ route('admin.configuracion.estado', ['tipo' => 'seccion', 'id' => $seccion->id]) }}">@csrf<button class="btn btn-sm {{ $seccion->activo ? 'btn-outline-danger' : 'btn-outline-success' }}">{{ $seccion->activo ? 'Desactivar' : 'Activar' }}</button></form></td>
                        </tr>
                    @endforeach
                    @foreach($plataformas as $plataforma)
                        <tr>
                            <td>Plataforma</td><td class="fw-semibold">{{ $plataforma->nombre }}</td><td>{{ $plataforma->niveles->pluck('nombre')->join(' · ') ?: 'Sin asignar' }}</td>
                            <td><span class="badge {{ $plataforma->activo ? 'bg-success-lt text-success' : 'bg-secondary-lt text-secondary' }}">{{ $plataforma->activo ? 'Activa' : 'Inactiva' }}</span></td>
                            <td class="text-end"><form method="POST" action="{{ route('admin.configuracion.estado', ['tipo' => 'plataforma', 'id' => $plataforma->id]) }}">@csrf<button class="btn btn-sm {{ $plataforma->activo ? 'btn-outline-danger' : 'btn-outline-success' }}">{{ $plataforma->activo ? 'Desactivar' : 'Activar' }}</button></form></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const brandForm = document.getElementById('institution-brand-form');
    if (brandForm) {
        const preview = document.getElementById('institution-preview');
        const previewName = document.getElementById('institution-preview-name');
        const previewSubtitle = document.getElementById('institution-preview-subtitle');
        const previewLogo = document.getElementById('institution-preview-logo');
        const name = document.getElementById('institution-name');
        const subtitle = document.getElementById('institution-subtitle');
        const primary = document.getElementById('primary-color');
        const accent = document.getElementById('accent-color');
        const logo = document.getElementById('institution-logo');

        const updatePreview = () => {
            previewName.textContent = name.value.trim() || 'Nombre de la escuela';
            previewSubtitle.textContent = subtitle.value.trim() || 'Portal académico';
            preview.style.setProperty('--preview-primary', primary.value);
            preview.style.setProperty('--preview-accent', accent.value);
        };

        [name, subtitle, primary, accent].forEach((input) => input.addEventListener('input', updatePreview));
        logo.addEventListener('change', () => {
            const file = logo.files[0];
            if (file) previewLogo.src = URL.createObjectURL(file);
        });
    }

    const level = document.getElementById('section-level');
    const grade = document.getElementById('section-grade');
    const options = [...grade.options].slice(1);

    level.addEventListener('change', () => {
        grade.innerHTML = '<option value="">Selecciona un grado</option>';
        options
            .filter(option => option.dataset.level === level.value)
            .forEach(option => grade.add(option.cloneNode(true)));

        grade.disabled = !level.value;
    });

    document.querySelectorAll('.platform-level-form').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            if (form.dataset.platformRemovalConfirmed === '1') {
                return;
            }

            const removedWithData = [...form.querySelectorAll('.platform-checkbox')]
                .filter((checkbox) => checkbox.dataset.originallyAssigned === '1' && !checkbox.checked)
                .filter((checkbox) => Number(checkbox.dataset.studentRecords) > 0);

            if (removedWithData.length === 0) {
                return;
            }

            event.preventDefault();

            const details = removedWithData
                .map((checkbox) => `${checkbox.dataset.platformName}: ${checkbox.dataset.studentRecords} alumno(s)`)
                .join('\n');
            const levelName = form.dataset.levelName;
            const confirmed = await window.schoolConfirm({
                title: 'Retirar plataformas del nivel',
                message: `Vas a retirar plataforma(s) con información registrada en ${levelName}.\n\n${details}\n\nNo se borrarán usuarios, contraseñas ni registros. La plataforma sólo dejará de mostrarse para este nivel.`,
                confirmLabel: 'Sí, retirar plataformas',
            });

            if (!confirmed) {
                return;
            }

            form.querySelector('.platform-removal-confirmation').value = '1';
            form.dataset.platformRemovalConfirmed = '1';
            form.requestSubmit();
        });
    });

});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Configuración académica | Instituto Horizonte')

@section('content')
<div class="container-xl py-4">
    <div class="csl-module-header">
        <h1>Configuración académica</h1>
        <p>Prepara la estructura de la escuela y sus plataformas. No necesitas modificar el sistema.</p>
    </div>

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
            <div class="card csl-data-card h-100">
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
            <div class="card csl-data-card h-100">
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
            <div class="card csl-data-card h-100">
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
            <div class="card csl-data-card h-100">
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

    <div class="card csl-data-card mt-4">
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

    <div class="card csl-data-card mt-4">
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
                            <td>Nivel educativo</td><td class="fw-semibold">{{ $nivel->nombre }}</td><td>—</td>
                            <td><span class="badge {{ $nivel->activo ? 'bg-success-lt text-success' : 'bg-secondary-lt text-secondary' }}">{{ $nivel->activo ? 'Activo' : 'Inactivo' }}</span></td>
                            <td class="text-end"><form method="POST" action="{{ route('admin.configuracion.estado', ['tipo' => 'nivel', 'id' => $nivel->id]) }}">@csrf<button class="btn btn-sm {{ $nivel->activo ? 'btn-outline-danger' : 'btn-outline-success' }}">{{ $nivel->activo ? 'Desactivar' : 'Activar' }}</button></form></td>
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
                            <td>Plataforma</td><td class="fw-semibold">{{ $plataforma->nombre }}</td><td>Se asigna por nivel</td>
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
        form.addEventListener('submit', (event) => {
            const removedWithData = [...form.querySelectorAll('.platform-checkbox')]
                .filter((checkbox) => checkbox.dataset.originallyAssigned === '1' && !checkbox.checked)
                .filter((checkbox) => Number(checkbox.dataset.studentRecords) > 0);

            if (removedWithData.length === 0) {
                return;
            }

            const details = removedWithData
                .map((checkbox) => `${checkbox.dataset.platformName}: ${checkbox.dataset.studentRecords} alumno(s)`)
                .join('\n');
            const levelName = form.dataset.levelName;
            const confirmed = window.confirm(
                `Atención: vas a retirar plataforma(s) con información registrada en ${levelName}.\n\n${details}\n\nNo se borrarán usuarios, contraseñas ni registros. La plataforma sólo dejará de mostrarse para este nivel. ¿Deseas continuar?`
            );

            if (!confirmed) {
                event.preventDefault();
                return;
            }

            form.querySelector('.platform-removal-confirmation').value = '1';
        });
    });
});
</script>
@endsection

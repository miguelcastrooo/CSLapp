@extends('layouts.app')

@section('title', 'Diseño del portal')

@section('content')
<div class="container-xl py-4">
    <div class="csl-module-header">
        <h1>Diseño del portal</h1>
        <p>Personaliza la identidad visual y las imágenes que acompañan al equipo.</p>
    </div>

    <x-task-guide title="Personalización segura" description="Los cambios visuales no modifican alumnos, plataformas ni historiales." :steps="['Ajusta nombre, logo y colores.', 'Agrega las imágenes que quieres mostrar.', 'Ordénalas con las flechas y activa sólo las necesarias.']" />

    <div class="card csl-data-card mb-4">
        <div class="card-header"><h2 class="card-title mb-0">Identidad y colores</h2></div>
        <div class="card-body">
            <div class="mb-4">
                <label class="form-label d-block">Temas rápidos</label>
                <p class="text-secondary small mb-2">Elige uno para ver una combinación completa al instante. No se guarda hasta pulsar “Guardar diseño”.</p>
                <div class="d-flex flex-wrap gap-2" role="group" aria-label="Temas de color sugeridos">
                    <button type="button" class="btn btn-outline-primary design-preset" data-primary="#24536D" data-sidebar="#245B75" data-accent="#176B62" data-background="#F3F7FB" data-text="#183444" data-muted="#526474" data-carousel="#173D5B"><i class="fas fa-school me-1"></i>Institucional</button>
                    <button type="button" class="btn btn-outline-primary design-preset" data-primary="#394B7A" data-sidebar="#303C63" data-accent="#E18239" data-background="#F7F7FA" data-text="#202B45" data-muted="#5F687D" data-carousel="#273A6C"><i class="fas fa-sun me-1"></i>Clásico</button>
                    <button type="button" class="btn btn-outline-primary design-preset" data-primary="#246B61" data-sidebar="#174C49" data-accent="#D2942A" data-background="#F4F8F5" data-text="#183D38" data-muted="#5A716B" data-carousel="#1A5A52"><i class="fas fa-leaf me-1"></i>Natural</button>
                    <button type="button" class="btn btn-outline-primary design-preset" data-primary="#6B3D75" data-sidebar="#4D2C55" data-accent="#D15F7B" data-background="#FAF6FA" data-text="#3B263F" data-muted="#6C5A70" data-carousel="#59305E"><i class="fas fa-palette me-1"></i>Creativo</button>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.configuracion.institucion') }}" enctype="multipart/form-data" class="row g-3" id="institution-brand-form">
                @csrf
                @method('PUT')
                <div class="col-md-6"><label class="form-label" for="institution-name">Nombre de la escuela</label><input id="institution-name" class="form-control" name="nombre" value="{{ $configuracionInstitucional->nombre }}" required></div>
                <div class="col-md-6"><label class="form-label" for="institution-subtitle">Subtítulo</label><input id="institution-subtitle" class="form-control" name="subtitulo" value="{{ $configuracionInstitucional->subtitulo }}"></div>
                <div class="col-md-6"><label class="form-label" for="institution-logo">Logotipo</label><input id="institution-logo" class="form-control" name="logo" type="file" accept="image/png,image/jpeg,image/webp"><small class="text-secondary">PNG, JPG o WEBP; máximo 2 MB.</small></div>
                <div class="col-md-6"><label class="form-label d-block">Vista previa</label><div class="csl-brand-preview" id="institution-preview" style="--preview-primary: {{ $configuracionInstitucional->color_primario }}; --preview-accent: {{ $configuracionInstitucional->color_acento }};"><img id="institution-preview-logo" src="{{ $configuracionInstitucional->logoUrl() }}" alt="Vista previa del logotipo"><div><strong id="institution-preview-name">{{ $configuracionInstitucional->nombre }}</strong><span id="institution-preview-subtitle">{{ $configuracionInstitucional->subtitulo ?: 'Portal académico' }}</span></div></div></div>
                @foreach (['color_primario' => 'Encabezados y tablas', 'color_sidebar' => 'Menú lateral', 'color_acento' => 'Botones', 'color_fondo' => 'Fondo', 'color_texto' => 'Texto principal', 'color_texto_secundario' => 'Texto secundario', 'color_carrusel' => 'Tono de carruseles'] as $campo => $etiqueta)
                    <div class="col-sm-6 col-lg-3"><label class="form-label" for="{{ $campo }}">{{ $etiqueta }}</label><input id="{{ $campo }}" class="form-control form-control-color w-100" name="{{ $campo }}" type="color" value="{{ $configuracionInstitucional->$campo }}" required></div>
                @endforeach
                <div class="col-sm-6 col-lg-4"><label class="form-label" for="font-family">Tipografía</label><select id="font-family" class="form-select" name="fuente"><option value="Inter" @selected($configuracionInstitucional->fuente === 'Inter')>Inter · moderna y clara</option><option value="Arial" @selected($configuracionInstitucional->fuente === 'Arial')>Arial · sencilla y familiar</option><option value="Trebuchet MS" @selected($configuracionInstitucional->fuente === 'Trebuchet MS')>Trebuchet · amigable</option><option value="Georgia" @selected($configuracionInstitucional->fuente === 'Georgia')>Georgia · clásica</option></select></div>
                <div class="col-sm-6 col-lg-4"><label class="form-label" for="font-size">Tamaño de texto</label><select id="font-size" class="form-select" name="tamano_texto"><option value="15" @selected((int) $configuracionInstitucional->tamano_texto === 15)>Compacto · 15 px</option><option value="16" @selected((int) $configuracionInstitucional->tamano_texto === 16)>Normal · 16 px</option><option value="17" @selected((int) $configuracionInstitucional->tamano_texto === 17)>Grande · 17 px</option><option value="18" @selected((int) $configuracionInstitucional->tamano_texto === 18)>Lectura fácil · 18 px</option></select></div>
                <div class="col-12"><label class="form-label d-block">Así se verá el portal</label><div id="portal-live-preview" class="csl-design-live-preview"><aside><div class="csl-design-preview-logo"><i class="fas fa-graduation-cap"></i></div><strong id="portal-preview-name">{{ $configuracionInstitucional->nombre }}</strong><small id="portal-preview-subtitle">{{ $configuracionInstitucional->subtitulo ?: 'Portal académico' }}</small><span><i class="fas fa-house me-2"></i>Inicio</span><span><i class="fas fa-users me-2"></i>Alumnos</span></aside><main><div class="csl-design-preview-topbar"><span>Portal académico</span><i class="fas fa-bell"></i></div><section><h3>Buenos días</h3><p>Consulta la información escolar de forma clara y ordenada.</p><div class="csl-design-preview-card"><strong>Alumnos activos</strong><b>24</b></div><button type="button">Registrar alumno</button></section></main></div><small class="text-secondary d-block mt-2"><i class="fas fa-eye me-1"></i>Esta vista es temporal. Revisa, ajusta y guarda sólo cuando te guste el resultado.</small></div>
                <div class="col-12 d-flex flex-wrap gap-2"><button class="btn btn-primary"><i class="fas fa-floppy-disk me-2"></i>Guardar diseño</button></div>
            </form>
            <form id="restore-design-form" method="POST" action="{{ route('admin.configuracion.institucion.restaurar') }}" class="mt-2" data-confirm="Se restaurarán el nombre, los colores, la tipografía y el logotipo de ejemplo. La información escolar no se modificará." data-confirm-title="Restaurar diseño predeterminado" data-confirm-button="Sí, restaurar">
                @csrf
                <button type="submit" formmethod="post" formaction="{{ route('admin.configuracion.institucion.restaurar') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-rotate-left me-1"></i>Restaurar valores de ejemplo</button>
            </form>
        </div>
    </div>

    <div class="card csl-data-card">
        <div class="card-header"><h2 class="card-title mb-0">Imágenes del carrusel</h2></div>
        <div class="card-body">
            <p class="text-secondary mb-3">El orden de estas tarjetas es el orden en que aparecerán en el panel principal. Usa las flechas para moverlas sin volver a cargar la imagen.</p>
            <form method="POST" enctype="multipart/form-data" action="{{ route('admin.diseno.carrusel.guardar') }}" class="row g-3 mb-4">
                @csrf
                <div class="col-md-4"><label class="form-label">Título</label><input class="form-control" name="titulo" maxlength="120" required></div>
                <div class="col-md-4"><label class="form-label">Texto breve</label><input class="form-control" name="descripcion" maxlength="255"></div>
                <div class="col-md-2"><label class="form-label">Orden</label><input class="form-control" name="orden" type="number" min="1" placeholder="Automático"></div>
                <div class="col-md-2"><label class="form-label">Imagen</label><input class="form-control" name="imagen" type="file" accept="image/png,image/jpeg,image/webp" required></div>
                <div class="col-12"><button class="btn btn-primary"><i class="fas fa-image me-2"></i>Agregar imagen</button></div>
            </form>

            <div class="row row-cards">
                @forelse ($imagenesCarrusel as $imagen)
                    <div class="col-md-6 col-xl-4">
                        <article class="card h-100">
                            <img src="{{ $imagen->url() }}" class="card-img-top" alt="{{ $imagen->titulo }}" style="height:150px;object-fit:cover">
                            <div class="card-body">
                                <div class="d-flex justify-content-between gap-2"><strong>{{ $imagen->titulo }}</strong><span class="badge bg-azure-lt text-azure">{{ $loop->iteration }}</span></div>
                                <p class="text-secondary small mb-2">{{ $imagen->descripcion }}</p>
                                <span class="badge {{ $imagen->activo ? 'bg-green-lt text-success' : 'bg-secondary-lt text-secondary' }}">{{ $imagen->activo ? 'Visible' : 'Oculta' }}</span>
                            </div>
                            <div class="card-footer d-flex flex-wrap gap-2 align-items-center">
                                <div class="btn-group" role="group" aria-label="Cambiar orden">
                                    <form method="POST" action="{{ route('admin.diseno.carrusel.mover', $imagen) }}">@csrf<input type="hidden" name="direccion" value="subir"><button class="btn btn-sm btn-outline-secondary" title="Subir imagen" @disabled($loop->first)><i class="fas fa-arrow-up"></i><span class="visually-hidden">Subir</span></button></form>
                                    <form method="POST" action="{{ route('admin.diseno.carrusel.mover', $imagen) }}">@csrf<input type="hidden" name="direccion" value="bajar"><button class="btn btn-sm btn-outline-secondary" title="Bajar imagen" @disabled($loop->last)><i class="fas fa-arrow-down"></i><span class="visually-hidden">Bajar</span></button></form>
                                </div>
                                <form method="POST" action="{{ route('admin.diseno.carrusel.estado', $imagen) }}">@csrf<button class="btn btn-sm btn-outline-primary">{{ $imagen->activo ? 'Ocultar' : 'Mostrar' }}</button></form>
                                <form method="POST" action="{{ route('admin.diseno.carrusel.borrar', $imagen) }}" data-confirm="¿Eliminar esta imagen del carrusel?" data-confirm-title="Eliminar imagen" data-confirm-button="Sí, eliminar">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12 text-secondary">Aún no agregas imágenes propias. Se seguirán usando las imágenes de ejemplo.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('institution-brand-form');
    if (!form) return;

    const preview = document.getElementById('institution-preview');
    const name = document.getElementById('institution-name');
    const subtitle = document.getElementById('institution-subtitle');
    const logo = document.getElementById('institution-logo');
    const primary = document.getElementById('color_primario');
    const accent = document.getElementById('color_acento');
    const colorFields = ['color_primario', 'color_sidebar', 'color_acento', 'color_fondo', 'color_texto', 'color_texto_secundario', 'color_carrusel'];
    const portalPreview = document.getElementById('portal-live-preview');
    const font = document.getElementById('font-family');
    const fontSize = document.getElementById('font-size');
    const fontStacks = {
        Inter: 'Inter, ui-sans-serif, system-ui, sans-serif',
        Arial: 'Arial, Helvetica, sans-serif',
        'Trebuchet MS': '"Trebuchet MS", Arial, sans-serif',
        Georgia: 'Georgia, Times, serif',
    };

    const updatePreview = () => {
        document.getElementById('institution-preview-name').textContent = name.value.trim() || 'Nombre de la escuela';
        document.getElementById('institution-preview-subtitle').textContent = subtitle.value.trim() || 'Portal académico';
        preview.style.setProperty('--preview-primary', primary.value);
        preview.style.setProperty('--preview-accent', accent.value);
        document.getElementById('portal-preview-name').textContent = name.value.trim() || 'Nombre de la escuela';
        document.getElementById('portal-preview-subtitle').textContent = subtitle.value.trim() || 'Portal académico';
        const values = {
            '--preview-primary': primary.value,
            '--preview-sidebar': document.getElementById('color_sidebar').value,
            '--preview-accent': accent.value,
            '--preview-surface': document.getElementById('color_fondo').value,
            '--preview-text': document.getElementById('color_texto').value,
            '--preview-muted': document.getElementById('color_texto_secundario').value,
            '--preview-carousel': document.getElementById('color_carrusel').value,
        };
        Object.entries(values).forEach(([property, value]) => portalPreview.style.setProperty(property, value));
        portalPreview.style.fontFamily = fontStacks[font.value];
        portalPreview.style.fontSize = `${fontSize.value}px`;
    };

    [name, subtitle, font, fontSize, ...colorFields.map((field) => document.getElementById(field))].forEach((input) => input.addEventListener('input', updatePreview));
    [font, fontSize].forEach((input) => input.addEventListener('change', updatePreview));
    logo.addEventListener('change', () => {
        if (logo.files[0]) document.getElementById('institution-preview-logo').src = URL.createObjectURL(logo.files[0]);
    });

    document.querySelectorAll('.design-preset').forEach((button) => button.addEventListener('click', () => {
        const values = {
            color_primario: button.dataset.primary,
            color_sidebar: button.dataset.sidebar,
            color_acento: button.dataset.accent,
            color_fondo: button.dataset.background,
            color_texto: button.dataset.text,
            color_texto_secundario: button.dataset.muted,
            color_carrusel: button.dataset.carousel,
        };
        Object.entries(values).forEach(([field, value]) => { document.getElementById(field).value = value; });
        updatePreview();
    }));

});
</script>
@endsection

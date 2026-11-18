@php
    $route = request()->route()?->getName() ?? '';
    $secciones = match (true) {
        $route === 'dashboard' => [],
        str_starts_with($route, 'avisos.') => ['Avisos'],
        str_starts_with($route, 'profile.') => ['Mi perfil'],
        str_starts_with($route, 'alumnos.excel.') => ['Alumnos', 'Importar y exportar'],
        str_starts_with($route, 'alumnos.archivados') => ['Alumnos', 'Alumnos archivados'],
        str_starts_with($route, 'alumnos.egresados') => ['Alumnos', 'Egresados'],
        str_starts_with($route, 'alumnos.') || str_starts_with($route, 'capturista.') => ['Alumnos', str_contains($route, '.edit') ? 'Expediente' : 'Consulta'],
        str_starts_with($route, 'niveles.') || $route === 'admin.select' => ['Alumnos', 'Plataformas'],
        str_starts_with($route, 'admin.grupos.') => ['Alumnos', 'Mover alumnos'],
        $route === 'admin.cierre-escolar' => ['Alumnos', 'Cierre escolar'],
        str_starts_with($route, 'admin.assign') || str_starts_with($route, 'admin.user') => ['Administración', 'Usuarios y roles'],
        str_starts_with($route, 'admin.configuracion') => ['Administración', 'Configuración académica'],
        str_starts_with($route, 'admin.diseno') => ['Administración', 'Diseño del portal'],
        $route === 'admin.bitacoraFamilias' => ['Comunicación', 'Bitácora de familias'],
        str_starts_with($route, 'admin.historial') => ['Administración', 'Historial'],
        default => [],
    };
@endphp

@if ($secciones)
    <nav class="csl-breadcrumb" aria-label="Ruta de navegación">
        <a href="{{ route('dashboard') }}"><i class="fas fa-house"></i><span>Inicio</span></a>
        @foreach ($secciones as $seccion)
            <i class="fas fa-chevron-right csl-breadcrumb-separator" aria-hidden="true"></i>
            <span @if($loop->last) aria-current="page" @endif>{{ $seccion }}</span>
        @endforeach
    </nav>
@endif

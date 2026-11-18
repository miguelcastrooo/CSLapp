@extends('layouts.app')

@section('title', 'Respaldo del sistema | Instituto Horizonte')

@section('content')
<div class="container-xl py-4">
    <div class="csl-module-header">
        <h1>Respaldo del sistema</h1>
        <p>Protege la información antes de un cierre escolar, una actualización o una operación importante.</p>
    </div>

    <x-task-guide
        title="Respalda antes de hacer cambios importantes"
        description="Este proceso guarda una copia de la base de datos. Sólo SuperAdmin debe realizarlo."
        :steps="[
            'Confirma que nadie esté capturando información.',
            'Crea el respaldo desde phpMyAdmin o con apoyo técnico.',
            'Guarda el archivo con la fecha en una ubicación externa segura.',
            'Antes de restaurar, crea un respaldo nuevo de la información actual.'
        ]"
    />

    <div class="alert alert-warning d-flex gap-3" role="alert">
        <i class="fas fa-triangle-exclamation mt-1" aria-hidden="true"></i>
        <div><strong>Restaurar reemplaza información.</strong> Nunca restaures una copia sin guardar primero el estado actual. El sistema no restaura datos automáticamente para evitar pérdidas accidentales.</div>
    </div>

    <div class="card csl-data-card mb-4"><div class="card-body d-flex flex-column flex-md-row align-items-md-center gap-3"><span class="csl-management-icon"><i class="fas fa-shield-heart"></i></span><div class="flex-fill"><strong>Último respaldo confirmado</strong><div class="text-secondary small">@if($ultimoRespaldo){{ $ultimoRespaldo->fecha->format('d/m/Y H:i') }} por {{ $ultimoRespaldo->usuario?->name ?? 'Administración' }}@else Aún no hay un respaldo confirmado desde el sistema.@endif</div></div><form method="POST" action="{{ route('admin.respaldo.confirmar') }}" data-confirm="Confirma sólo después de descargar y guardar el archivo SQL en una ubicación externa segura." data-confirm-title="Confirmar respaldo" data-confirm-button="Sí, ya lo guardé" data-confirm-variant="primary">@csrf<button class="btn btn-primary"><i class="fas fa-check me-2"></i>Confirmar respaldo creado</button></form></div></div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card csl-data-card h-100"><div class="card-body">
                <h2 class="h3">Opción recomendada: phpMyAdmin</h2>
                <ol class="mb-0 ps-3">
                    <li>Abre <strong>phpMyAdmin</strong> desde XAMPP.</li>
                    <li>Selecciona la base de datos <strong>cslprac</strong>.</li>
                    <li>Abre la pestaña <strong>Exportar</strong>.</li>
                    <li>Elige <strong>Rápido</strong> y formato <strong>SQL</strong>.</li>
                    <li>Guarda el archivo con un nombre como <strong>cslprac-2026-11-12.sql</strong>.</li>
                </ol>
            </div></div>
        </div>
        <div class="col-lg-6">
            <div class="card csl-data-card h-100"><div class="card-body">
                <h2 class="h3">Para recuperar una copia</h2>
                <ol class="mb-0 ps-3">
                    <li>Crea primero un respaldo nuevo del estado actual.</li>
                    <li>En phpMyAdmin selecciona <strong>cslprac</strong>.</li>
                    <li>Abre <strong>Importar</strong> y selecciona el archivo SQL.</li>
                    <li>Confirma la importación sólo si revisaste la fecha del archivo.</li>
                    <li>Entra al sistema y verifica alumnos, usuarios y plataformas.</li>
                </ol>
            </div></div>
        </div>
    </div>
</div>
@endsection

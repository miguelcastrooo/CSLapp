@props([
    'icon' => 'fa-folder-open',
    'title' => 'Aún no hay información para mostrar',
    'description' => 'Cuando haya registros disponibles, aparecerán aquí.',
])

<div {{ $attributes->merge(['class' => 'csl-empty-state']) }}>
    <span class="csl-empty-state-icon"><i class="fas {{ $icon }}"></i></span>
    <div>
        <h3>{{ $title }}</h3>
        <p>{{ $description }}</p>
        @if (trim($slot))<div class="mt-3">{{ $slot }}</div>@endif
    </div>
</div>

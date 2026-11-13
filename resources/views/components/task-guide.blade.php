@props(['title' => 'Guía rápida', 'description' => null, 'steps' => []])

<div class="csl-task-guide mb-4" role="note">
    <div class="csl-task-guide-heading">
        <i class="fas fa-circle-info"></i>
        <div>
            <strong>{{ $title }}</strong>
            @if ($description)<span>{{ $description }}</span>@endif
        </div>
    </div>
    @if (count($steps))
        <ol class="csl-task-guide-steps">
            @foreach ($steps as $step)<li>{{ $step }}</li>@endforeach
        </ol>
    @endif
</div>

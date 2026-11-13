@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')

@section('title', 'Página no encontrada | Instituto Horizonte')

@section('content')
<div class="row justify-content-center py-4 py-md-5">
    <div class="col-md-9 col-lg-7 col-xl-6">
        <div class="card csl-data-card text-center">
            <div class="card-body p-4 p-md-5">
                <span class="csl-choice-icon mb-3"><i class="fas fa-map-location-dot"></i></span>
                <h1 class="h2 mb-2">No encontramos esta página</h1>
                <p class="text-secondary mb-4">Es posible que el enlace sea antiguo, el expediente ya no exista o la dirección no esté completa.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary"><i class="fas fa-house me-2"></i>Volver al panel principal</a>
            </div>
        </div>
    </div>
</div>
@endsection

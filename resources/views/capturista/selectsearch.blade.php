@extends('layouts.app')

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="container">
    <h1 class="text-center mb-4">Busqueda por Nivel Educativo</h1>
    
    <div class="row justify-content-center">
        @foreach ($niveles as $nivel)
            @php
                // Asignar un color si no está en la base de datos
                $colores = [
                    'Preescolar' => '#FF5733',
                    'Primaria Baja' => '#FFC300',
                    'Primaria Alta' => '#28B463',
                    'Secundaria' => '#1976D2'
                ];
                $color = $nivel->color ?? ($colores[$nivel->nombre] ?? '#007bff');

                // Asignar iconos para cada nivel
                $iconos = [
                    'Preescolar' => 'fas fa-child',
                    'Primaria Baja' => 'fas fa-book',
                    'Primaria Alta' => 'fas fa-graduation-cap',
                    'Secundaria' => 'fas fa-school'
                ];
                $icono = $iconos[$nivel->nombre] ?? 'fas fa-users';
            @endphp

            <div class="col-12 col-sm-6 col-md-3 d-flex align-items-stretch">
                <!-- Redirige al index filtrando por el nivel -->
                <a href="{{ route('capturista.index', ['nivel' => $nivel->id]) }}" class="btn text-white mb-3 shadow-lg w-100 p-3" 
                   style="background-color: {{ $color }}; border-radius: 10px; font-size: 1.2rem; display: flex; align-items: center; justify-content: center;">
                    <i class="{{ $icono }} fa-2x mr-2"></i> 
                    {{ $nivel->nombre }}
                </a>
            </div>
        @endforeach
    </div>

    <!-- Usando el componente de búsqueda -->
    @component('components.search')
    @endcomponent
</div>

<!-- Scripts -->
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('#search').on('keyup', function(){
        let searchTerm = $(this).val().trim(); // Usamos .trim() para eliminar espacios al inicio y al final

        // Realizamos la búsqueda solo si hay algo escrito
        if (searchTerm.length > 0) {
            $.ajax({
                url: '{{ route('alumnos.search') }}',
                method: 'GET',
                data: { search: searchTerm },
                success: function(data){
                    // Mostrar los resultados en la vista sin recargar la página
                    $('#search-results').html(data);
                },
                error: function(){
                    // Mostrar un mensaje de error si la solicitud falla
                    $('#search-results').html('<p>Error al realizar la búsqueda.</p>');
                }
            });
        } else {
            // Si no hay búsqueda, limpiar los resultados
            $('#search-results').empty();
        }
    });
});
</script>

@endsection
@endsection

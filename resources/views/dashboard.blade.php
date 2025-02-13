@extends('layouts.app')

@section('content')
    <!-- Contenedor principal con el carrusel -->
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
            <br><br><br>

                <!-- Carrusel -->
                <div id="welcomeCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="{{ asset('img/1.jpg') }}" class="d-block w-100" alt="Imagen 1">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Bienvenido a tu Dashboard</h5>
                                <p>Explora tus configuraciones y herramientas personalizadas.</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('img/2.jpg') }}" class="d-block w-100" alt="Imagen 2">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Inicia tu día con éxito</h5>
                                <p>Todo lo que necesitas está aquí para ti.</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('img/3.jpg') }}" class="d-block w-100" alt="Imagen 3">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Personaliza tu experiencia</h5>
                                <p>Haz que tu panel de control sea tuyo.</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenido del dashboard -->
        <div class="container d-flex align-items-center justify-content-center mt-5" style="height: 60vh;">
            <div class="text-center">
                <h1 class="display-4 mb-4">¡Bienvenido, {{ Auth::user()->name }}!</h1>
                <p>Comienza a explorar y gestionar tu panel de control.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Ajuste para el carrusel */
        .carousel-inner img {
            max-height: 250px; /* Reducir la altura máxima de las imágenes */
            object-fit: cover; /* Asegura que la imagen cubra el área sin distorsionarse */
            width: 100%; /* Asegura que la imagen ocupe todo el ancho disponible */
        }

        /* Estilo para el contenedor del carrusel */
        .carousel {
            max-width: 800px; /* Limitar el ancho máximo del carrusel */
            margin: 0 auto; /* Centrar el carrusel */
        }

        /* Estilo para las leyendas en el carrusel */
        .carousel-caption {
            background-color: rgba(0, 0, 0, 0.5); /* Fondo semitransparente para mejorar la visibilidad del texto */
            border-radius: 5px;
            padding: 10px;
        }
    </style>
@endpush

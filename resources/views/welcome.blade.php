@extends('layouts.guest')

@section('title', 'Bienvenido')

@section('styles')
    <style>
        .bienvenido {
            color: black; /* Cambiar el color de "Bienvenido" a negro */
        }

        .main-section {
            background-color: #f8f9fa; /* Fondo suave debajo de la navbar */
            padding: 40px 0; /* Espaciado para que el contenido no quede pegado al borde */
            border-radius: 8px; /* Bordes redondeados */
        }

        .btn-custom {
            background-color: #3E8E7E;
            border-color: #3E8E7E;
        }

        .btn-custom:hover {
            background-color: #098FC3;
            border-color: #098FC3;
        }

        .intro-text {
            font-size: 1.25rem;
            color: #333;
        }

        .carousel-container {
            margin-top: 30px; /* Espacio entre el texto y el carrusel */
        }

        .carousel-inner img {
            max-height: 400px; /* Limitar la altura máxima de las imágenes */
            object-fit: cover; /* Ajustar el contenido de la imagen dentro del tamaño */
        }
    </style>
@endsection

@section('content')
    <div class="text-center">
        <h1 class="display-4 bienvenido">¡Bienvenido!</h1>
    </div>

    <!-- Carrusel -->
    <div class="carousel-container">
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('img/1.jpg') }}" class="d-block w-100" alt="Imagen 1">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('img/2.jpg') }}" class="d-block w-100" alt="Imagen 2">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('img/3.jpg') }}" class="d-block w-100" alt="Imagen 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    </div>
@endsection

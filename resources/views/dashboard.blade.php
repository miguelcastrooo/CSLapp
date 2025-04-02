@extends('layouts.app')

@section('content')
    <!-- Contenedor principal con el carrusel -->
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
            <br><br><br>

  <!-- Carrusel -->
<div class="container carousel-container">
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('img/5.jpg') }}" class="d-block w-100" alt="Campus moderno">
                <div class="carousel-caption">
                    <h5>Campus moderno</h5>
                    <p>Infraestructura de vanguardia para nuestros estudiantes.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/6.jpg') }}" class="d-block w-100" alt="Aulas equipadas">
                <div class="carousel-caption">
                    <h5>Aulas equipadas</h5>
                    <p>Espacios diseñados para el aprendizaje interactivo.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('img/7.jpg') }}" class="d-block w-100" alt="Actividades extracurriculares">
                <div class="carousel-caption">
                    <h5>Actividades extracurriculares</h5>
                    <p>Fomentamos el desarrollo integral de nuestros estudiantes.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>
</div>

<style>
    /* Cambiar color de texto a negro en los elementos del carrusel */
    .carousel-caption h5, 
    .carousel-caption p {
        color: black !important;
    }
</style>


        <!-- Contenido del dashboard -->
        <div class="container d-flex align-items-center justify-content-center mt-5" style="height: 60vh;">
            <div class="text-center">
                <strong><h1 class="display-4 mb-4">¡Bienvenido, {{ Auth::user()->name }}!</h1></strong>
                <p>Comienza a explorar y gestionar tu panel de control.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
    /* Aseguramos que las imágenes tengan el 100% de ancho y 400px de altura */
    .carousel-inner img {
        width: 100% !important; /* Asegura que las imágenes ocupen el 100% del ancho disponible */
        height: 400px !important; /* Establece una altura fija de 400px */
        object-fit: cover !important; /* Hace que las imágenes cubran el área sin distorsionarse */
    }
    </style>
@endpush

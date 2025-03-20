@extends('layouts.guest')

@section('title', 'CSL')

@section('styles')
    <style>
      body {
            background: linear-gradient(to right,rgb(255, 255, 255),rgb(218, 236, 23),rgb(44, 100, 51));
            color: white;
        }

        .hero-section {
            position: relative;
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            background: url('{{ asset('img/school-bg.jpg') }}') center/cover no-repeat;
            background-blend-mode: darken;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 50px 20px;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: bold;
        }

        .hero-section p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: auto;
        }

        .btn-custom {
            background-color: #3E8E7E;
            border: none;
            padding: 12px 25px;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: 0.3s ease-in-out;
            margin-top: 20px;
        }

        .btn-custom:hover {
            background-color: #098FC3;
        }

        .carousel-container {
            margin-top: 40px;
        }

        .carousel-item img {
            max-height: 500px;
            object-fit: cover;
            border-radius: 12px;
            filter: brightness(85%);
        }

        .carousel-caption {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 8px;
            padding: 10px;
        }

    </style>
@endsection

@section('content')

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
<br><br><br>
@endsection

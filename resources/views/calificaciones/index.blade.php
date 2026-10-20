@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4"><i class="fas fa-chart-bar"></i> Dashboard de Calificaciones</h3>

    <!-- Filtros dinámicos -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-filter"></i> Filtrar por Nivel, Grado, Sección</h5>

            <div class="row">
                <div class="col-md-4">
                    <strong>Niveles:</strong><br>
                    @foreach($niveles as $nivel)
                        <div class="form-check">
                            <input class="form-check-input filtro-nivel" type="checkbox" value="{{ $nivel->id }}" id="nivel{{ $nivel->id }}">
                            <label class="form-check-label" for="nivel{{ $nivel->id }}">{{ $nivel->nombre }}</label>
                        </div>
                    @endforeach
                </div>

                <div class="col-md-4">
                    <strong>Grados:</strong><br>
                    <div id="contenedor-grados"></div>
                </div>

                <div class="col-md-4">
                    <strong>Secciones:</strong><br>
                    <div id="contenedor-secciones"></div>
                </div>
            </div>

            <hr>
            <div class="mt-2">
                <label for="buscador-alumno" class="form-label">Buscar alumno:</label>
                <input type="text" class="form-control" id="buscador-alumno" placeholder="Nombre o matrícula...">
            </div>
        </div>
    </div>

    <!-- Gráfica general -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <strong>Promedios generales por plataforma</strong>
        </div>
        <div class="card-body">
            <canvas id="graficaPlataformas" height="100"></canvas>
        </div>
    </div>

    <!-- Detalle del alumno -->
    <div id="detalle-alumno" class="card d-none shadow-sm">
        <div class="card-header bg-light" id="titulo-detalle">
            Detalles del Alumno
        </div>
        <div class="card-body">
            <canvas id="graficaAlumno" height="120"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const promedios = @json($promedios);
    const niveles = @json($niveles);

    const ctxGeneral = document.getElementById('graficaPlataformas').getContext('2d');
    new Chart(ctxGeneral, {
        type: 'bar',
        data: {
            labels: promedios.map(p => p.nombre),
            datasets: [{
                label: 'Promedio',
                data: promedios.map(p => p.promedio),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });

    let alumnoChart = null;

    const contGrados = document.getElementById('contenedor-grados');
    const contSecciones = document.getElementById('contenedor-secciones');
    const buscador = document.getElementById('buscador-alumno');

    // Manejar selección de niveles
    document.querySelectorAll('.filtro-nivel').forEach(chk => {
        chk.addEventListener('change', function () {
            const nivelesSeleccionados = Array.from(document.querySelectorAll('.filtro-nivel:checked')).map(c => c.value);
            fetch(`/calificaciones/grados-por-niveles`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ niveles: nivelesSeleccionados })
            })
            .then(res => res.json())
            .then(data => {
                contGrados.innerHTML = '';
                data.grados.forEach(grado => {
                    const id = `grado${grado.id}`;
                    contGrados.innerHTML += `
                        <div class="form-check">
                            <input class="form-check-input filtro-grado" type="checkbox" value="${grado.id}" id="${id}">
                            <label class="form-check-label" for="${id}">${grado.nombre}</label>
                        </div>
                    `;
                });
                contSecciones.innerHTML = '';
            });
        });
    });

    // Manejar selección de grados
    contGrados.addEventListener('change', function () {
        const gradosSeleccionados = Array.from(contGrados.querySelectorAll('.filtro-grado:checked')).map(c => c.value);
        fetch(`/calificaciones/secciones-por-grados`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ grados: gradosSeleccionados })
        })
        .then(res => res.json())
        .then(data => {
            contSecciones.innerHTML = '';
            data.secciones.forEach(seccion => {
                const id = `seccion${seccion.id}`;
                contSecciones.innerHTML += `
                    <div class="form-check">
                        <input class="form-check-input filtro-seccion" type="checkbox" value="${seccion.id}" id="${id}">
                        <label class="form-check-label" for="${id}">${seccion.nombre}</label>
                    </div>
                `;
            });
        });
    });

    // Buscar alumno
    buscador.addEventListener('input', function () {
        const termino = this.value.trim();
        const seccionesSeleccionadas = Array.from(document.querySelectorAll('.filtro-seccion:checked')).map(c => c.value);

        if (termino.length < 3 || seccionesSeleccionadas.length === 0) return;

        fetch(`/calificaciones/buscar-alumno`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ secciones: seccionesSeleccionadas, termino: termino })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.alumno || data.plataformas.length === 0) {
                document.getElementById('detalle-alumno').classList.add('d-none');
                return;
            }

            document.getElementById('detalle-alumno').classList.remove('d-none');
            document.getElementById('titulo-detalle').innerText = `Detalle de ${data.alumno}`;

            const labelsAlumno = data.plataformas.map(p => p.nombre);
            const califs = data.plataformas.map(p => p.calificacion);

            if (alumnoChart) alumnoChart.destroy();

            const ctxAlumno = document.getElementById('graficaAlumno').getContext('2d');
            alumnoChart = new Chart(ctxAlumno, {
                type: 'radar',
                data: {
                    labels: labelsAlumno,
                    datasets: [{
                        label: 'Calificaciones',
                        data: califs,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        r: {
                            suggestedMin: 0,
                            suggestedMax: 100
                        }
                    }
                }
            });
        });
    });
});
</script>
@endpush

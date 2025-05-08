<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DATOS DE ACCESO A PLATAFORMAS DIGITALES</title>
    <!-- Cargar Bootstrap y FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .header {
            background-color: #28a745;  /* Verde */
            color: rgb(0, 0, 0);  /* Negro */
            padding: 40px;
            position: relative;
            border-radius: 50% 50% 0 0;  /* Hacer el fondo verde con borde superior redondeado */
            margin-bottom: 30px;  /* Espacio debajo del header */
            text-align: center;
        }

        .header img {
            max-width: 150px;
            margin-top: -50px; /* Subir la imagen para que quede debajo del arco */
        }

        .header h2 {
            margin-top: 10px;
            font-size: 24px;
            color: rgb(0, 0, 0); /* Amarillo */
        }

        .section p {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .platform-table th {
            background-color: #f1c40f;  /* Amarillo */
            color: #000000;  /* Negro */
            text-align: center;
            padding: 10px;
        }

        .platform-table td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .platform-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .platform-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .platform-table td {
            font-size: 14px;
        }

        .btn-back {
            margin-top: 20px;
            display: block;
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container mt-4">
        <div class="header">
            <!-- Logo de la escuela -->
            <img src="data:image/webp;base64,{{ base64_encode(file_get_contents(public_path('img/sanluis.webp'))) }}" alt="Logo Escuela" class="img-fluid">
            <h2>DATOS DE ACCESO A PLATAFORMAS DIGITALES</h2>
        </div>

        <div class="section mt-4">
            <p><strong>Matricula:</strong> {{ $alumno->matricula }}</p>
            <p><strong>Nombre Completo:</strong> {{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</p>
            <p><strong>Nivel Educativo:</strong> {{ $alumno->nivelEducativo->nombre ?? 'N/A' }}</p>
            <p><strong>Grado y Sección:</strong> {{ $alumno->grado->nombre ?? 'N/A' }} {{ $alumno->seccion }}</p>
        </div>

        <table class="table table-bordered platform-table mx-auto">
            <thead>
                <tr>
                    <th>Plataforma</th>
                    <th>Usuario</th>
                    <th>Contraseña</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumno->plataformas as $plataforma)
                    <tr>
                        <td>{{ $plataforma->nombre ?? 'N/A' }}</td>
                        <td>{{ $plataforma->pivot->usuario ?? 'N/A' }}</td>
                        <td>{{ $plataforma->pivot->contraseña ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

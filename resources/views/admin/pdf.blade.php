<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DATOS DE ACCESO A PLATAFORMAS DIGITALES</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/fontawesome.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .header {
            background-color: #f39c12;
            color: #fff;
            padding: 20px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .header .icon {
            font-size: 50px;
            color: #000;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 15px;
        }
        .platform-table {
            width: 100%;
            margin-top: 20px;
        }
        .platform-table th {
            background-color: #2ecc71;
            color: #fff;
            text-align: center;
        }
        .platform-table td {
            text-align: center;
            background-color: #fff;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .platform-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div class="container mt-4">
        <div class="header">
            <i class="fas fa-graduation-cap icon"></i>
            <h2>DATOS DE ACCESO A PLATAFORMAS</h2>
        </div>

        <div class="row mt-4">
            <div class="col-md-8">
                <div class="section">
                    <p><strong>Matricula:</strong> {{ $alumno->matricula }}</p>
                    <p><strong>Nombre Completo:</strong> {{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</p>
                    <p><strong>Nivel Educativo:</strong> {{ $alumno->nivelEducativo->nombre ?? 'N/A' }}</p>
                    <p><strong>Grado:</strong> {{ $alumno->grado->nombre ?? 'N/A' }}</p>
                    <p><strong>Sección:</strong> {{ $alumno->seccion }}</p>
                </div>
            </div>
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

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

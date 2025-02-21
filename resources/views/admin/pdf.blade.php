<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DATOS DE ACCESO A PLATAFORMAS DIGITALES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
            background-color: #f39c12; /* Amarillo */
            color: #fff;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            position: relative;
        }
        .logo {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 40px;
            color: #fff;
        }
        .section {
            margin-bottom: 15px;
        }
        .platform-table th, .platform-table td {
            vertical-align: middle;
        }
        .platform-table td {
            padding: 8px 12px;
        }
        .platform-table th {
            background-color: #2ecc71; /* Verde */
            color: #fff;
        }
        .platform-table tr:nth-child(even) {
            background-color: #f2f2f2; /* Gris claro */
        }
        .platform-table tr:nth-child(odd) {
            background-color: #fff; /* Blanco */
        }
    </style>
</head>
<body>

    <div class="container mt-4">
        <div class="header">
            <h2>DATOS DE ACCESO A PLATAFORMAS DIGITALES</h2>
            <i class="fas fa-user-graduate logo"></i> <!-- Ejemplo de ícono, reemplázalo con tu logo -->
        </div>

        <div class="row mt-4">
            <div class="col-md-8">
                <div class="section">
                    <p><strong>Nombre Completo:</strong> {{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</p>
                    <p><strong>Grado:</strong> {{ $alumno->grado->nombre ?? 'N/A' }}</p>
                    <p><strong>Sección:</strong> {{ $alumno->seccion }}</p>
                </div>
            </div>
        </div>

        <!-- Tabla de plataformas -->
        <table class="table table-bordered platform-table mt-4 mx-auto" style="width: 80%;">
            <thead>
                <tr>
                    <th>Plataforma</th>
                    <th>Usuario</th>
                    <th>Contraseña</th>
                </tr>
            </thead>
            <tbody>
                @if($alumno->nivelEducativo->nombre == 'Preescolar')
                    <tr>
                        <td>Moodle</td>
                        <td>{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>ClassRoom</td>
                        <td>{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                    </tr>

                @elseif($alumno->nivelEducativo->nombre == 'Primaria Baja')
                    <tr>
                        <td>Moodle</td>
                        <td>{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>ClassRoom</td>
                        <td>{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>HMH</td>
                        <td>{{ $alumno->usuario_hmh ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_hmh ?? 'N/A' }}</td>
                    </tr>

                @elseif($alumno->nivelEducativo->nombre == 'Primaria Alta')
                    <tr>
                        <td>Moodle</td>
                        <td>{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>ClassRoom</td>
                        <td>{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>HMH</td>
                        <td>{{ $alumno->usuario_hmh ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_hmh ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Mathletics</td>
                        <td>{{ $alumno->usuario_mathletics ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_mathletics ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Progrentis</td>
                        <td>{{ $alumno->usuario_progrentis ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_progrentis ?? 'N/A' }}</td>
                    </tr>

                @elseif($alumno->nivelEducativo->nombre == 'Secundaria')
                    <tr>
                        <td>Moodle</td>
                        <td>{{ $alumno->usuario_moodle ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_moodle ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>ClassRoom</td>
                        <td>{{ $alumno->usuario_classroom ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_classroom ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Mathletics</td>
                        <td>{{ $alumno->usuario_mathletics ?? 'N/A' }}</td>
                        <td>{{ $alumno->contraseña_mathletics ?? 'N/A' }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

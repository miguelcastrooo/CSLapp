<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Acceso a Plataformas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            margin: auto;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            margin-top: 30px;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            color: #888;
            margin-top: 30px;
        }
        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        .list-group-item {
            padding-left: 20px;
        }
        h3 {
            margin-bottom: 15px;
        }
        .warning {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Logo de la escuela usando CID -->
            <img src="cid:logo-csl" alt="Logo Escuela" style="max-width: 150px;">
            <h2>Datos de las Plataformas</h2>
        </div>

        <div class="text-center my-5">
            <h1 class="text-dark">Estimado/a {{ $familiar->nombre }} {{ $familiar->apellido_paterno }} {{ $familiar->apellido_materno }}:</h1>
            <p class="lead">Adjunto a este correo encontrará el documento con los datos de acceso a las plataformas digitales de su hijo/a <strong>{{ $alumno->nombre }} {{ $alumno->apellido_paterno }} {{ $alumno->apellido_materno }}</strong>.</p>
            <p>Este documento incluye la información necesaria para acceder a las plataformas digitales utilizadas en el ámbito educativo.</p>

            <p class="lead">Si tiene alguna duda o necesita asistencia adicional, no dude en contactarnos.</p>

            <p>Adjunto el documento con los datos de acceso. Haga clic en el siguiente enlace para descargarlo:</p>
            <a href="cid:credenciales_alumno.pdf" class="btn btn-primary">Descargar PDF</a>

            <div class="mt-4">
                <p>Saludos cordiales,</p>
                <p><strong>La Administración Escolar</strong></p>
            </div>

            <hr>

            <p class="text-muted">Este es un mensaje automático, por favor no responda a este correo.</p>
        </div>
    </div>
</body>
</html>

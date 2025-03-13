<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Credenciales - Control Escolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <img src="{{ asset('img/san-luis_512%20(1).webp') }}" alt="Logo Escuela" class="img-fluid" style="max-width: 150px;">
            <h2>Registro de Alumno</h2>
        </div>

        <div class="content">
            <p>Estimados Coordinadores,</p>

            <p>El área de Control Escolar ha actualizado la información de las credenciales del siguiente alumno. Ahora es su turno de verificar y ajustar la asignación de la sección y las credenciales correspondientes.</p>

            <h3>Información Actualizada del Alumno:</h3>
            <ul class="list-group">
                <li class="list-group-item"><strong>Matricula:</strong> {{ $alumno->matricula }}</li>
                <li class="list-group-item"><strong>Nombre Completo:</strong> {{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</li>
                <li class="list-group-item"><strong>Grado:</strong> {{ $alumno->grado->nombre }}</li>
                <li class="list-group-item"><strong>Nivel Educativo:</strong> {{ $alumno->nivelEducativo->nombre }}</li>
                <li class="list-group-item"><strong>Fecha de Inscripción:</strong> {{ \Carbon\Carbon::parse($alumno->fecha_inscripcion)->format('d \d\e F, Y') }}</li>
                <li class="list-group-item"><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($alumno->fecha_inicio)->format('d \d\e F, Y') }}</li>
            </ul>

            @if($contactos->isNotEmpty())
                <h3>Contactos de Emergencia:</h3>
                <div class="list-group">
                    @foreach ($contactos as $contacto)
                        <div class="list-group-item">
                            <h5 class="mb-1"><strong>Nombre:</strong> {{ $contacto->nombre }}</h5>
                            <p class="mb-1"><strong>Teléfono:</strong> {{ $contacto->telefono }}</p>
                            <p><strong>Correo:</strong> {{ $contacto->correo }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p>No se han registrado contactos de emergencia.</p>
            @endif

            <h3>Plataformas Asignadas:</h3>
            <ul class="list-group">
                @foreach ($alumno->alumnoPlataforma as $plataforma)
                    <li class="list-group-item">
                        @if($plataforma->usuario && $plataforma->contraseña)
                            ✅ {{ $plataforma->plataforma->nombre }}: {{ $plataforma->usuario }} / {{ $plataforma->contraseña }}
                        @else
                            ⚠️ {{ $plataforma->plataforma->nombre }}: Usuario o Contraseña faltante
                        @endif
                    </li>
                @endforeach

                @if(empty($alumno->grado->seccion))
                    <li class="list-group-item">⚠️ Sección pendiente por asignar.</li>
                @endif
            </ul>

            <h3>Indicaciones para la Asignación:</h3>
            <ul class="list-group">
                <li class="list-group-item"><strong>Profesor 1:</strong> Verificar la sección y asignarla en el sistema.</li>
                <li class="list-group-item"><strong>Profesor 2:</strong> Confirmar los accesos a HMH, Mathletics y Progrentis según lo indicado.</li>
            </ul>

            <p>Por favor, revisen la información actualizada y asegúrense de completar el proceso lo antes posible para garantizar que el alumno tenga acceso a todos los recursos educativos.</p>

            <p>Gracias por su colaboración. Si tienen dudas, pueden contactarnos.</p>
        </div>
    </div>

</body>
</html>

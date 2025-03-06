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
            <h2>Actualización de Alumno</h2>
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
                <li class="list-group-item"><strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($alumno->fecha_inscripcion)->format('d \d\e F, Y') }}</li>
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

            <h3>Información Pendiente de Asignación:</h3>
                <ul class="list-group">
                    <li class="list-group-item">
                        @if(!empty($alumno->seccion))
                            ✅ <strong>Sección:</strong> {{ $alumno->seccion }}
                        @else
                            ⚠️ <strong>Sección pendiente por asignar.</strong>
                        @endif
                    </li>
                </ul>

                <h3>Plataformas Asignadas:</h3>
                <ul class="list-group">
                    <li class="list-group-item">✅ Classroom</li>
                    <li class="list-group-item">✅ Moodle</li>

                    @if($alumno->nivelEducativo->nombre == 'Primaria Baja')
                        <li class="list-group-item">
                            @if($alumno->usuario_hmh && $alumno->contraseña_hmh)
                                ✅ HMH: {{ $alumno->usuario_hmh }} / {{ $alumno->contraseña_hmh }}
                            @else
                                ⚠️ HMH: Usuario o Contraseña faltante
                            @endif
                        </li>
                    @endif

                    @if($alumno->nivelEducativo->nombre == 'Primaria Alta')
                        <li class="list-group-item">
                            @if($alumno->usuario_hmh && $alumno->contraseña_hmh)
                                ✅ HMH: {{ $alumno->usuario_hmh }} / {{ $alumno->contraseña_hmh }}
                            @else
                                ⚠️ HMH: Usuario o Contraseña faltante
                            @endif
                        </li>
                        <li class="list-group-item">
                            @if($alumno->usuario_mathletics && $alumno->contraseña_mathletics)
                                ✅ Mathletics: {{ $alumno->usuario_mathletics }} / {{ $alumno->contraseña_mathletics }}
                            @else
                                ⚠️ Mathletics: Usuario o Contraseña faltante
                            @endif
                        </li>
                        <li class="list-group-item">
                            @if($alumno->usuario_progrentis && $alumno->contraseña_progrentis)
                                ✅ Progrentis: {{ $alumno->usuario_progrentis }} / {{ $alumno->contraseña_progrentis }}
                            @else
                                ⚠️ Progrentis: Usuario o Contraseña faltante
                            @endif
                        </li>
                    @endif

                    @if($alumno->nivelEducativo->nombre == 'Secundaria')
                        <li class="list-group-item">
                            @if($alumno->usuario_mathletics && $alumno->contraseña_mathletics)
                                ✅ Mathletics: {{ $alumno->usuario_mathletics }} / {{ $alumno->contraseña_mathletics }}
                            @else
                                ⚠️ Mathletics: Usuario o Contraseña faltante
                            @endif
                        </li>
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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciales del Alumno</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .section { margin-bottom: 15px; }
        .divider { border-top: 1px solid #000; margin: 10px 0; }
    </style>
</head>
<body>

    <div class="section">
        <p><strong>Matrícula:</strong> {{ $alumno->matricula }}</p>
        <p><strong>Nombre Completo:</strong> {{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</p>
        <p><strong>Nivel Educativo:</strong> {{ $alumno->nivelEducativo->nombre }}</p>
        <p><strong>Grado:</strong> {{ $alumno->grado->nombre ?? 'N/A' }}</p>
        <p><strong>Sección:</strong> {{ $alumno->seccion }}</p>
    </div>

    <div class="section">
        @foreach($plataformas as $plataforma)
            <p><strong>Plataforma:</strong> {{ $plataforma }}</p>
            <p><strong>Usuario:</strong> {{ $alumno->plataformas->where('nombre', $plataforma)->first()->pivot->usuario ?? 'N/A' }}</p>
            <p><strong>Contraseña:</strong> {{ $alumno->plataformas->where('nombre', $plataforma)->first()->pivot->contraseña ?? 'N/A' }}</p>
            <div class="divider"></div>
        @endforeach
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataformas - {{ $nivel }}</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Plataformas para el nivel: {{ $nivel }}</h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($plataformas as $plataforma)
                <tr>
                    <td>{{ $plataforma->nombre }}</td>
                    <td>{{ $plataforma->descripcion ?? 'Sin descripción' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

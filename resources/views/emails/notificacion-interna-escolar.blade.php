<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
</head>
<body style="background:#f3f6fa;color:#243442;font-family:Arial,sans-serif;margin:0;padding:24px;">
    <table role="presentation" style="background:#ffffff;border-collapse:collapse;border-radius:10px;max-width:620px;overflow:hidden;width:100%;">
        <tr><td style="background:#24536d;color:#ffffff;padding:20px 24px;"><strong style="font-size:18px;">{{ $configuracionInstitucional->nombre }}</strong><br><span style="font-size:12px;opacity:.9;">{{ $configuracionInstitucional->subtitulo }}</span></td></tr>
        <tr><td style="padding:24px;">
            <h1 style="color:#183444;font-size:20px;margin:0 0 10px;">{{ $titulo }}</h1>
            <p style="line-height:1.5;margin:0 0 18px;">{{ $mensaje }}</p>
            <table role="presentation" style="border-collapse:collapse;width:100%;">
                @foreach($detalles as $etiqueta => $valor)
                    <tr>
                        <td style="border-top:1px solid #e2e8ef;color:#526474;font-size:12px;padding:10px 8px 10px 0;vertical-align:top;width:35%;">{{ $etiqueta }}</td>
                        <td style="border-top:1px solid #e2e8ef;font-size:13px;padding:10px 0;vertical-align:top;">{{ $valor }}</td>
                    </tr>
                @endforeach
            </table>
            <p style="color:#526474;font-size:12px;line-height:1.45;margin:20px 0 0;">Este aviso no incluye contraseñas ni datos familiares. Consulta el expediente únicamente si tu rol tiene autorización.</p>
        </td></tr>
    </table>
</body>
</html>

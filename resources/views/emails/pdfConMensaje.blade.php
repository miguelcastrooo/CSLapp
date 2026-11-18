<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{{ $asunto }}</title></head>
<body style="background:#f3f6fa;color:#183444;font-family:Arial,Helvetica,sans-serif;margin:0;padding:24px 12px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td align="center">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background:#fff;border:1px solid #d6e0ea;border-radius:14px;max-width:600px;overflow:hidden;width:100%;">
<tr><td style="background:{{ $configuracionInstitucional->color_primario }};padding:28px 30px;text-align:center;">
@if($tieneLogotipo)<img src="cid:logo-institucional" alt="Logotipo de {{ $configuracionInstitucional->nombre }}" width="70" style="background:#fff;border-radius:12px;display:block;height:70px;margin:0 auto 15px;object-fit:contain;padding:5px;width:70px;">@endif
<div style="color:#fff;font-size:20px;font-weight:bold;line-height:1.25;">{{ $configuracionInstitucional->nombre }}</div>
<div style="color:#d9ebf5;font-size:11px;letter-spacing:1px;margin-top:5px;text-transform:uppercase;">{{ $configuracionInstitucional->subtitulo }}</div>
<div style="border-top:1px solid rgba(255,255,255,.35);margin:18px auto 0;max-width:270px;padding-top:16px;"><span style="color:#fff;font-size:16px;font-weight:bold;">Accesos digitales del alumno</span></div>
</td></tr>
<tr><td style="padding:30px;">
<p style="font-size:20px;font-weight:bold;line-height:1.35;margin:0 0 15px;">Hola, {{ $familiar->nombre }} {{ $familiar->apellido_paterno }}:</p>
<p style="color:#405867;font-size:15px;line-height:1.6;margin:0 0 18px;">Te compartimos el documento con los datos de acceso a las plataformas digitales de:</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#edf7f5;border:1px solid #c7e3dd;border-radius:10px;margin:0 0 20px;"><tr><td style="padding:16px 18px;"><div style="color:#176b62;font-size:11px;font-weight:bold;letter-spacing:.7px;text-transform:uppercase;">Alumno</div><div style="color:#183444;font-size:18px;font-weight:bold;margin-top:4px;">{{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</div><div style="color:#526474;font-size:13px;margin-top:4px;">Matrícula: {{ $alumno->matricula }}</div></td></tr></table>
<p style="color:#405867;font-size:15px;line-height:1.6;margin:0 0 16px;">El PDF adjunto contiene los usuarios y contraseñas de las plataformas asignadas. Guárdalo en un lugar seguro y evita reenviarlo a personas no autorizadas.</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fff7df;border-left:4px solid #d59a00;margin:0 0 22px;"><tr><td style="color:#69520d;font-size:13px;line-height:1.5;padding:12px 14px;"><strong>Importante:</strong> las credenciales son personales y deben compartirse únicamente con la familia responsable.</td></tr></table>
<p style="color:#405867;font-size:15px;line-height:1.6;margin:0;">Si necesitas apoyo, comunícate con la administración escolar.</p>
<p style="font-size:15px;line-height:1.6;margin:24px 0 0;">Saludos cordiales,<br><strong>{{ $configuracionInstitucional->nombre }}</strong></p>
</td></tr>
<tr><td style="background:#f7fafc;border-top:1px solid #d6e0ea;color:#617582;font-size:11px;line-height:1.5;padding:15px 24px;text-align:center;">Mensaje automático del portal académico. Para proteger la información del alumno, no reenvíes el archivo adjunto.</td></tr>
</table>
</td></tr></table>
</body>
</html>

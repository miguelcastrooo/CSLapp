<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Accesos digitales - {{ $alumno->matricula }}</title>
    <style>
        @page { margin: 28px 32px; }
        * { box-sizing: border-box; }
        body { color: #243442; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.45; margin: 0; }
        .header { background: #24536d; border-radius: 8px; color: #ffffff; padding: 18px 20px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .brand-icon { background: #ffffff; border-radius: 6px; color: #176b62; font-size: 19px; font-weight: bold; padding: 8px 12px; text-align: center; width: 42px; }
        .brand-name { font-size: 16px; font-weight: bold; margin: 0; }
        .brand-subtitle { color: #d9ebf5; font-size: 9px; margin: 3px 0 0; text-transform: uppercase; }
        .document-label { color: #d9ebf5; font-size: 9px; text-align: right; }
        .document-title { font-size: 14px; font-weight: bold; margin: 3px 0 0; text-align: right; }
        .section-title { color: #176b62; font-size: 11px; font-weight: bold; margin: 22px 0 8px; text-transform: uppercase; }
        .student-card { background: #f2f7f8; border: 1px solid #d4e3e5; border-radius: 6px; padding: 12px 14px; }
        .student-name { color: #183444; font-size: 15px; font-weight: bold; margin: 0 0 9px; }
        .details { width: 100%; border-collapse: collapse; }
        .details td { padding: 3px 0; vertical-align: top; width: 50%; }
        .label { color: #526474; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .value { color: #243442; font-size: 10px; font-weight: normal; }
        .platform-table { border-collapse: collapse; margin-top: 5px; width: 100%; }
        .platform-table th { background: #24536d; color: #ffffff; font-size: 9px; padding: 9px 10px; text-align: left; }
        .platform-table td { border-bottom: 1px solid #d8e1e8; padding: 10px; vertical-align: middle; }
        .platform-table tr:nth-child(even) td { background: #f7fafb; }
        .empty { background: #fff7df; border: 1px solid #ead28d; border-radius: 5px; color: #6f5510; padding: 10px; }
        .note { background: #eef5fb; border-left: 4px solid #4b83b8; color: #36536c; margin-top: 18px; padding: 9px 11px; }
        .footer { border-top: 1px solid #d8e1e8; color: #6b7d8c; font-size: 8px; margin-top: 28px; padding-top: 9px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table"><tr>
            <td style="width: 48px;"><div class="brand-icon">IH</div></td>
            <td><p class="brand-name">Instituto Horizonte</p><p class="brand-subtitle">Portal academico</p></td>
            <td><p class="document-label">CREDENCIALES DEL ALUMNO</p><p class="document-title">Accesos digitales</p></td>
        </tr></table>
    </div>

    <p class="section-title">Datos del alumno</p>
    <div class="student-card">
        <p class="student-name">{{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</p>
        <table class="details"><tr>
            <td><span class="label">Matricula</span><br><span class="value">{{ $alumno->matricula }}</span></td>
            <td><span class="label">Nivel educativo</span><br><span class="value">{{ $alumno->nivelEducativo->nombre ?? 'No registrado' }}</span></td>
        </tr><tr>
            <td><span class="label">Grado</span><br><span class="value">{{ $alumno->grado->nombre ?? 'No registrado' }}</span></td>
            <td><span class="label">Seccion</span><br><span class="value">{{ $alumno->seccion ?? 'No registrada' }}</span></td>
        </tr></table>
    </div>

    <p class="section-title">Plataformas asignadas</p>
    @if($alumno->plataformas->isNotEmpty())
        <table class="platform-table">
            <thead><tr><th style="width: 28%;">Plataforma</th><th style="width: 36%;">Usuario</th><th style="width: 36%;">Contraseña</th></tr></thead>
            <tbody>
                @foreach ($alumno->plataformas as $plataforma)
                    <tr><td><strong>{{ $plataforma->nombre ?? 'No registrada' }}</strong></td><td>{{ $plataforma->pivot->usuario ?? 'No asignado' }}</td><td>{{ $plataforma->pivot->contraseña ?? 'No asignada' }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">Este alumno no tiene plataformas asignadas actualmente.</div>
    @endif

    <div class="note">Conserva este documento en un lugar seguro. Las credenciales son personales y deben compartirse unicamente con la familia responsable.</div>
    <div class="footer">Documento generado el {{ now()->format('d/m/Y H:i') }} - Instituto Horizonte</div>
</body>
</html>

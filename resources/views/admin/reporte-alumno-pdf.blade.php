<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 34px 38px; }
        body { color:#203746; font-family: DejaVu Sans, sans-serif; font-size:10px; line-height:1.45; }
        h1 { color:#183f59; font-size:20px; margin:0 0 3px; }
        h2 { background:#eaf4f2; border-left:4px solid #176b62; color:#183f59; font-size:12px; margin:18px 0 8px; padding:6px 8px; }
        .muted { color:#667b88; } .header { border-bottom:2px solid #176b62; padding-bottom:12px; }
        .student { background:#f4f8fa; border:1px solid #d7e4e9; margin-top:12px; padding:12px; }
        table { border-collapse:collapse; width:100%; } th { background:#24536d; color:#fff; font-size:9px; text-align:left; } th,td { border:1px solid #d7e4e9; padding:6px; vertical-align:top; }
        .two-col td { border:0; padding:3px 8px 3px 0; width:50%; } .label { color:#667b88; font-size:8px; text-transform:uppercase; }
        .footer { border-top:1px solid #d7e4e9; color:#667b88; font-size:8px; margin-top:20px; padding-top:8px; }
    </style>
</head>
<body>
    <div class="header"><h1>{{ $configuracionInstitucional->nombre }}</h1><div class="muted">Reporte de expediente escolar · Emitido el {{ now()->format('d/m/Y H:i') }}</div></div>
    <div class="student"><strong style="font-size:16px;">{{ $alumno->nombre }} {{ $alumno->apellidopaterno }} {{ $alumno->apellidomaterno }}</strong><br><span class="muted">Matrícula: {{ $alumno->matricula }} · {{ $alumno->grado?->nombre ?? 'Sin grado' }} · Sección {{ $alumno->seccion ?: 'Sin sección' }} · {{ $alumno->nivelEducativo?->nombre ?? 'Sin nivel' }}</span></div>

    <h2>Datos del alumno</h2>
    <table class="two-col"><tr><td><span class="label">Fecha de nacimiento</span><br>{{ $alumno->fecha_nacimiento ? \Illuminate\Support\Carbon::parse($alumno->fecha_nacimiento)->format('d/m/Y') : 'No registrada' }}</td><td><span class="label">Sexo</span><br>{{ $alumno->sexo ?: 'No registrado' }}</td></tr><tr><td><span class="label">Domicilio</span><br>{{ collect([$alumno->domicilio, $alumno->no_domicilio, $alumno->colonia, $alumno->ciudad, $alumno->estado, $alumno->cp])->filter()->implode(', ') ?: 'No registrado' }}</td><td><span class="label">Inscripción</span><br>{{ $alumno->fecha_inscripcion ? \Illuminate\Support\Carbon::parse($alumno->fecha_inscripcion)->format('d/m/Y') : 'No registrada' }}</td></tr></table>

    <h2>Contactos de emergencia</h2>
    <table><thead><tr><th>Nombre</th><th>Teléfono</th><th>Correo</th><th>Relación</th></tr></thead><tbody>@forelse($alumno->contactos as $contacto)<tr><td>{{ $contacto->nombre }}</td><td>{{ $contacto->telefono }}</td><td>{{ $contacto->correo }}</td><td>{{ $contacto->tipo_contacto }}</td></tr>@empty<tr><td colspan="4">No hay contactos registrados.</td></tr>@endforelse</tbody></table>

    <h2>Familiares responsables</h2>
    <table><thead><tr><th>Tipo</th><th>Nombre</th><th>Celular</th><th>Correo</th></tr></thead><tbody>@forelse($alumno->familiares as $familiar)<tr><td>{{ $familiar->tipo_familiar }}</td><td>{{ $familiar->nombre }} {{ $familiar->apellido_paterno }} {{ $familiar->apellido_materno }}</td><td>{{ $familiar->celular ?: 'No registrado' }}</td><td>{{ $familiar->correo ?: 'No registrado' }}</td></tr>@empty<tr><td colspan="4">No hay familiares registrados.</td></tr>@endforelse</tbody></table>

    <h2>Plataformas educativas</h2>
    <table><thead><tr><th>Plataforma</th><th>Estado</th></tr></thead><tbody>@forelse($alumno->alumnoPlataforma as $acceso)<tr><td>{{ $acceso->plataforma?->nombre ?? 'Plataforma' }}</td><td>{{ $acceso->usuario && $acceso->contraseña ? 'Credenciales registradas' : 'Pendiente por registrar' }}</td></tr>@empty<tr><td colspan="2">No hay plataformas registradas.</td></tr>@endforelse</tbody></table>

    <h2>Bitácora de contacto con familia</h2>
    <table><thead><tr><th>Fecha</th><th>Medio</th><th>Registró</th><th>Resumen</th></tr></thead><tbody>@forelse($alumno->bitacorasContacto as $registro)<tr><td>{{ $registro->fecha_contacto->format('d/m/Y H:i') }}</td><td>{{ ucfirst($registro->tipo) }}</td><td>{{ $registro->usuario?->name ?? 'Sin registro' }}</td><td>{{ $registro->resumen }}</td></tr>@empty<tr><td colspan="4">Aún no hay contactos registrados.</td></tr>@endforelse</tbody></table>
    <div class="footer">Documento de consulta interna. No incluye contraseñas ni credenciales de acceso.</div>
</body>
</html>

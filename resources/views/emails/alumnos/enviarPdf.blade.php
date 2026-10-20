@component('mail::message')
# Estimado(a) {{ $nombre }}

Se adjunta el informe con los datos de acceso a las plataformas digitales de su hijo(a). Si tiene alguna pregunta, no dude en contactarnos.

Saludos cordiales,<br>
{{ config('app.name') }}
@endcomponent

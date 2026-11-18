@extends('layouts.app')

@section('content')
<div class="container-xl">
    <div class="csl-module-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-3">
            <span class="csl-choice-icon"><i class="ti ti-messages"></i></span>
            <div>
                <h1>Seguimiento con la familia</h1>
                <p>{{ trim("{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}") }}</p>
            </div>
        </div>
        <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-secondary ms-auto"><i class="ti ti-arrow-left"></i> Volver al expediente</a>
    </div>

    <x-task-guide
        title="Cómo usar este historial"
        description="Aquí se guardan llamadas, correos, reuniones o acuerdos con padre, madre o tutor. No modifica los datos del alumno."
        :steps="$puedeRegistrarBitacora ? ['Elige cómo se realizó la comunicación.', 'Revisa la fecha y escribe un resumen claro.', 'Guarda el registro para consultarlo después.'] : ['Consulta los registros anteriores como referencia.', 'Si necesitas agregar uno nuevo, solicita apoyo a Administración o Control Escolar.']"
    />

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <section class="card csl-data-card">
        <div class="card-header">
            <div>
                <h2 class="card-title mb-1"><i class="ti ti-notes me-2"></i>Bitácora de contacto</h2>
                <p class="text-secondary small mb-0">Cada registro muestra cuándo se contactó a la familia, el medio y el acuerdo alcanzado.</p>
            </div>
        </div>
        <div class="card-body">
            @if($puedeRegistrarBitacora)
                <form method="POST" action="{{ route('alumnos.bitacora-contacto.registrar', $alumno) }}" class="row g-3 mb-4">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label" for="contacto-tipo">¿Cómo se realizó la comunicación?</label>
                        <select id="contacto-tipo" name="tipo" class="form-select" required>
                            <option value="llamada">Llamada telefónica</option>
                            <option value="correo">Correo electrónico</option>
                            <option value="reunion">Reunión</option>
                            <option value="mensaje">Mensaje</option>
                            <option value="otro">Otro medio</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="contacto-fecha">¿Cuándo ocurrió?</label>
                        <input id="contacto-fecha" name="fecha_contacto" type="datetime-local" class="form-control" value="{{ now()->format('Y-m-d\\TH:i') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="contacto-resumen">¿Qué se habló o acordó?</label>
                        <textarea id="contacto-resumen" name="resumen" class="form-control" rows="4" maxlength="1500" required placeholder="Ejemplo: Se habló con la madre sobre la actualización de datos y se acordó entregar el documento el viernes."></textarea>
                        <small class="text-secondary">El resumen es obligatorio. No escribas contraseñas ni información sensible.</small>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit"><i class="ti ti-device-floppy me-2"></i>Guardar en el historial</button>
                    </div>
                </form>
            @else
                <div class="alert alert-secondary mb-4"><i class="ti ti-lock me-2"></i>Coordinación puede consultar el historial como referencia. Administración y Control Escolar registran nuevas comunicaciones.</div>
            @endif

            <div class="table-responsive">
                <table class="table table-vcenter mb-0">
                    <thead><tr><th>Fecha</th><th>Medio</th><th>Resumen</th><th>Registró</th></tr></thead>
                    <tbody>
                        @forelse($alumno->bitacorasContacto as $registro)
                            <tr>
                                <td class="text-nowrap">{{ $registro->fecha_contacto->format('d/m/Y H:i') }}</td>
                                <td><span class="badge bg-azure-lt">{{ ucfirst($registro->tipo) }}</span></td>
                                <td>{{ $registro->resumen }}</td>
                                <td>{{ $registro->usuario?->name ?? 'Sin registro' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary py-4">Aún no hay contactos registrados con la familia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

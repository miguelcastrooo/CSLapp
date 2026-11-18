<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grado;
use App\Models\HistorialAlumno;
use App\Models\NivelEducativo;
use App\Models\Seccion;
use App\Support\NivelAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportacionAlumnoController extends Controller
{
    private const IMPORT_HEADERS = [
        'matricula', 'nombres', 'apellido_paterno', 'apellido_materno', 'nivel_educativo', 'grado', 'seccion',
        'fecha_inscripcion', 'fecha_inicio',
    ];

    private const EXPORT_HEADERS = [
        'matricula', 'nombres', 'apellido_paterno', 'apellido_materno', 'nivel_educativo', 'grado', 'seccion',
        'fecha_inscripcion', 'fecha_inicio', 'plataformas_asignadas',
    ];

    public function index()
    {
        $this->autorizar();
        $nivelIds = NivelAccess::allowedIds(auth()->user());
        $niveles = NivelEducativo::whereIn('id', $nivelIds)->where('activo', true)->orderBy('nombre')->get();
        $grados = Grado::whereIn('nivel_educativo_id', $nivelIds)->where('activo', true)->orderBy('nombre')->get();
        $secciones = Seccion::whereIn('grado_id', $grados->pluck('id'))->where('activo', true)->orderBy('nombre')->get();
        $alumnos = Alumno::whereIn('nivel_educativo_id', $nivelIds)->orderBy('apellidopaterno')->orderBy('apellidomaterno')->orderBy('nombre')->get(['id', 'matricula', 'nombre', 'apellidopaterno', 'apellidomaterno', 'nivel_educativo_id', 'grado_id', 'seccion']);

        return view('capturista.excel', [
            'preview' => session('importacion_alumnos.preview'),
            'niveles' => $niveles,
            'grados' => $grados,
            'secciones' => $secciones,
            'alumnos' => $alumnos,
        ]);
    }

    public function plantilla()
    {
        $this->autorizar();
        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->setTitle('Alumnos');
        $sheet->fromArray([self::IMPORT_HEADERS], null, 'A1');
        $sheet->fromArray([[
            '260001', 'María Fernanda', 'De la Cruz', 'Del Valle', 'Primaria Baja', '1°', 'A', now()->toDateString(), now()->toDateString(),
        ]], null, 'A2');
        $sheet->freezePane('A2');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $this->download($sheet->getParent(), 'plantilla-importacion-alumnos.xlsx');
    }

    public function exportar(Request $request)
    {
        $this->autorizar();
        $niveles = NivelAccess::allowedIds(auth()->user());
        $request->validate([
            'nivel_id' => ['nullable', 'integer'],
            'grado_id' => ['nullable', 'integer'],
            'seccion' => ['nullable', 'string', 'max:50'],
            'alumno_id' => ['nullable', 'integer'],
        ]);

        if ($request->filled('nivel_id')) {
            NivelAccess::ensureCanAccess(auth()->user(), (int) $request->nivel_id);
        }
        if ($request->filled('grado_id')) {
            $grado = Grado::findOrFail($request->grado_id);
            NivelAccess::ensureCanAccess(auth()->user(), $grado->nivel_educativo_id);
            abort_if($request->filled('nivel_id') && $grado->nivel_educativo_id !== (int) $request->nivel_id, 422, 'El grado no pertenece al nivel seleccionado.');
        }
        if ($request->filled('alumno_id')) {
            $alumnoSeleccionado = Alumno::findOrFail($request->alumno_id);
            NivelAccess::ensureCanAccess(auth()->user(), $alumnoSeleccionado->nivel_educativo_id);
        }

        $alumnos = Alumno::with(['nivelEducativo', 'grado', 'alumnoPlataforma.plataforma'])
            ->whereIn('nivel_educativo_id', $niveles)
            ->when($request->filled('nivel_id'), fn ($query) => $query->where('nivel_educativo_id', $request->nivel_id))
            ->when($request->filled('grado_id'), fn ($query) => $query->where('grado_id', $request->grado_id))
            ->when($request->filled('seccion'), fn ($query) => $query->where('seccion', $request->seccion))
            ->when($request->filled('alumno_id'), fn ($query) => $query->whereKey($request->alumno_id))
            ->orderBy('apellidopaterno')->orderBy('apellidomaterno')->orderBy('nombre')->get();

        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->setTitle('Alumnos');
        $sheet->fromArray([self::EXPORT_HEADERS], null, 'A1');
        foreach ($alumnos as $alumno) {
            $sheet->fromArray([[
                $alumno->matricula, $alumno->nombre, $alumno->apellidopaterno, $alumno->apellidomaterno,
                $alumno->nivelEducativo?->nombre, $alumno->grado?->nombre, $alumno->seccion,
                $alumno->fecha_inscripcion ? Carbon::parse($alumno->fecha_inscripcion)->format('Y-m-d') : null,
                $alumno->fecha_inicio ? Carbon::parse($alumno->fecha_inicio)->format('Y-m-d') : null,
                $alumno->alumnoPlataforma->map(fn ($asignacion) => $asignacion->plataforma?->nombre)->filter()->implode(', '),
            ]], null, 'A'.($sheet->getHighestRow() + 1));
        }
        $sheet->freezePane('A2');
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        return $this->download($sheet->getParent(), 'alumnos-'.now()->format('Y-m-d').'.xlsx');
    }

    public function previsualizar(Request $request)
    {
        $this->autorizar();
        $request->validate(['archivo' => ['required', 'file', 'mimes:xlsx', 'max:5120']]);

        try {
            $rows = IOFactory::load($request->file('archivo')->getRealPath())->getActiveSheet()->toArray(null, true, true, false);
        } catch (\Throwable) {
            return back()->withErrors(['archivo' => 'No fue posible leer el archivo. Usa la plantilla descargada y guárdala como .xlsx.']);
        }

        $headers = array_map(fn ($header) => strtolower(trim((string) $header)), array_shift($rows) ?? []);
        if ($headers !== self::IMPORT_HEADERS) {
            return back()->withErrors(['archivo' => 'Los encabezados no coinciden con la plantilla. Descarga una plantilla nueva y no cambies sus columnas.']);
        }
        if (count($rows) > 500) {
            return back()->withErrors(['archivo' => 'El archivo puede contener hasta 500 alumnos por importación.']);
        }

        $preview = ['validos' => [], 'errores' => []];
        $matriculasVistas = [];
        foreach ($rows as $index => $values) {
            if (! array_filter($values, fn ($value) => filled($value))) {
                continue;
            }

            $linea = $index + 2;
            $row = array_combine(self::IMPORT_HEADERS, array_pad($values, count(self::IMPORT_HEADERS), null));
            [$data, $errores] = $this->validarFila($row, $matriculasVistas);
            if ($data) {
                $matriculasVistas[] = $data['matricula'];
                $preview['validos'][] = ['linea' => $linea, 'data' => $data];
            } else {
                $preview['errores'][] = ['linea' => $linea, 'nombre' => trim(($row['nombres'] ?? '').' '.($row['apellido_paterno'] ?? '')), 'mensajes' => $errores];
            }
        }

        session(['importacion_alumnos.preview' => $preview]);

        return redirect()->route('alumnos.excel.index');
    }

    public function confirmar()
    {
        $this->autorizar();
        $preview = session('importacion_alumnos.preview');
        if (empty($preview['validos']) || ! empty($preview['errores'])) {
            return redirect()->route('alumnos.excel.index')->with('error', 'Corrige todos los errores antes de confirmar la importación.');
        }

        DB::transaction(function () use ($preview) {
            foreach ($preview['validos'] as $fila) {
                $data = $fila['data'];
                abort_if(Alumno::where('matricula', $data['matricula'])->exists(), 422, "La matrícula {$data['matricula']} ya existe. Vuelve a revisar el archivo.");

                $alumno = Alumno::create($data['alumno']);
                HistorialAlumno::create([
                    'alumno_id' => $alumno->id,
                    'alumno_nombre' => trim("{$alumno->nombre} {$alumno->apellidopaterno} {$alumno->apellidomaterno}"),
                    'user_id' => auth()->id(),
                    'accion' => 'alta_importacion_excel',
                    'cambios' => ['origen' => 'Importación de Excel'],
                    'fecha' => now(),
                ]);
            }
        });

        $cantidad = count($preview['validos']);
        session()->forget('importacion_alumnos.preview');

        return redirect()->route('alumnos.excel.index')->with('success', "Se importaron {$cantidad} alumno(s) y se registró el historial de cada expediente.");
    }

    private function validarFila(array $row, array $matriculasVistas): array
    {
        $row = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $row);
        $requeridos = ['matricula', 'nombres', 'apellido_paterno', 'apellido_materno', 'nivel_educativo', 'grado', 'fecha_inscripcion'];
        $errores = [];
        foreach ($requeridos as $campo) {
            if (! array_key_exists($campo, $row) || $row[$campo] === null || $row[$campo] === '') {
                $errores[] = "Falta {$campo}.";
            }
        }
        if ($errores) {
            return [null, $errores];
        }

        $matricula = (string) $row['matricula'];
        if (in_array($matricula, $matriculasVistas, true) || Alumno::where('matricula', $matricula)->exists()) {
            $errores[] = 'La matrícula ya existe o está repetida en el archivo.';
        }
        $nivel = NivelEducativo::where('nombre', $row['nivel_educativo'])->first();
        if (! $nivel || ! in_array($nivel->id, NivelAccess::allowedIds(auth()->user()), true)) {
            $errores[] = 'El nivel educativo no existe o no tienes acceso a él.';
        }
        $grado = $nivel ? Grado::where('nivel_educativo_id', $nivel->id)->where('nombre', $row['grado'])->first() : null;
        if (! $grado) {
            $errores[] = 'El grado no pertenece al nivel indicado.';
        }
        if ($grado && filled($row['seccion']) && Seccion::where('grado_id', $grado->id)->exists() && ! Seccion::where('grado_id', $grado->id)->where('nombre', $row['seccion'])->exists()) {
            $errores[] = 'La sección no pertenece al grado indicado.';
        }
        $fechaInscripcion = $this->fecha($row['fecha_inscripcion']);
        $fechaInicio = blank($row['fecha_inicio']) ? $fechaInscripcion : $this->fecha($row['fecha_inicio']);
        if (! $fechaInscripcion || ! $fechaInicio) {
            $errores[] = 'Las fechas deben tener formato AAAA-MM-DD.';
        }
        if ($errores) {
            return [null, $errores];
        }

        return [[
            'matricula' => $matricula,
            'alumno' => [
                'matricula' => $matricula, 'nombre' => $row['nombres'], 'apellidopaterno' => $row['apellido_paterno'], 'apellidomaterno' => $row['apellido_materno'],
                'nivel_educativo_id' => $nivel->id, 'grado_id' => $grado->id, 'seccion' => $row['seccion'] ?: null,
                'fecha_inscripcion' => $fechaInscripcion, 'fecha_inicio' => $fechaInicio,
                'sexo' => 'Sin Definir',
            ],
        ], []];
    }

    private function fecha(mixed $value): ?string
    {
        try {
            return is_numeric($value) ? Date::excelToDateTimeObject($value)->format('Y-m-d') : Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function autorizar(): void
    {
        abort_unless(NivelAccess::hasFullAccess(auth()->user()), 403);
    }

    private function download(Spreadsheet $spreadsheet, string $filename)
    {
        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}

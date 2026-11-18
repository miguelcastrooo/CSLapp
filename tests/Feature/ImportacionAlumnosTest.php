<?php

use App\Models\Alumno;
use App\Models\Grado;
use App\Models\NivelEducativo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('ControlEscolar', 'web');
});

test('control escolar revisa e importa alumnos sin separar apellidos compuestos', function () {
    $usuario = User::factory()->create(['activo' => true]);
    $usuario->assignRole('ControlEscolar');
    $nivel = NivelEducativo::create(['nombre' => 'Primaria de prueba', 'activo' => true]);
    $grado = Grado::create(['nombre' => '1°', 'nivel_educativo_id' => $nivel->id, 'activo' => true]);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray([[
        'matricula', 'nombres', 'apellido_paterno', 'apellido_materno', 'nivel_educativo', 'grado', 'seccion', 'fecha_inscripcion', 'fecha_inicio',
    ], [
        '260001', 'María Fernanda', 'De la Cruz', 'Del Valle', 'Primaria de prueba', '1°', '', '2026-01-10', '2026-01-10',
    ]]);
    $archivo = tempnam(sys_get_temp_dir(), 'alumnos-').'.xlsx';
    (new Xlsx($spreadsheet))->save($archivo);

    $this->actingAs($usuario)
        ->post(route('alumnos.excel.previsualizar'), [
            'archivo' => new UploadedFile($archivo, 'alumnos.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
        ])
        ->assertRedirect(route('alumnos.excel.index'));

    expect(session('importacion_alumnos.preview.validos'))->toHaveCount(1)
        ->and(session('importacion_alumnos.preview.errores'))->toBeEmpty();

    $this->actingAs($usuario)->post(route('alumnos.excel.confirmar'))->assertRedirect(route('alumnos.excel.index'));

    $this->assertDatabaseHas('alumnos', [
        'matricula' => '260001',
        'nombre' => 'María Fernanda',
        'apellidopaterno' => 'De la Cruz',
        'apellidomaterno' => 'Del Valle',
    ]);
    $this->assertDatabaseHas('historial_alumnos', [
        'alumno_id' => Alumno::where('matricula', '260001')->value('id'),
        'accion' => 'alta_importacion_excel',
    ]);

    $this->actingAs($usuario)
        ->get(route('alumnos.excel.exportar', ['nivel_id' => $nivel->id, 'grado_id' => $grado->id]))
        ->assertDownload();
});

test('coordinacion no puede usar la importacion masiva', function () {
    Role::findOrCreate('CoordinacionPreescolar', 'web');
    $usuario = User::factory()->create(['activo' => true]);
    $usuario->assignRole('CoordinacionPreescolar');

    $this->actingAs($usuario)->get(route('alumnos.excel.index'))->assertForbidden();
});

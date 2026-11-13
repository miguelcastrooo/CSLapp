<?php

use App\Models\Alumno;
use App\Models\Grado;
use App\Models\NivelEducativo;
use App\Models\User;
use Spatie\Permission\Models\Role;

function crearAlumnoDePrueba(NivelEducativo $nivel, string $matricula): Alumno
{
    $grado = Grado::create([
        'nombre' => "Grado {$matricula}",
        'nivel_educativo_id' => $nivel->id,
    ]);

    return Alumno::factory()->create([
        'matricula' => $matricula,
        'nivel_educativo_id' => $nivel->id,
        'grado_id' => $grado->id,
        'status' => 1,
    ]);
}

beforeEach(function () {
    Role::findOrCreate('CoordinacionPreescolar', 'web');
});

test('una coordinacion solo consulta alumnos de su nivel', function () {
    $preescolar = NivelEducativo::create(['nombre' => 'Preescolar']);
    $secundaria = NivelEducativo::create(['nombre' => 'Secundaria']);
    $alumnoPreescolar = crearAlumnoDePrueba($preescolar, 'PERM-PRE-001');
    $alumnoSecundaria = crearAlumnoDePrueba($secundaria, 'PERM-SEC-001');
    $coordinadora = User::factory()->create();
    $coordinadora->assignRole('CoordinacionPreescolar');

    $this->actingAs($coordinadora)
        ->get("/calificaciones/{$alumnoPreescolar->id}")
        ->assertOk()
        ->assertJsonPath('alumno', "{$alumnoPreescolar->nombre} {$alumnoPreescolar->apellidopaterno}");

    $this->actingAs($coordinadora)
        ->get("/calificaciones/{$alumnoSecundaria->id}")
        ->assertForbidden();
});

test('una coordinacion no puede cargar grados de otro nivel', function () {
    $preescolar = NivelEducativo::create(['nombre' => 'Preescolar']);
    $secundaria = NivelEducativo::create(['nombre' => 'Secundaria']);
    Grado::create(['nombre' => 'Preescolar 1', 'nivel_educativo_id' => $preescolar->id]);
    $gradoSecundaria = Grado::create(['nombre' => 'Secundaria 1', 'nivel_educativo_id' => $secundaria->id]);
    $coordinadora = User::factory()->create();
    $coordinadora->assignRole('CoordinacionPreescolar');

    $this->actingAs($coordinadora)
        ->get("/grados/{$gradoSecundaria->nivel_educativo_id}")
        ->assertForbidden();
});

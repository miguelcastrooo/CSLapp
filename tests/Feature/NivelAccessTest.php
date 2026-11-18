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
    $coordinadora = User::factory()->create(['activo' => true]);
    $coordinadora->assignRole('CoordinacionPreescolar');
    $token = $coordinadora->createToken('prueba', ['calificaciones:read'])->plainTextToken;

    $this->withToken($token)
        ->getJson("/api/v1/calificaciones?alumno_id={$alumnoPreescolar->id}")
        ->assertOk()
        ->assertJson([]);

    $this->withToken($token)
        ->getJson("/api/v1/calificaciones?alumno_id={$alumnoSecundaria->id}")
        ->assertForbidden();
});

test('una coordinacion no puede cargar grados de otro nivel', function () {
    $preescolar = NivelEducativo::create(['nombre' => 'Preescolar']);
    $secundaria = NivelEducativo::create(['nombre' => 'Secundaria']);
    Grado::create(['nombre' => 'Preescolar 1', 'nivel_educativo_id' => $preescolar->id]);
    $gradoSecundaria = Grado::create(['nombre' => 'Secundaria 1', 'nivel_educativo_id' => $secundaria->id]);
    $coordinadora = User::factory()->create(['activo' => true]);
    $coordinadora->assignRole('CoordinacionPreescolar');
    $token = $coordinadora->createToken('prueba', ['calificaciones:read'])->plainTextToken;

    $this->withToken($token)
        ->getJson("/api/v1/niveles/{$gradoSecundaria->nivel_educativo_id}/grados")
        ->assertForbidden();
});

test('una coordinacion no puede registrar ni dar de baja alumnos', function () {
    $coordinadora = User::factory()->create(['activo' => true]);
    $coordinadora->assignRole('CoordinacionPreescolar');

    $this->actingAs($coordinadora)
        ->get('/alumnos/select')
        ->assertForbidden();

    $this->actingAs($coordinadora)
        ->get('/alumnos/baja')
        ->assertForbidden();
});

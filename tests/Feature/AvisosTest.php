<?php

use App\Models\User;
use App\Notifications\AvisoEscolar;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['SuperAdmin', 'ControlEscolar', 'CoordinacionPreescolar'] as $rol) {
        Role::findOrCreate($rol, 'web');
    }
});

test('los avisos se guardan y se emiten en tiempo real', function () {
    $aviso = new AvisoEscolar('Aviso de prueba', 'Este aviso debe llegar al instante.');

    expect($aviso->via(User::factory()->make()))->toBe(['database', 'broadcast']);
});

test('control escolar puede enviar un aviso a una coordinacion', function () {
    $controlEscolar = User::factory()->create(['activo' => true]);
    $controlEscolar->assignRole('ControlEscolar');
    $coordinadora = User::factory()->create(['activo' => true]);
    $coordinadora->assignRole('CoordinacionPreescolar');

    $this->actingAs($controlEscolar)
        ->post(route('avisos.enviar'), [
            'titulo' => 'Revisión de expedientes',
            'mensaje' => 'Favor de revisar los expedientes pendientes.',
            'tipo_destino' => 'rol',
            'roles' => ['CoordinacionPreescolar'],
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('notifications', [
        'notifiable_id' => $coordinadora->id,
        'type' => AvisoEscolar::class,
    ]);
});

test('una coordinacion puede enviar una consulta a superadmin', function () {
    $coordinadora = User::factory()->create(['activo' => true]);
    $coordinadora->assignRole('CoordinacionPreescolar');
    $superAdmin = User::factory()->create(['activo' => true]);
    $superAdmin->assignRole('SuperAdmin');

    $this->actingAs($coordinadora)
        ->post(route('avisos.enviar'), [
            'titulo' => 'Necesito apoyo',
            'mensaje' => 'Tengo una duda sobre un expediente.',
            'tipo_destino' => 'rol',
            'roles' => ['SuperAdmin'],
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('notifications', [
        'notifiable_id' => $superAdmin->id,
        'type' => AvisoEscolar::class,
    ]);
});

test('superadmin puede enviar un aviso a una persona especifica', function () {
    $superAdmin = User::factory()->create(['activo' => true]);
    $superAdmin->assignRole('SuperAdmin');
    $destinataria = User::factory()->create(['activo' => true]);
    $destinataria->assignRole('CoordinacionPreescolar');

    $this->actingAs($superAdmin)
        ->post(route('avisos.enviar'), [
            'titulo' => 'Seguimiento individual',
            'mensaje' => 'Por favor revisa tu bandeja de pendientes.',
            'tipo_destino' => 'usuario',
            'usuario_id' => $destinataria->id,
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('notifications', [
        'notifiable_id' => $destinataria->id,
        'type' => AvisoEscolar::class,
    ]);
});

test('cada usuario puede borrar sus avisos sin afectar los de otras cuentas', function () {
    $usuario = User::factory()->create(['activo' => true]);
    $otroUsuario = User::factory()->create(['activo' => true]);
    $usuario->notify(new AvisoEscolar('Aviso propio', 'Se puede eliminar.'));
    $otroUsuario->notify(new AvisoEscolar('Aviso ajeno', 'Debe conservarse.'));
    $id = $usuario->notifications()->value('id');

    $this->actingAs($usuario)->delete(route('avisos.borrar', $id))->assertSessionHas('success');

    expect($usuario->fresh()->notifications)->toBeEmpty()
        ->and($otroUsuario->fresh()->notifications)->toHaveCount(1);
});

test('admin puede responder a quien envió un aviso', function () {
    $admin = User::factory()->create(['activo' => true]);
    $admin->assignRole('SuperAdmin');
    $coordinadora = User::factory()->create(['activo' => true]);
    $coordinadora->assignRole('CoordinacionPreescolar');
    $admin->notify(new AvisoEscolar('Duda de coordinación', 'Necesito confirmar un dato.', [
        'Enviado por' => $coordinadora->name,
        'remitente_id' => $coordinadora->id,
    ], 'aviso'));
    $id = $admin->notifications()->value('id');

    $this->actingAs($admin)
        ->post(route('avisos.responder', $id), ['mensaje' => 'Ya revisé el caso.'])
        ->assertSessionHas('success');

    $this->assertDatabaseHas('notifications', [
        'notifiable_id' => $coordinadora->id,
        'type' => AvisoEscolar::class,
    ]);
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('api login returns a sanctum token for an active user', function () {
    $user = User::factory()->create([
        'email' => 'api-test@example.test',
        'password' => Hash::make('ClaveSegura123!'),
        'activo' => true,
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'ClaveSegura123!',
        'device_name' => 'prueba-api',
    ]);

    $response->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'roles']]);
});

test('api catalog requires an authenticated token', function () {
    $this->getJson('/api/v1/niveles')->assertUnauthorized();
});

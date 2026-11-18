<?php

test('api angular remains disabled by default', function () {
    $this->postJson('/api/v1/auth/login', [
        'email' => 'api-test@example.test',
        'password' => 'ClaveSegura123!',
    ])->assertNotFound();

    $this->getJson('/api/v1/niveles')->assertNotFound();
});

<?php

test('password reset routes are unavailable', function () {
    $this->get('/forgot-password')->assertNotFound();
    $this->post('/forgot-password', ['email' => 'test@example.com'])->assertNotFound();
    $this->get('/reset-password/example-token')->assertNotFound();
    $this->post('/reset-password', [])->assertNotFound();
});

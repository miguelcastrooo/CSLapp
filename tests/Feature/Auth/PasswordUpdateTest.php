<?php

test('users cannot update their own password', function () {
    $this->put('/password', [])->assertNotFound();
});

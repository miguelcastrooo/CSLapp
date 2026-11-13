<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Verificación de correo institucional
    |--------------------------------------------------------------------------
    |
    | Se deja desactivada mientras se prepara el cambio para las cuentas
    | existentes. Cuando se decida activarla, cambia la variable de entorno a
    | true y actualiza la caché de configuración.
    |
    */
    'require_verified_email' => env('SCHOOL_REQUIRE_VERIFIED_EMAIL', false),
];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API experimental de Angular
    |--------------------------------------------------------------------------
    |
    | La API se conserva como una ampliación futura, pero permanece cerrada
    | hasta que el módulo de calificaciones tenga reglas académicas aprobadas.
    | Para habilitarla se requiere definir API_PORTAL_ENABLED=true en el entorno
    | correspondiente y validar de nuevo sus permisos y pruebas.
    |
    */
    'angular_api_enabled' => filter_var(env('API_PORTAL_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
];

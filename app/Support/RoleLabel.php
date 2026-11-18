<?php

namespace App\Support;

class RoleLabel
{
    public static function for(?string $role): string
    {
        return match ($role) {
            'SuperAdmin' => 'Admin',
            'ControlEscolar' => 'Control Escolar',
            'CoordinacionPreescolar' => 'Coordinación Preescolar',
            'CoordinacionPrimaria' => 'Coordinación Primaria',
            'CoordinacionSecundaria' => 'Coordinación Secundaria',
            default => $role ?: 'Sin rol asignado',
        };
    }
}

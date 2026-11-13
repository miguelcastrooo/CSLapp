<?php

namespace App\Support;

use App\Models\NivelEducativo;
use App\Models\User;
use Illuminate\Support\Collection;

class NivelAccess
{
    public static function hasFullAccess(User $user): bool
    {
        return $user->hasRole('SuperAdmin') || $user->hasRole('ControlEscolar');
    }

    public static function allowedLevels(User $user): Collection
    {
        if (self::hasFullAccess($user)) {
            return NivelEducativo::where('activo', true)->orderBy('nombre')->get();
        }

        $names = match (true) {
            $user->hasRole('CoordinacionPreescolar') => ['Preescolar'],
            $user->hasRole('CoordinacionPrimaria') => ['Primaria Baja', 'Primaria Alta'],
            $user->hasRole('CoordinacionSecundaria') => ['Secundaria'],
            default => [],
        };

        return NivelEducativo::whereIn('nombre', $names)->where('activo', true)->orderBy('nombre')->get();
    }

    public static function allowedIds(User $user): array
    {
        return self::allowedLevels($user)->pluck('id')->all();
    }

    public static function ensureCanAccess(User $user, int $levelId): void
    {
        abort_unless(in_array($levelId, self::allowedIds($user), true), 403, 'No tienes permisos para consultar este nivel educativo.');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialSeguridad extends Model
{
    use HasFactory;

    protected $table = 'historial_seguridad';

    protected $fillable = [
        'user_id',
        'evento',
        'descripcion',
        'ip_address',
        'user_agent',
        'metadata',
        'fecha',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'fecha' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function registrar(?User $user, string $evento, string $descripcion, ?array $metadata = null): void
    {
        self::create([
            'user_id' => $user?->id,
            'evento' => $evento,
            'descripcion' => $descripcion,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 512),
            'metadata' => $metadata,
            'fecha' => now(),
        ]);
    }
}

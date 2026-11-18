<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionInstitucional extends Model
{
    use HasFactory;

    protected $table = 'configuracion_institucional';

    protected $fillable = [
        'nombre',
        'subtitulo',
        'logo_path',
        'color_primario',
        'color_sidebar',
        'color_acento',
        'color_fondo',
        'color_texto',
        'color_texto_secundario',
        'color_carrusel',
        'fuente',
        'tamano_texto',
    ];

    public static function valoresPredeterminados(): array
    {
        return [
            'nombre' => 'Instituto Horizonte',
            'subtitulo' => 'Portal académico',
            'logo_path' => null,
            'color_primario' => '#24536D',
            'color_sidebar' => '#24536D',
            'color_acento' => '#176B62',
            'color_fondo' => '#F3F6FA',
            'color_texto' => '#183444',
            'color_texto_secundario' => '#526474',
            'color_carrusel' => '#173D5B',
            'fuente' => 'Inter',
            'tamano_texto' => 16,
        ];
    }

    public function logoUrl(): string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : asset('img/demo-school.svg');
    }

    public function familiaTipografica(): string
    {
        return match ($this->fuente) {
            'Arial' => 'Arial, Helvetica, sans-serif',
            'Georgia' => 'Georgia, Times, serif',
            'Trebuchet MS' => '"Trebuchet MS", Arial, sans-serif',
            default => 'Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif',
        };
    }
}

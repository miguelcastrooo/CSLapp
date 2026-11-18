<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarruselImagen extends Model
{
    use HasFactory;

    protected $table = 'carrusel_imagenes';

    protected $fillable = ['titulo', 'descripcion', 'imagen_path', 'orden', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function url(): string
    {
        return asset('storage/'.$this->imagen_path);
    }
}

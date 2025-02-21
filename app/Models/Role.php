<?php


namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // No es necesario definir la relación con los usuarios, Spatie lo maneja automáticamente.
    protected $fillable = ['name'];
}


<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Grado;
use App\Models\NivelEducativo;
use App\Models\Seccion;
use App\Support\NivelAccess;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function niveles(Request $request) { return response()->json(NivelAccess::allowedLevels($request->user())->map->only(['id', 'nombre'])->values()); }
    public function grados(Request $request, int $nivel) { NivelAccess::ensureCanAccess($request->user(), $nivel); return response()->json(Grado::where('nivel_educativo_id', $nivel)->where('activo', true)->orderBy('orden_promocion')->get(['id', 'nombre', 'orden_promocion'])); }
    public function plataformas(Request $request, int $nivel)
    {
        NivelAccess::ensureCanAccess($request->user(), $nivel);
        return response()->json(NivelEducativo::findOrFail($nivel)->plataformas()->where('activo', true)->orderBy('nombre')->get(['plataformas.id', 'nombre']));
    }
    public function secciones(Request $request, Grado $grado) { NivelAccess::ensureCanAccess($request->user(), $grado->nivel_educativo_id); return response()->json(Seccion::where('grado_id', $grado->id)->where('activo', true)->orderBy('nombre')->get(['id', 'nombre'])); }
    public function alumnos(Request $request)
    {
        $data = $request->validate(['nivel_id' => ['required', 'integer'], 'grado_id' => ['required', 'integer'], 'seccion' => ['nullable', 'string', 'max:50']]);
        NivelAccess::ensureCanAccess($request->user(), $data['nivel_id']);
        abort_unless(Grado::whereKey($data['grado_id'])->where('nivel_educativo_id', $data['nivel_id'])->exists(), 404);
        return response()->json(Alumno::where('nivel_educativo_id', $data['nivel_id'])->where('grado_id', $data['grado_id'])->when($data['seccion'] ?? null, fn ($query, $seccion) => $query->where('seccion', $seccion))->orderBy('apellidopaterno')->orderBy('nombre')->get(['id', 'matricula', 'nombre', 'apellidopaterno', 'apellidomaterno', 'seccion']));
    }
}

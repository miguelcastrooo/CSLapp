<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string'], 'device_name' => ['nullable', 'string', 'max:100']]);
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! $user->activo || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Las credenciales no son válidas o la cuenta está desactivada.'], 422);
        }

        $abilities = ['calificaciones:read'];
        if ($user->hasAnyRole(['SuperAdmin', 'ControlEscolar', 'CoordinacionPreescolar', 'CoordinacionPrimaria', 'CoordinacionSecundaria'])) $abilities[] = 'calificaciones:write';
        $token = $user->createToken($credentials['device_name'] ?? 'angular-portal', $abilities)->plainTextToken;

        return response()->json(['token' => $token, 'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'roles' => $user->getRoleNames()->values()]]);
    }

    public function logout(Request $request) { $request->user()->currentAccessToken()?->delete(); return response()->noContent(); }
}

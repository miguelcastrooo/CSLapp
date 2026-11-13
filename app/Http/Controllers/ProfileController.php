<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Actualizar otros datos del perfil
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'Se han realizado los cambios con exito');
    }

    public function updatePicture(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Las nuevas fotos se almacenan fuera del código versionado.
            if ($user->profile_picture && Str::startsWith($user->profile_picture, 'storage/profiles/')) {
                Storage::disk('public')->delete(Str::after($user->profile_picture, 'storage/'));
            } elseif ($user->profile_picture && Str::startsWith($user->profile_picture, 'img/')) {
                // Compatibilidad con fotos antiguas almacenadas en public/img.
                @unlink(public_path($user->profile_picture));
            }

            // No se usa el nombre original del archivo para evitar rutas o nombres inseguros.
            $file = $request->file('profile_picture');
            $path = $file->storeAs('profiles', Str::uuid().'.'.$file->extension(), 'public');

            // Guardar la ruta en la base de datos
            $user->profile_picture = 'storage/'.$path;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'Foto de perfil actualizada');
    }

}

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Actualizar Contraseña') }}
        </h2>

        <p class="text-secondary mb-0">Usa 12 o más caracteres, mayúscula, minúscula, número y símbolo.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="form-label">Contraseña actual</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="form-label">Nueva contraseña</label>
            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="form-label">Confirmar nueva contraseña</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-primary" type="submit"><i class="fas fa-key me-2"></i>Actualizar contraseña</button>

            @if (session('status') === 'Se actualizado la contraseña')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

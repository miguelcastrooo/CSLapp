<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- Profile Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Profile Information') }}</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PATCH')

                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('Name') }}</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required autofocus>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('Email') }}</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                            </form>
                        </div>
                    </div>

                    <!-- Update Password -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Update Password') }}</h3>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Delete Account -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title text-danger">{{ __('Delete Account') }}</h3>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

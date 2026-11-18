<?php

namespace App\Providers;

use App\Models\ConfiguracionInstitucional;
use App\Models\CarruselImagen;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(fn () => Password::min(12)
            ->mixedCase()
            ->numbers()
            ->symbols());

        View::composer('*', function ($view): void {
            $configuracion = ConfiguracionInstitucional::query()->first();

            $view->with('configuracionInstitucional', $configuracion ?? new ConfiguracionInstitucional(
                ConfiguracionInstitucional::valoresPredeterminados()
            ));
            $view->with('imagenesCarrusel', CarruselImagen::query()->where('activo', true)->orderBy('orden')->get());

            if (auth()->check()) {
                $view->with('avisosPendientes', auth()->user()->unreadNotifications()->latest()->take(5)->get());
                $view->with('totalAvisosPendientes', auth()->user()->unreadNotifications()->count());
            }
        });
    }
}

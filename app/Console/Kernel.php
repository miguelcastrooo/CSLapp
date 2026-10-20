<?php

namespace App\Console;

use Illuminate\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Los comandos de la aplicación globalmente registrados.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Registra los comandos de la aplicación.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Aquí puedes registrar tareas programadas
    }

    /**
     * Registra los middleware de la consola.
     *
     * @return void
     */
    protected function bootstrap()
    {
        // Aquí puedes agregar cualquier proceso de arranque para los comandos
    }
}

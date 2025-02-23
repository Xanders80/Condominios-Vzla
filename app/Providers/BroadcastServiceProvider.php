<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Inicializa los servicios de la aplicación.
     *
     * @return void
     */
    public function boot()
    {
        // Configura las rutas de Broadcast
        Broadcast::routes();

        // Requiere el archivo de configuración de canales
        require_once base_path('routes/channels.php');
    }
}

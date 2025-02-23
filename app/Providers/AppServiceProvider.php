<?php

namespace App\Providers;

use App\Services\PaymentsService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registrar cualquier servicio de la aplicación.
     *
     * @return void
     */
    public function register()
    {
        // Registrar el servicio de Payments
        $this->app->bind(PaymentsService::class, function () {
            return new PaymentsService();
        });
        // Usar el modelo de token de acceso personal de Sanctum
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }

    /**
     * Inicializar cualquier servicio de la aplicación.
     *
     * @return void
     */
    public function boot()
    {
        // Establecer la longitud de cadena predeterminada
        Schema::defaultStringLength(191);

        // Forzar HTTPS en producción
        if (config('app.env') === 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }
    }
}

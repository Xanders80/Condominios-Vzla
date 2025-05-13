<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * La ruta de "inicio" para tu aplicación.
     *
     * Típicamente, los usuarios son redirigidos aquí después de la autenticación.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define los enlaces de modelo de ruta, filtros de patrones y otra configuración de rutas.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurarLimitacionDeTasa();

        $this->routes(function () {
            // Rutas de la API
            Route::middleware('api')
                ->prefix('api')
                ->namespace('App\Http\Controllers\Api')
                ->group(base_path('routes/api.php'));

            // Rutas web
            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/web.php'));

            // Rutas del backend
            Route::middleware(['web', 'auth', 'backend'])
                ->namespace('App\Http\Controllers\Backend')
                ->group(base_path('routes/backend.php')); // Asegúrate de que este archivo contenga todas las rutas necesarias
        });
    }

    /**
     * Configura los limitadores de tasa para la aplicación.
     *
     * @return void
     */
    protected function configurarLimitacionDeTasa()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}

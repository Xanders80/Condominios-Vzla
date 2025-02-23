<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de modelos a políticas para la aplicación.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Modelo' => 'App\Policies\ModeloPolicy',
    ];

    /**
     * Registrar cualquier servicio de autenticación / autorización.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Aquí se pueden registrar otras autorizaciones o configuraciones.
    }
}

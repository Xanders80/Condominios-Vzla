<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de eventos a oyentes para la aplicación.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Registra cualquier evento para tu aplicación.
     *
     * @return void
     */
    public function boot()
    {
        // Se pueden registrar eventos adicionales aquí si es necesario.
    }

    /**
     * Determina si los eventos y oyentes deben ser descubiertos automáticamente.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false; // Cambiar a true si se desea descubrimiento automático.
    }
}

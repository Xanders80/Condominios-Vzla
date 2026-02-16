<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Solo registrar Telescope en desarrollo
        if ($this->app->environment('local')) {

            // Filtrar rutas específicas
            Telescope::filter(function (IncomingEntry $entry) {
                // Ignorar rutas de assets y vendor
                if ($entry->type === 'request') {
                    $uri = $entry->content['uri'] ?? '';

                    return ! str_starts_with($uri, '/js/')
                        && ! str_starts_with($uri, '/css/')
                        && ! str_starts_with($uri, '/admins/');
                }

                return true;
            });

            // Etiquetar entradas importantes
            Telescope::tag(function (IncomingEntry $entry) {
                $tags = [];

                if ($entry->type === 'request') {
                    $tags[] = 'method:'.($entry->content['method'] ?? 'unknown');

                    // Tag para rutas de pagos (críticas)
                    if (str_contains($entry->content['uri'] ?? '', 'payments')) {
                        $tags[] = 'payment';
                    }
                }

                return $tags;
            });
        }

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function (User $user) {
            return $user->level->code === 'root';
        });
    }
}

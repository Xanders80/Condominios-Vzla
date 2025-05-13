<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Obtener los patrones de host que deben ser confiables.
     *
     * @return array<int, string|null>
     */
    public function hosts()
    {
        // Devolver todos los subdominios de la URL de la aplicación
        return [
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}

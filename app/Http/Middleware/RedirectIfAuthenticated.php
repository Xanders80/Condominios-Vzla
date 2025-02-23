<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Manejar una solicitud entrante.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @param string|null                                                                                       ...$guards
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, \Closure $next, ...$guards)
    {
        // Si no se especifican guardias, usar el guardia por defecto
        $guards = empty($guards) ? [null] : $guards;

        // Verificar si el usuario está autenticado en alguno de los guardias
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirigir a la página de inicio si está autenticado
                return redirect(RouteServiceProvider::HOME);
            }
        }

        // Continuar con la solicitud si no está autenticado
        return $next($request);
    }
}

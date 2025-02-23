<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * Lista de tipos de excepciones con sus niveles de log personalizados.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [];

    /**
     * Lista de tipos de excepciones que no se reportan.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [];

    /**
     * Lista de inputs que nunca se envían a la sesión en excepciones de validación.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registra los callbacks para el manejo de excepciones.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (\Throwable $e) {
            // Lógica personalizada para reportar excepciones
        });
    }

    /**
     * Renderiza la excepción en una respuesta HTTP.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, \Throwable $exception)
    {
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();

            return response()->view("errors.{$statusCode}", [], $statusCode);
        }

        return parent::render($request, $exception);
    }
}

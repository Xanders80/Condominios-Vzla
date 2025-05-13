<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// Definimos el inicio del tiempo de ejecución de Laravel
define('LARAVEL_START', microtime(true));

// Comprobar si la aplicación está en modo de mantenimiento
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require_once $maintenance; // Cargar contenido pre-renderizado
}

// Registrar el autoload de Composer
require_once __DIR__.'/../vendor/autoload.php';

// Ejecutar la aplicación
$app = require_once __DIR__.'/../bootstrap/app.php';

// Crear el kernel HTTP de la aplicación
$kernel = $app->make(Kernel::class);

// Capturar la solicitud entrante y manejarla
$response = $kernel->handle(
    $request = Request::capture()
)->send(); // Enviar la respuesta al navegador

// Terminar el manejo de la solicitud
$kernel->terminate($request, $response);

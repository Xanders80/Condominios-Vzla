<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// Definimos el inicio del tiempo de ejecución de Laravel
define('LARAVEL_START', microtime(true));

// Comprobar si la aplicación está en modo de mantenimiento
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require_once $maintenance; // Cargar contenido pre-renderizado
}

// Registrar el autoload de Composer (comprobación amigable si falta)
$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    http_response_code(500);
    echo "<h1>Dependencias faltantes</h1>";
    echo "<p>No se encontró <strong>vendor/autoload.php</strong>. Instale las dependencias del proyecto ejecutando:</p>";
    echo "<pre>composer install</pre>";
    echo "<p>Ejecute el comando en la raíz del proyecto y vuelva a intentar.</p>";
    // Terminar para evitar el fatal error original
    exit(1);
}
require_once $autoload;

// Ejecutar la aplicación
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Crear el kernel HTTP de la aplicación
$kernel = $app->make(Kernel::class);

// Capturar la solicitud entrante y manejarla
$response = $kernel->handle(
    $request = Request::capture()
)->send(); // Enviar la respuesta al navegador

// Terminar el manejo de la solicitud
$kernel->terminate($request, $response);

<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

/**
 * @OA\Info(
 *     title="Condominios API",
 *     version="1.0.0",
 *     description="API RESTful para autenticación de usuarios en el sistema de Condominios",
 *
 *     @OA\Contact(
 *         email="admin@condominios.com"
 *     ),
 *
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Enter token in format (Bearer <token>)"
 * )
 */
class OpenApiSpec
{
    // Esta clase solo contiene anotaciones OpenAPI
}

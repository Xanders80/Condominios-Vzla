<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * Trait ApiResponseTrait
 * 
 * Proporciona métodos estandarizados para respuestas JSON en la API
 */
trait ApiResponseTrait
{
    /**
     * Respuesta exitosa
     */
    protected function successResponse(
        string $message,
        array $data = [],
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data ?: null,
        ], $code);
    }

    /**
     * Respuesta de error
     */
    protected function errorResponse(
        string $message,
        int $code = 400,
        array $errors = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}

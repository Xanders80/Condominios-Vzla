<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

abstract class BaseService
{
    /**
     * Standardized response format.
     */
    protected function response(bool $status, string $message, int $statusCode, $data = null, array $errors = []): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'status_code' => $statusCode,
            'data' => $data,
            'errors' => $errors,
        ];
    }

    /**
     * Standardized success response.
     */
    protected function success(string $message, $data = null, int $statusCode = 200): array
    {
        return $this->response(true, $message, $statusCode, $data);
    }

    /**
     * Standardized error response.
     */
    protected function error(string $message, array $errors = [], int $statusCode = 500): array
    {
        return $this->response(false, $message, $statusCode, null, $errors);
    }

    /**
     * Wrap database operations in a transaction with error handling.
     */
    protected function executeTransaction(callable $callback, string $errorMessagePrefix = 'Operation failed')
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($errorMessagePrefix, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->error($errorMessagePrefix . ': ' . $e->getMessage());
        }
    }
}

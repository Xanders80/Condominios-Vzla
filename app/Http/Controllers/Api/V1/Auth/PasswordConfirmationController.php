<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Controlador de Confirmación de Contraseña API RESTful
 * 
 * POST /api/v1/auth/confirm-password - Confirmar contraseña
 */
class PasswordConfirmationController extends Controller
{
    /**
     * Confirmar contraseña del usuario
     * 
     * POST /api/v1/auth/confirm-password
     */
    public function confirm(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'password' => ['required', 'string'],
            ]);

            if (!Auth::guard('web')->validate([
                'email' => $request->user()->email,
                'password' => $validated['password'],
            ])) {
                throw ValidationException::withMessages([
                    'password' => [trans('auth.password')]
                ]);
            }

            $request->session()->put('auth.password_confirmed_at', now());

            return $this->successResponse('Password confirmed successfully.');
        } catch (ValidationException $e) {
            Log::error('Password confirmation validation error', [
                'user_id' => $request->user()?->id,
                'errors' => $e->errors(),
            ]);

            return $this->errorResponse(
                'Invalid password',
                422,
                $e->errors()
            );
        } catch (\Exception $e) {
            Log::critical('Error confirming password', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'An error occurred while confirming password',
                500
            );
        }
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\PasswordResetRequest;
use App\Http\Requests\Api\V1\Auth\NewPasswordRequest;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

/**
 * Controlador de Restablecimiento de Contraseña API RESTful
 * 
 * POST /api/v1/auth/password/email - Solicitar enlace
 * PUT /api/v1/auth/password/reset - Resetear contraseña
 */
class PasswordController extends Controller
{
    /**
     * Solicitar enlace de restablecimiento
     * 
     * POST /api/v1/auth/password/email
     */
    public function sendResetLink(PasswordResetRequest $request): JsonResponse
    {
        try {
            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return $this->successResponse(
                    trans($status),
                    ['email' => $request->email]
                );
            }

            return $this->errorResponse(trans($status), 400);
        } catch (\Exception $e) {
            Log::error('Error sending reset link', [
                'error' => $e->getMessage(),
                'email' => $request->email,
            ]);

            return $this->errorResponse(
                'An error occurred while trying to send the reset link',
                500
            );
        }
    }

    /**
     * Resetear contraseña
     * 
     * PUT /api/v1/auth/password/reset
     */
    public function reset(NewPasswordRequest $request): JsonResponse
    {
        try {
            $status = Password::reset(
                $request->validated(),
                fn ($user, $password) => $this->updatePassword($user, $password)
            );

            if ($status === Password::PASSWORD_RESET) {
                return $this->successResponse(trans($status));
            }

            return $this->errorResponse(trans($status), 400);
        } catch (\Exception $e) {
            Log::error('Error resetting password', [
                'error' => $e->getMessage(),
                'email' => $request->email,
            ]);

            return $this->errorResponse(
                'An error occurred while trying to reset your password',
                500
            );
        }
    }

    /**
     * Actualizar contraseña del usuario
     */
    protected function updatePassword(User $user, string $password): void
    {
        $user->update(['password' => Hash::make($password)]);
        event(new PasswordReset($user));
    }
}

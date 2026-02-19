<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ResendVerificationRequest;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Controlador de Verificación de Email API RESTful
 * 
 * GET /api/v1/auth/email/verify/{id}/{hash} - Verificar email
 * POST /api/v1/auth/email/resend - Reenviar verificación
 */
class VerificationController extends Controller
{
    /**
     * Verificar email del usuario
     * 
     * GET /api/v1/auth/email/verify/{id}/{hash}
     */
    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        try {
            if ($request->user()->hasVerifiedEmail()) {
                return $this->successResponse('Email already verified.');
            }

            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }

            return $this->successResponse('Email verified successfully.');
        } catch (\Exception $e) {
            Log::error('Error verifying email', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'An error occurred while verifying email',
                500
            );
        }
    }

    /**
     * Reenviar email de verificación
     * 
     * POST /api/v1/auth/email/resend
     */
    public function resend(ResendVerificationRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->errorResponse('User not found.', 404);
            }

            if ($user->hasVerifiedEmail()) {
                return $this->errorResponse('Your email is already verified.', 400);
            }

            if (!$user->hasEmailVerificationExpired()) {
                return $this->errorResponse(
                    'A verification email has already been sent. Please check your inbox.',
                    429
                );
            }

            $user->sendEmailVerificationNotification();

            return $this->successResponse(
                'A new verification link has been sent to your email address.'
            );
        } catch (\Exception $e) {
            Log::critical('Error sending verification email', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'Error sending email verification',
                500
            );
        }
    }
}

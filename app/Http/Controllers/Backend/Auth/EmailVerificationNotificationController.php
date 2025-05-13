<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Envía una nueva verificación de correo si el usuario no ha verificado su correo.
     *
     * @return RedirectResponse
     */
    public function resendVerificationEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $message = trans('A verification email has already been sent. Please check your inbox.');
        $status = config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR');

        // Verifica si el usuario ya ha verificado su correo electrónico
        if ($user->email_verified_at !== null) {
            $message = trans('Your email is already verified.');
        } elseif ($user->hasEmailVerificationExpired()) {
            try {
                // Enviar el correo de verificación
                $user->sendEmailVerificationNotification();
                $user->sendEmailVerificationNotificationAt();
                $message = trans('A new verification link has been sent to your email address.');
                $status = config('constants.STATUS_CODES.OK');
            } catch (\Exception $e) {
                // Registra el error crítico en el log
                Log::critical(trans('Error sending email verification'), [
                    'user_id' => $user->id ?? null,
                    'error_message' => $e->getMessage(),
                ]);
                $message = trans('Error sending email verification');
                $status = config('constants.STATUS_CODES.BAD_REQUEST');
            }
        } else {
            $message = trans('An error occurred while trying to verify email');
        }

        return $this->help::jsonResponse(false, $message, $status);
    }
}

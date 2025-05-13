<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Maneja la verificación de correo electrónico para el usuario autenticado.
     *
     * @param EmailVerificationRequest $request
     *
     * @return RedirectResponse
     */
    public function __invoke(Request $request, $id, $hash)
    {
        // Buscar al usuario por ID
        $user = User::find($id); // Asumiendo que el ID del usuario se pasa como parámetro en la ruta

        // Inicializar la variable para el mensaje de error
        $errorMessage = null;

        // Verificar si el usuario existe
        if (!$user) {
            $errorMessage = trans('Data not found');
        } elseif ($user->email_verified_at !== null) {
            abort(403, trans('mail already verified.'));
        } else {
            // Intentar marcar el correo electrónico como verificado
            try {
                if ($user->markEmailAsVerified()) {
                    event(new Verified($user)); // Disparar el evento de verificación

                    // Redirigir al dashboard con un parámetro de verificación
                    return redirect()->intended(route('dashboard', false) . '?verified=1');
                }
            } catch (\Exception $e) {
                // Registrar el error
                $errorMessage = trans(config('constants.MESSAGES.DATA_ERROR')) . trans(config('constants.MESSAGES.ERROR_TRYING_TO_VERIFY_EMAIL')) . ' data: ' . $e->getMessage();
                Log::error($errorMessage);
            }
        }

        // Si hay un mensaje de error, redirigir con el mensaje correspondiente
        if ($errorMessage) {
            return $this->help::jsonResponse(false, $errorMessage, config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }
}

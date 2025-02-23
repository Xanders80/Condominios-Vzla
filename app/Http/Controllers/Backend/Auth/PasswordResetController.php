<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Muestra la vista para solicitar un enlace de restablecimiento de contraseña.
     *
     * */
    public function showResetLinkEmail(): View
    {
        return view('backend.auth.forgot-password');
    }

    /**
     * Maneja la solicitud para enviar un enlace de restablecimiento de contraseña.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendResetLinkEmail(Request $request)
    {
        $status = false;
        $message = trans(config('constants.MESSAGES.MESS_BAD_REQUEST'));
        $httpStatus = config('constants.STATUS_CODES.NOT_FOUND');
        $data = null;

        // Validar los datos utilizando el método validate del modelo Colegio
        $validation = User::validateSendEmail($request->only('email'));

        if ($validation->fails()) {
            return $this->help::jsonResponse($status, $message, $httpStatus, $validation->errors()->toArray());
        } else {
            try {
                $status = Password::sendResetLink($request->only('email'));
                $message = trans($status);
                $status = $status === Password::RESET_LINK_SENT;
                $data = $status ? [$message] : $request->only('email');
                $httpStatus = config('constants.STATUS_CODES.OK');
            } catch (\Exception $e) {
                // Registrar cualquier excepción que ocurra al crear el usuario
                Log::error(trans('Internal Server Error, please try again later'), [
                    'error' => $e->getMessage(),
                    'input' => $request->only('email'),
                ]);

                $httpStatus = config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR');
                $message = trans('An error occurred while trying to send the reset link');
            }
        }

        return $this->help::jsonResponse($status, $message, $httpStatus, [], $data);
    }
}

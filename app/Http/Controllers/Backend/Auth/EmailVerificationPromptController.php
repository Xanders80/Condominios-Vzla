<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailVerificationPromptController extends Controller
{
    /**
     * Muestra el aviso de verificación de correo electrónico.
     *
     * @return redirect
     */
    public function __invoke(Request $request)
    {
        // Intentar mostrar el aviso de verificación de correo electrónico
        try {
            // Verificar si el usuario ya ha verificado su correo electrónico
            if ($request->user()->hasVerifiedEmail()) {
                return redirect()->route('dashboard'); // Redirigir al dashboard si ya está verificado
            }

            return view('auth.verify-email'); // Mostrar vista de verificación si no está verificado
        } catch (\Exception $e) {
            // Registrar el error crítico con información del usuario
            Log::critical(trans('Error show info email verification'), [
                'user_id' => $request->user()->id ?? null, // Usar null si no hay usuario
                'error_message' => $e->getMessage(),
            ]);

            // Redirigir a la página principal con un mensaje de error
            return redirect('/')->with('error', trans('Error show info email verification'));
        }
    }
}

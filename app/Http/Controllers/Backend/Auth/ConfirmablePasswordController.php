<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class ConfirmablePasswordController extends Controller
{
    /**
     * Muestra la vista de confirmación de contraseña.
     *
     * @return RedirectResponse
     */
    public function show()
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirma la contraseña del usuario.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validatePassword($request);
            $request->session()->put('auth.password_confirmed_at', now());

            return redirect()->intended('dashboard');
        } catch (ValidationException $e) {
            // Registrar los errores de validación
            Log::error('Error de validación al confirmar la contraseña:', [
                'errors' => $e->validator->errors(),
                'input' => $request->all(),
            ]);

            // Volver a lanzar la excepción para manejarla en otro lugar
            throw $e;
        } catch (\Exception $e) {
            // Registrar el error crítico
            Log::critical('Error crítico al confirmar la contraseña del usuario', [
                'user_id' => $request->user() ? $request->user()->id : null,
                'error_message' => $e->getMessage(),
            ]);

            // Redirigir con un mensaje de error
            return redirect()->back()->with('error', 'Ocurrió un error al intentar confirmar la contraseña.');
        }
    }

    /**
     * Valida la contraseña del usuario.
     *
     * @throws ValidationException
     */
    protected function validatePassword(Request $request)
    {
        try {
            // Cargar la lista de contraseñas comunes desde el archivo de configuración
            $commonPasswords = config('common_passwords.common_passwords');

            $commonPasswordsFormatted = array_map(function ($password) {
                return ucfirst(strtolower($password));
            }, $commonPasswords);

            // Convertir el array a una cadena separada por comas
            $notInList = implode(',', $commonPasswordsFormatted);

            $request->validate([
                'password' => [
                    'required',
                    'not_in:' . $notInList,
                    'regex:/^(?=.*[0-9])(?=.*[#$%^&*()_\-+=@!,.<>?;:])[A-Za-z].{8,20}$',
                    Rules\Password::defaults(),
                ],
            ], [
                'password.not_in' => 'La nueva contraseña no puede ser una de las contraseñas comunes.',
                'password.regex' => 'La nueva contraseña debe contener al menos un número, un carácter especial (#$.\-@*¬) y una letra mayúscula, y tener entre 8 y 20 caracteres.',
            ]);

            if (!Auth::guard('web')->attempt(['email' => $request->user()->email, 'password' => $request->password])) {
                throw ValidationException::withMessages(['password' => trans('auth.password')]);
            }
        } catch (ValidationException $e) {
            // Registrar los errores de validación
            Log::error('Error de validación en la validación de contraseña:', [
                'errors' => $e->validator->errors(),
                'input' => $request->all(),
            ]);

            // Volver a lanzar la excepción para manejarla en otro lugar
            throw $e;
        }
    }
}

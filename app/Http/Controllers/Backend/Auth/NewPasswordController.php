<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class NewPasswordController extends Controller
{
    private array $response = [
        'status' => 200,
        'message' => 'OK',
        'data' => null,
    ];

    /**
     * Muestra la vista para restablecer la contraseña.
     *
     * @return View
     */
    public function showResetPassword(Request $request, $token)
    {
        // Verificar si el email existe en la tabla password_reset_tokens
        $tokenRecord = PasswordResetToken::where('email', $request->input('email'))
            ->first();

        if (!$tokenRecord) {
            abort(419, trans('The reset token is invalid or has expired.'));
        }

        // Si el token es válido, mostrar la vista de restablecimiento de contraseña
        return view('backend.auth.reset-password', ['request' => $request]);
    }

    /**
     * Procesa la solicitud para restablecer la contraseña.
     *
     * @return RedirectResponse
     */
    public function storeResetPassword(Request $request)
    {
        // Validar los datos utilizando el método validate del modelo Colegio
        $validation = User::validateNewPassword($request->all());

        if ($validation->fails()) {
            $this->response['status'] = 400;
            $this->response['message'] = trans('Bad Request, please check your input');
            $this->response['data'] = $validation->errors();
        } else {
            try {
                // Intentar restablecer la contraseña
                $status = Password::reset(
                    $request->only('email', 'password', 'password_confirmation', 'token'),
                    fn ($user, $password) => $this->updatePassword($user, $password)
                );

                if ($status === Password::PASSWORD_RESET) {
                    // Busca el registro por ID
                    $data = User::where('email', $request->input('email'))->firstOrFail();
                    // Actualiza el registro
                    if ($data->update($request->all())) {
                        $this->response['message'] = trans($status);
                    }
                } else {
                    $this->response['data'] = $request->input('email');
                    $this->response['message'] = trans($status); // 'El token de restablecimiento de contraseña no es válido.'
                }
            } catch (\Exception $e) {
                // Registrar cualquier excepción que ocurra al crear el usuario
                Log::error(trans('Internal Server Error, please try again later'), [
                    'error' => $e->getMessage(),
                    'input' => $request->only('email'),
                ]);

                $this->response['status'] = 500;
                $this->response['message'] = trans('An error occurred while trying to reset your password');
            }
        }

        return response()->json($this->response, $this->response['status']);
    }

    /**
     * Actualiza la contraseña del usuario.
     *
     * @param User $user
     */
    protected function updatePassword($user, string $newPassword): void
    {
        $user->update(['password' => Hash::make($newPassword)]);
        event(new PasswordReset($user));
    }
}

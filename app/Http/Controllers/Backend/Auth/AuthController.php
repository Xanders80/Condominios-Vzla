<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private array $response = [
        'status' => 200,
        'message' => 'OK',
        'data' => null,
    ];

    /**
     * Muestra el formulario de inicio de sesión o redirige al dashboard si ya está autenticado.
     *
     * @return \Illuminate\View\View|RedirectResponse
     */
    public function formlogin()
    {
        return auth()->check() ? to_route('dashboard') : view('backend.auth.login');
    }

    public function termsofuse(Request $request)
    {
        return view('backend.auth.terms-of-use');
    }

    /**
     * Muestra el formulario de registro.
     *
     * @return \Illuminate\View\View
     */
    public function formRegister()
    {
        return view('backend.auth.register');
    }

    /**
     * Maneja el registro de un nuevo usuario.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $status = false;
        $message = trans(config('constants.MESSAGES.MESS_BAD_REQUEST'));
        $httpStatus = config('constants.STATUS_CODES.NOT_FOUND');
        $data = null;

        // Validar los datos utilizando el método validate del modelo Colegio
        $validation = User::validateRegister($request->all());

        if ($validation->fails()) {
            return $this->handleValidationFailure($validation);
        } else {
            try {
                $request->merge(['password' => $request->password, 'level_id' => 3, 'access_group_id' => 3]);
                $user = User::create($request->all());

                $data = [$user];
                $message = trans('User created successfully');
                $httpStatus = config('constants.STATUS_CODES.OK');
                $status = true;

                // Disparar el evento de registro
                event(new Registered($user));

                // Enviar el correo de verificación
                $user->sendEmailVerificationNotification();
                $user->sendEmailVerificationNotificationAt();
            } catch (\Exception $e) {
                // Registrar cualquier excepción que ocurra al crear el usuario
                Log::error('Error creating user', [
                    'error' => $e->getMessage(),
                    'input' => $request->all(),
                ]);
                $httpStatus = config('constants.STATUS_CODES.NOT_FOUND');
                $message = trans('Internal Server Error, please try again later');
            }
        }

        return $this->help::jsonResponse($status, $message, $httpStatus, [], $data);
    }

    /**
     * Maneja el inicio de sesión de un usuario.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validation = User::validateLogin($request->all());

        if ($validation->fails()) {
            return $this->handleValidationFailure($validation);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->handleUnauthorizedAccess();
        }

        return $this->handleUserLogin($request, $user);
    }

    /**
     * Destruye una sesión autenticada.
     *
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        // Intentar cerrar la sesión y manejar errores potenciales
        try {
            if ($request->user()) {
                $request->user()->tokens()->delete();
            }

            // Cerrar sesión del usuario
            Auth::guard('web')->logout();

            // Invalidar la sesión actual
            $request->session()->invalidate();

            // Regenerar el token de sesión para mayor seguridad
            $request->session()->regenerateToken();

            // Redirigir al usuario a la página de inicio
            $this->response['message'] = trans('User logged out successfully');

            return response()->json($this->response, $this->response['status']);
        } catch (\Exception $e) {
            // Registrar el error crítico
            Log::critical(trans('Critical error while logging out user'), [
                'user_id' => $request->user() ? $request->user()->id : null,
                'error_message' => $e->getMessage(),
            ]);

            // Redirigir al usuario a la página de inicio con un mensaje de error
            $this->response['message'] = trans('Critical error while logging out user');

            return response()->json($this->response, $this->response['status']);
        }
    }

    /**
     * Devuelve la información del usuario autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        // Devolver los datos del usuario autenticado
        $this->response['data'] = auth()->user();

        return response()->json($this->response, $this->response['status']);
    }

    /**
     * Asegura que la solicitud de inicio de sesión no esté limitada por la tasa.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            event(new Lockout($request));

            $seconds = RateLimiter::availableIn($this->throttleKey($request));
            throw ValidationException::withMessages(['email' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)])]);
        }
    }

    /**
     * Obtiene la clave de limitación de tasa para la solicitud.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower(Str::transliterate($request->input('email'))) . '|' . $request->ip();
    }

    private function handleValidationFailure($validation)
    {
        $this->response['status'] = 400;
        $this->response['message'] = trans('Bad Request');
        $this->response['data'] = $validation->errors();

        return response()->json($this->response, $this->response['status']);
    }

    private function handleUnauthorizedAccess()
    {
        $this->response['status'] = 401;
        $this->response['message'] = trans('Unauthorized Access, please check your credentials.');

        return response()->json($this->response, $this->response['status']);
    }

    private function handleUserLogin(Request $request, $user)
    {
        if (!$user->hasVerifiedEmail()) {
            return $this->handleUnverifiedEmail($user);
        }

        $this->ensureIsNotRateLimited($request);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember == 'true')) {
            return $this->logUserLogin($request, $user);
        } else {
            RateLimiter::hit($this->throttleKey($request), 180);
            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }
    }

    private function handleUnverifiedEmail($user)
    {
        if ($user->hasEmailVerificationExpired()) {
            $this->response['status'] = 410; // Gone
            $this->response['message'] = trans('The email verification link has expired. Please request a new verification link');
            $this->response['data'] = [
                'request_new_link' => true, // Indica que se puede solicitar un nuevo enlace
            ];
        } else {
            $this->response['status'] = 403; // Forbidden
            $this->response['message'] = trans('Please verify your email address before logging in.');
        }

        return response()->json($this->response, $this->response['status']);
    }

    private function logUserLogin(Request $request, $user)
    {
        $user->log()->create([
            'ip' => $request->ip(),
            'data' => [
                'platform' => $request->device_name ?? 'web',
                'browser' => $request->header('User-Agent') ?? 'web',
            ],
            'user_agent' => $request->userAgent(),
        ]);

        $request->session()->regenerate();
        $this->response['status'] = 200; // OK
        $this->response['message'] = trans('User logged in successfully');

        return response()->json($this->response, $this->response['status']);
    }
}

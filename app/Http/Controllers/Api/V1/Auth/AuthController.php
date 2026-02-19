<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Resources\V1\Auth\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for user authentication"
 * )
 */
class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully. Please verify your email."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                ...$request->validated(),
                'password' => $request->password,
                'level_id' => 3,
                'access_group_id' => 3,
            ]);

            event(new Registered($user));

            // Enviar email de verificación de forma segura
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Exception $e) {
                Log::warning('Could not send verification email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return $this->successResponse(
                'User registered successfully. Please verify your email.',
                ['user' => new UserResource($user)],
                201
            );
        } catch (\Exception $e) {
            Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'input' => $request->all(),
            ]);

            return $this->errorResponse(
                'Internal Server Error, please try again later',
                500
            );
        }
    }

    /**
     * Login user
     *
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="Authenticate user and return token",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="remember", type="boolean", example=true),
     *             @OA\Property(property="device_name", type="string", example="web")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged in successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User"),
     *                 @OA\Property(property="token", type="string", example="1|laravel_sanctum_token...")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Email not verified"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many requests"
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->errorResponse('Unauthorized Access, please check your credentials.', 401);
        }

        return $this->handleUserLogin($request, $user);
    }

    /**
     * Logout user
     *
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     summary="Logout authenticated user",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged out successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user) {
                // Revocar tokens de API
                $user->tokens()->delete();

                // Cerrar sesión web
                Auth::guard('web')->logout();
            }

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->successResponse('User logged out successfully');
        } catch (\Exception $e) {
            Log::critical('Critical error while logging out user', [
                'user_id' => $request->user()?->id,
                'error_message' => $e->getMessage(),
            ]);

            return $this->errorResponse('Critical error while logging out user', 500);
        }
    }

    /**
     * Get authenticated user
     *
     * @OA\Get(
     *     path="/api/v1/auth/user",
     *     summary="Get authenticated user data",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User data retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function user(Request $request): JsonResponse
    {
        return $this->successResponse(
            'User retrieved successfully',
            ['user' => new UserResource($request->user())]
        );
    }

    /**
     * Manejar login con rate limiting y verificación
     */
    private function handleUserLogin(Request $request, User $user): JsonResponse
    {
        // Verificar email verificado
        if (! $user->hasVerifiedEmail()) {
            return $this->handleUnverifiedEmail($user);
        }

        // Rate limiting
        $this->ensureIsNotRateLimited($request);

        // Intentar autenticación
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            return $this->logUserLogin($request, $user);
        }

        RateLimiter::hit($this->throttleKey($request), 180);

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Manejar email no verificado
     */
    private function handleUnverifiedEmail(User $user): JsonResponse
    {
        if ($user->hasEmailVerificationExpired()) {
            return $this->errorResponse(
                'The email verification link has expired. Please request a new verification link.',
                410,
                ['request_new_link' => true]
            );
        }

        return $this->errorResponse(
            'Please verify your email address before logging in.',
            403
        );
    }

    /**
     * Log del login exitoso
     */
    private function logUserLogin(Request $request, User $user): JsonResponse
    {
        $user->log()->create([
            'ip' => $request->ip(),
            'data' => [
                'platform' => $request->device_name ?? 'web',
                'browser' => $request->header('User-Agent'),
            ],
            'user_agent' => $request->userAgent(),
        ]);

        $request->session()->regenerate();

        return $this->successResponse(
            'User logged in successfully',
            [
                'user' => new UserResource($user),
                'token' => $user->createToken('api-token')->plainTextToken,
            ]
        );
    }

    /**
     * Verificar rate limiting
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            $seconds = RateLimiter::availableIn($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => [trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ])],
            ]);
        }
    }

    /**
     * Generar clave de rate limiting
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower(Str::transliterate($request->input('email'))).'|'.$request->ip();
    }
}

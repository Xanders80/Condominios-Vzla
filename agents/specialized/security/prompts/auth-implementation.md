# Authentication Implementation Guide - Security

## Auth Architecture for Condominios-Vzla

### 1. Web Authentication (Laravel Session)
```php
// Login flow
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        if (!Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Please verify your email first.',
            ]);
        }

        return redirect()->intended(route('dashboard.index'));
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
}
```

### 2. API Authentication (Laravel Sanctum)
```php
// Register and create token
public function register(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    $token = $user->createToken('api-token', ['*'])->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => new UserResource($user),
    ]);
}

// Login and create token
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = User::where('email', $credentials['email'])->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    if (!$user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email not verified'], 403);
    }

    $token = $user->createToken('api-token', ['*'])->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => new UserResource($user),
    ]);
}

// Logout (revoke token)
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully']);
}
```

### 3. Role-Based Access Control
```php
// Middleware: userRoles
// Checks if authenticated user has access to the current route

// Access check in controller
public function edit($id)
{
    $user = Auth::user();
    
    // Check via access_menus table
    if (!$user->hasAccess('condominiums.edit')) {
        abort(403, 'Unauthorized action.');
    }
    
    // ...
}

// User model methods
public function hasAccess($routeName): bool
{
    return $this->levels()->whereHas('accessMenus', function ($query) use ($routeName) {
        $query->where('route_name', $routeName);
    })->exists();
}

public function isAdmin(): bool
{
    return $this->levels()->where('name', 'admin')->exists();
}
```

### 4. Password Reset Flow
```php
// Request reset link
public function sendResetLinkEmail(Request $request)
{
    $request->validate(['email' => 'required|email|exists:users,email']);

    $token = Password::createToken(
        User::where('email', $request->email)->first()
    );

    // Send email with reset link
    Mail::to($request->email)->send(
        new ResetPasswordNotification($token)
    );

    return back()->with('status', 'Reset link sent!');
}

// Reset password
public function reset(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => ['required', 'min:8', 'confirmed'],
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
}
```

### 5. Email Verification
```php
// User model must implement MustVerifyEmail
class User extends Authenticatable implements MustVerifyEmail

// Middleware on protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Routes requiring verified email
});

// Resend verification
public function resend(Request $request)
{
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->intended(route('dashboard.index'));
    }

    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
}
```

### 6. Session Security
```php
// config/session.php
'lifetime' => 120,
'expire_on_close' => false,
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => true,
'same_site' => 'lax',

// Regenerate session on login
$request->session()->regenerate();

// Regenerate on privilege change (password update, role change)
$request->session()->regenerate();
```

### 7. API Token Abilities
```php
// Create token with specific abilities
$token = $user->createToken('condo-token', [
    'condominiums:read',
    'condominiums:write',
    'payments:read',
    'payments:write',
])->plainTextToken;

// Check token abilities in middleware
if (!$request->user()->tokenCan('payments:write')) {
    abort(403, 'Token lacks permission');
}
```

### 8. Rate Limiting
```php
// config/rate-limiter
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinutes(5, 10)->by($request->email . '|' . $request->ip());
});

// Apply to routes
Route::middleware('throttle:login')->group(function () {
    Route::post('sign-in', [AuthController::class, 'login']);
});
```

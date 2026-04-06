# Backend Specialist - Security Patterns

## Security Implementation for Laravel Backend

### 1. Input Validation
```php
// Always use Form Request
class CondominiumRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'rif' => ['required', 'string', 'max:20', 'unique:condominiums,rif,' . $this->condominium],
            'email' => ['nullable', 'email', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
        ];
    }
}
```

### 2. SQL Injection Prevention
```php
// SAFE: Eloquent parameter binding
User::where('email', $request->email)->first();

// SAFE: Query builder with binding
DB::table('users')->where('email', $request->email)->get();

// DANGEROUS: Raw query without binding (NEVER DO THIS)
DB::select("SELECT * FROM users WHERE email = '$request->email'");

// SAFE: Raw query with binding
DB::select("SELECT * FROM users WHERE email = ?", [$request->email]);
```

### 3. Mass Assignment Protection
```php
class User extends Model
{
    // ALWAYS use fillable, NEVER guarded = []
    protected $fillable = [
        'name', 'email', 'password', 'is_active',
    ];
    
    // Sensitive fields should NOT be fillable
    // is_admin, role_id, password (set separately)
}
```

### 4. Authorization
```php
// In Controller
public function update(CondominiumRequest $request, Condominium $condominium)
{
    $this->authorize('update', $condominium);
    // ...
}

// In Policy
public function update(User $user, Condominium $condominium): bool
{
    return $user->hasAccess('condominiums.edit')
        && $user->condominium_id === $condominium->id;
}
```

### 5. File Upload Security
```php
// Validate file
$request->validate([
    'document' => 'required|file|mimes:pdf,jpg,png|max:5120',
]);

// Store with safe name
$file = $request->file('document');
$path = $file->storeAs(
    'documents',
    uniqid() . '.' . $file->getClientOriginalExtension(),
    'public'
);

// Never use original filename directly
```

### 6. API Security
```php
// Sanctum token with limited abilities
$token = $user->createToken('api-token', ['condominiums:read', 'payments:write']);

// Rate limiting
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // ...
});

// CORS configuration in config/cors.php
```

### 7. Password Security
```php
// Hashing (automatic with Laravel's Hash facade)
Hash::make($password);
Hash::check($password, $hashedPassword);

// Password validation rules
'password' => [
    'required',
    'string',
    'min:8',
    'confirmed',
    Password::defaults(), // Uses common_passwords.php config
],
```

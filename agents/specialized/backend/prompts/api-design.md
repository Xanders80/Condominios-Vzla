# Backend Specialist - API Design Patterns

## RESTful API Conventions for Condominios-Vzla

### 1. URL Structure
```
GET    /api/v1/{resource}           # List (paginated)
POST   /api/v1/{resource}           # Create
GET    /api/v1/{resource}/{id}      # Show
PUT    /api/v1/{resource}/{id}      # Update
DELETE /api/v1/{resource}/{id}      # Delete
GET    /api/v1/{resource}/{id}/{relation}  # Show relation
```

### 2. Response Format (Success)
```json
{
    "success": true,
    "data": { ... },
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

### 3. Response Format (Error)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."]
    }
}
```

### 4. Pagination
```php
public function index(Request $request)
{
    $perPage = $request->input('per_page', 15);
    $perPage = min($perPage, 100); // Cap at 100
    
    return Model::paginate($perPage);
}
```

### 5. Filtering
```php
public function index(Request $request)
{
    $query = Model::query();
    
    if ($request->has('search')) {
        $query->where('name', 'like', "%{$request->search}%");
    }
    
    if ($request->has('status')) {
        $query->where('status', $request->status);
    }
    
    if ($request->has('sort')) {
        $query->orderBy($request->sort, $request->input('direction', 'asc'));
    }
    
    return $query->paginate($request->input('per_page', 15));
}
```

### 6. Eager Loading Relations
```php
public function show($id)
{
    $allowedRelations = ['state', 'city', 'towers', 'units'];
    $with = $this->parseIncludeParam($allowedRelations);
    
    return new Resource(
        Model::with($with)->findOrFail($id)
    );
}
```

### 7. Rate Limiting
```php
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::apiResource('condominiums', CondominiumController::class);
});
```

### 8. API Versioning Strategy
- Current: v1 (auth only)
- Future: v1 (full CRUD), v2 (breaking changes)
- Version in URL: `/api/v1/...`
- Never break v1 without creating v2

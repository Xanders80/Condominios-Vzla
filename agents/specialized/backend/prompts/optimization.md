# Backend Specialist - Query Optimization

## Eloquent Performance for Condominios-Vzla

### 1. Eager Loading (N+1 Prevention)
```php
// BAD: N+1 queries
$condominiums = Condominium::all();
foreach ($condominiums as $condo) {
    echo $condo->state->name; // Query per iteration
}

// GOOD: Eager loading
$condominiums = Condominium::with('state', 'city', 'municipality')->get();
foreach ($condominiums as $condo) {
    echo $condo->state->name; // No additional queries
}

// BETTER: Select only needed columns
$condominiums = Condominium::with(['state:id,name', 'city:id,name'])
    ->select('id', 'name', 'state_id', 'city_id')
    ->get();
```

### 2. Chunking Large Datasets
```php
// BAD: Load all into memory
$allPayments = Payment::all();

// GOOD: Process in chunks
Payment::chunk(500, function ($payments) {
    foreach ($payments as $payment) {
        // Process each payment
    }
});

// BETTER: Cursor for memory efficiency
foreach (Payment::cursor() as $payment) {
    // Process each payment (one at a time)
}
```

### 3. Query Caching
```php
// Cache expensive queries
$stats = Cache::remember('condominium_stats_' . $condoId, 3600, function () use ($condoId) {
    return [
        'total_units' => Unit::where('condominium_id', $condoId)->count(),
        'active_dwellers' => Dweller::whereHas('unit', fn($q) => $q->where('condominium_id', $condoId))
            ->where('is_active', true)->count(),
        'pending_payments' => Payment::where('condominium_id', $condoId)
            ->where('status', 'pending')->sum('amount'),
    ];
});
```

### 4. Database Indexes
```php
// Add indexes for frequently queried columns
$table->index(['condominium_id', 'is_active']);
$table->index(['status', 'due_date']);
$table->index('rif'); // Unique is auto-indexed

// Composite indexes for multi-column queries
$table->index(['condominium_id', 'period', 'type']);
```

### 5. Count Optimization
```php
// BAD: Load all then count
$count = Model::all()->count();

// GOOD: Database-level count
$count = Model::count();

// BEST: Conditional count
$count = Model::where('is_active', true)->count();

// With exists check (faster than count for existence)
$hasRecords = Model::where('status', 'pending')->exists();
```

### 6. Select Specific Columns
```php
// BAD: Select all columns
$users = User::all();

// GOOD: Only what you need
$users = User::select('id', 'name', 'email')->get();

// Using pluck for single column
$names = User::pluck('name', 'id');
```

### 7. Avoid SELECT * in DataTables
```php
// BAD
return datatables()->of(Model::all())->toJson();

// GOOD
return datatables()->of(
    Model::select('id', 'name', 'status', 'created_at')
        ->with(['relation:id,name'])
)->toJson();
```

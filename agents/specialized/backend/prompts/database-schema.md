# Backend Specialist - Database Schema Patterns

## Database Conventions for Condominios-Vzla

### 1. Table Naming
- Plural, snake_case: `condominiums`, `common_areas`, `assembly_sessions`
- Pivot tables: singular, alphabetical: `bank_condominium`, `condominium_user`

### 2. Column Conventions
```php
// Primary key (auto)
$table->id();

// Timestamps (always)
$table->timestamps();

// Soft deletes (when logical deletion needed)
$table->softDeletes();

// Foreign keys
$table->foreignId('state_id')->constrained('state_addresses')->nullOnDelete();

// Status fields
$table->boolean('is_active')->default(true);
$table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

// Monetary values (always decimal)
$table->decimal('amount', 15, 2);
$table->decimal('balance', 15, 2)->default(0);

// Dates
$table->date('due_date')->nullable();
$table->dateTime('paid_at')->nullable();

// Text fields
$table->string('name', 255);
$table->string('rif', 20)->unique();
$table->text('description')->nullable();
$table->longText('content')->nullable();
```

### 3. Index Strategy
```php
// Foreign keys (auto-indexed by foreignId)
$table->foreignId('state_id')->constrained();

// Search columns
$table->index(['name', 'is_active']);

// Unique constraints
$table->unique(['condominium_id', 'period', 'type']);

// Full-text for search
$table->fullText(['name', 'description']);
```

### 4. Common Relationships
```php
// One-to-Many
public function units(): HasMany { return $this->hasMany(Unit::class); }
public function condominium(): BelongsTo { return $this->belongsTo(Condominium::class); }

// Many-to-Many
public function condominiums(): BelongsToMany {
    return $this->belongsToMany(Condominium::class, 'bank_condominium');
}

// Polymorphic
public function attachable(): MorphTo { return $this->morphTo(); }
public function attachments(): MorphMany { return $this->morphMany(Attachment::class, 'attachable'); }

// Has-One-Through
public function municipality(): HasOneThrough {
    return $this->hasOneThrough(
        MunicipalityAddress::class,
        StateAddress::class,
        'id', 'id', 'state_id', 'municipality_id'
    );
}
```

### 5. Query Scopes
```php
public function scopeActive($query) {
    return $query->where('is_active', true);
}

public function scopeForCondominium($query, $condominiumId) {
    return $query->where('condominium_id', $condominiumId);
}

public function scopeOverdue($query) {
    return $query->where('due_date', '<', now())
                 ->where('status', 'pending');
}

public function scopeWithBalance($query, $operator, $amount) {
    return $query->whereRaw('amount - COALESCE((SELECT SUM(amount) FROM payments WHERE debt_id = debts.id), 0) ' . $operator . ' ?', [$amount]);
}
```

### 6. Migration Order
1. Base tables (users, levels, access_groups)
2. Address tables (countries, states, cities, municipalities)
3. Core tables (condominiums, towers, floors, units)
4. People tables (dwellers, suppliers)
5. Financial tables (payments, receipts, debts, expenses)
6. Operations tables (common_areas, bookings, work_orders)
7. Governance tables (assembly_sessions, motions, votes)

# Test Strategy Guide - QA

## Testing Strategy for Condominios-Vzla

### 1. Testing Pyramid
```
        /  E2E Tests  \        (5%  - Critical user flows)
       / Feature Tests \       (25% - HTTP endpoints, CRUD, API)
      /  Unit Tests     \     (70% - Models, Services, Helpers)
```

### 2. Unit Testing Strategy
```php
// What to test:
// - Model methods and scopes
// - Service class logic
// - Helper functions
// - Calculation formulas
// - Validation rules

// What NOT to test:
// - Framework code (Laravel internals)
// - Simple getters/setters
// - Database queries (use Feature tests)

// Example: Financial calculation
public function test_interest_calculation_respects_grace_period(): void
{
    $debt = Debt::factory()->create([
        'amount' => 1000,
        'due_date' => now()->subDays(15),
    ]);

    $calculation = InterestCalculation::calculate($debt, now(), 15);

    $this->assertEquals(0, $calculation->interest);
    $this->assertEquals('current', $debt->status);
}
```

### 3. Feature Testing Strategy
```php
// What to test:
// - CRUD operations (index, create, store, edit, update, delete)
// - Authentication flows (login, logout, password reset)
// - Authorization (role-based access)
// - API endpoints (request/response format)
// - Form validation (valid and invalid data)

// Minimum tests per CRUD module (8):
// 1. index returns 200 for authenticated user
// 2. index redirects for unauthenticated user
// 3. data returns JSON for DataTables
// 4. store with valid data succeeds
// 5. store with invalid data fails validation
// 6. update modifies database correctly
// 7. delete removes record (soft delete)
// 8. unauthorized access is blocked
```

### 4. API Testing Strategy
```php
// What to test:
// - Authentication required (401 without token)
// - Token-based access (Sanctum)
// - Request validation
// - Response format (API Resource structure)
// - Pagination
// - Filtering and sorting
// - Error responses

public function test_api_requires_authentication(): void
{
    $response = $this->getJson(route('api.condominiums.index'));
    $response->assertUnauthorized();
}

public function test_api_returns_paginated_response(): void
{
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Condominium::factory()->count(20)->create();

    $response = $this->getJson(route('api.condominiums.index', ['per_page' => 10]));

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => ['*' => ['id', 'name']],
        'links',
        'meta' => ['current_page', 'last_page', 'total'],
    ]);
}
```

### 5. Test Data Strategy
```php
// Use factories for all test data
// Define relationships in factories
// Use states for different scenarios

class CondominiumFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'rif' => 'J-' . $this->faker->numerify('########-#'),
            'address' => $this->faker->address,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }
}
```

### 6. Coverage Targets
```yaml
minimum_coverage:
  models: "90%"
  services: "95%"
  controllers: "80%"
  api_endpoints: "85%"
  helpers: "100%"
  views: "N/A (feature tests cover rendering)"

priority_modules:
  - "payments"
  - "receipts"
  - "debts"
  - "interest_calculations"
  - "common_expenses"
  - "auth"
```

### 7. Test Organization
```
tests/
├── Feature/
│   ├── Api/V1/
│   │   ├── Auth/
│   │   │   ├── AuthControllerTest.php
│   │   │   └── PasswordControllerTest.php
│   │   └── {Module}ControllerTest.php
│   ├── {Module}Test.php
│   └── AuthorizationTest.php
├── Unit/
│   ├── Models/
│   │   └── {Model}Test.php
│   ├── Services/
│   │   └── {Service}Test.php
│   └── Helpers/
│       └── HelperTest.php
├── CreatesApplication.php
└── TestCase.php
```

### 8. Database Testing
```php
// Always use RefreshDatabase trait
class ModuleTest extends TestCase
{
    use RefreshDatabase;

    // Assertions:
    // assertDatabaseHas('table', ['column' => 'value'])
    // assertDatabaseMissing('table', ['column' => 'value'])
    // assertDatabaseCount('table', 5)
    // assertSoftDeleted('table', ['id' => 1])
    // assertNotSoftDeleted('table', ['id' => 1])
}
```

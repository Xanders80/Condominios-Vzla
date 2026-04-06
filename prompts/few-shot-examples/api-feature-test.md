# Few-Shot Example: API Feature Test

## Input
Write feature tests for the Condominium API endpoints (index, show) with Sanctum auth.

## Output

### `tests/Feature/Api/V1/CondominiumControllerTest.php`
```php
<?php

namespace Tests\Feature\Api\V1;

use App\Models\Condominium;
use App\Models\StateAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CondominiumControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_list_condominiums(): void
    {
        $response = $this->getJson(route('api.condominiums.index'));

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_condominiums(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Condominium::factory()->count(3)->create();

        $response = $this->getJson(route('api.condominiums.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'rif', 'address', 'is_active', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);
    }

    public function test_can_search_condominiums_by_name(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Condominium::factory()->create(['name' => 'Torre Norte']);
        Condominium::factory()->create(['name' => 'Torre Sur']);

        $response = $this->getJson(route('api.condominiums.index', ['search' => 'Norte']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => 'Torre Norte']);
    }

    public function test_can_show_single_condominium(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $state = StateAddress::factory()->create();
        $condominium = Condominium::factory()->create(['state_id' => $state->id]);

        $response = $this->getJson(route('api.condominiums.show', $condominium));

        $response->assertOk();
        $response->assertJson([
            'data' => [
                'id' => $condominium->id,
                'name' => $condominium->name,
                'state' => $state->name,
            ],
        ]);
    }

    public function test_show_returns_404_for_nonexistent_condominium(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.condominiums.show', 999));

        $response->assertNotFound();
    }
}
```

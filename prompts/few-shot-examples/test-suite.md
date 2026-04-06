# Few-Shot Example: Feature Test Suite

## Input
Write feature tests for the Supplier CRUD module.

## Output

### `tests/Feature/SupplierTest.php`
```php
<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_authenticated_user_can_access_supplier_index(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('supplier.index'));

        $response->assertOk();
        $response->assertSee('Suppliers');
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('supplier.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_supplier_data_returns_json_for_datatables(): void
    {
        Supplier::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->getJson(route('supplier.data'));

        $response->assertOk();
        $response->assertJsonStructure([
            'draw', 'recordsTotal', 'recordsFiltered', 'data',
        ]);
    }

    public function test_can_create_supplier(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('supplier.create'));

        $response->assertOk();
        $response->assertSee('Create Supplier');
    }

    public function test_can_store_supplier_with_valid_data(): void
    {
        $data = Supplier::factory()->make()->toArray();

        $response = $this->actingAs($this->admin)
            ->post(route('supplier.store'), $data);

        $response->assertRedirect(route('supplier.index'));
        $this->assertDatabaseHas('suppliers', ['name' => $data['name']]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('supplier.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_can_edit_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('supplier.edit', $supplier));

        $response->assertOk();
        $response->assertSee($supplier->name);
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create();
        $newData = ['name' => 'Updated Supplier Name'];

        $response = $this->actingAs($this->admin)
            ->put(route('supplier.update', $supplier), $newData);

        $response->assertRedirect(route('supplier.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'Updated Supplier Name']);
    }

    public function test_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('supplier.destroy', $supplier));

        $response->assertRedirect(route('supplier.index'));
        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }
}
```

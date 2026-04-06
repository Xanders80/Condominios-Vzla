# Feature Test Template

## Input Variables
- `{{module_name}}` - Module name (e.g., "Supplier")
- `{{model_name}}` - Model name (e.g., "Supplier")
- `{{route_prefix}}` - Route prefix (e.g., "supplier")
- `{{validation_fields}}` - Required fields for validation tests

## Template

```php
<?php

namespace Tests\Feature;

use App\Models\{{model_name}};
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {{module_name}}Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_authenticated_user_can_access_index(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('{{route_prefix}}.index'));

        $response->assertOk();
        $response->assertSee('{{module_name}}');
    }

    public function test_unauthenticated_user_is_redirected(): void
    {
        $response = $this->get(route('{{route_prefix}}.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_data_returns_json_for_datatables(): void
    {
        {{model_name}}::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->getJson(route('{{route_prefix}}.data'));

        $response->assertOk();
        $response->assertJsonStructure([
            'draw', 'recordsTotal', 'recordsFiltered', 'data',
        ]);
    }

    public function test_can_store_with_valid_data(): void
    {
        $data = {{model_name}}::factory()->make()->toArray();

        $response = $this->actingAs($this->admin)
            ->post(route('{{route_prefix}}.store'), $data);

        $response->assertRedirect(route('{{route_prefix}}.index'));
        $this->assertDatabaseHas('{{table_name}}', ['{{primary_field}}' => $data['{{primary_field}}']]);
    }

{{#each validation_fields}}
    public function test_store_requires_{{field}}(): void
    {
        $data = {{model_name}}::factory()->make()->toArray();
        unset($data['{{field}}']);

        $response = $this->actingAs($this->admin)
            ->post(route('{{route_prefix}}.store'), $data);

        $response->assertSessionHasErrors(['{{field}}']);
    }

{{/each}}
    public function test_can_update(): void
    {
        $item = {{model_name}}::factory()->create();
        $newData = ['{{update_field}}' => 'Updated Value'];

        $response = $this->actingAs($this->admin)
            ->put(route('{{route_prefix}}.update', $item), $newData);

        $response->assertRedirect(route('{{route_prefix}}.index'));
        $this->assertDatabaseHas('{{table_name}}', ['{{update_field}}' => 'Updated Value']);
    }

    public function test_can_delete(): void
    {
        $item = {{model_name}}::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('{{route_prefix}}.destroy', $item));

        $response->assertRedirect(route('{{route_prefix}}.index'));
{{#if soft_deletes}}
        $this->assertSoftDeleted('{{table_name}}', ['id' => $item->id]);
{{else}}
        $this->assertDatabaseMissing('{{table_name}}', ['id' => $item->id]);
{{/if}}
    }
}
```

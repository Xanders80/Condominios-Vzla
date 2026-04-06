/**
 * Test Generator Utility
 * 
 * Generate PHPUnit test files for:
 * - Feature tests (CRUD modules)
 * - Unit tests (Models, Services)
 * - API tests (RESTful endpoints)
 */

import { writeFile, fileExists } from '../../shared/file-operations';

interface TestConfig {
  moduleName: string;
  modelName: string;
  tableName: string;
  routePrefix: string;
  testType: 'feature' | 'unit' | 'api';
  fields: string[];
  relationships?: string[];
  validationRules?: Record<string, string>;
}

/**
 * Generate a complete test file
 */
export function generateTestFile(basePath: string, config: TestConfig): string {
  switch (config.testType) {
    case 'feature':
      return generateFeatureTest(basePath, config);
    case 'unit':
      return generateUnitTest(basePath, config);
    case 'api':
      return generateApiTest(basePath, config);
    default:
      throw new Error(`Unknown test type: ${config.testType}`);
  }
}

function generateFeatureTest(basePath: string, config: TestConfig): string {
  const className = `${config.moduleName}Test`;
  const filePath = `${basePath}/tests/Feature/${className}.php`;

  if (fileExists(filePath)) {
    throw new Error(`Test file already exists: ${filePath}`);
  }

  const content = `<?php

namespace Tests\\Feature;

use App\\Models\\${config.modelName};
use App\\Models\\User;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\TestCase;

class ${className} extends TestCase
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
            ->get(route('${config.routePrefix}.index'));

        $response->assertOk();
        $response->assertSee('${config.moduleName}');
    }

    public function test_unauthenticated_user_is_redirected(): void
    {
        $response = $this->get(route('${config.routePrefix}.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_data_returns_json_for_datatables(): void
    {
        ${config.modelName}::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->getJson(route('${config.routePrefix}.data'));

        $response->assertOk();
        $response->assertJsonStructure([
            'draw', 'recordsTotal', 'recordsFiltered', 'data',
        ]);
    }

    public function test_can_create(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('${config.routePrefix}.create'));

        $response->assertOk();
        $response->assertSee('Create ${config.moduleName}');
    }

    public function test_can_store_with_valid_data(): void
    {
        $data = ${config.modelName}::factory()->make()->toArray();

        $response = $this->actingAs($this->admin)
            ->post(route('${config.routePrefix}.store'), $data);

        $response->assertRedirect(route('${config.routePrefix}.index'));
        $this->assertDatabaseHas('${config.tableName}', ['id' => 1]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('${config.routePrefix}.store'), []);

        $response->assertSessionHasErrors();
    }

    public function test_can_update(): void
    {
        $item = ${config.modelName}::factory()->create();
        $newData = ['name' => 'Updated Name'];

        $response = $this->actingAs($this->admin)
            ->put(route('${config.routePrefix}.update', $item), $newData);

        $response->assertRedirect(route('${config.routePrefix}.index'));
        $this->assertDatabaseHas('${config.tableName}', ['name' => 'Updated Name']);
    }

    public function test_can_delete(): void
    {
        $item = ${config.modelName}::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('${config.routePrefix}.destroy', $item));

        $response->assertRedirect(route('${config.routePrefix}.index'));
        $this->assertDatabaseMissing('${config.tableName}', ['id' => $item->id]);
    }
}
`;

  writeFile(filePath, content);
  return filePath;
}

function generateUnitTest(basePath: string, config: TestConfig): string {
  const className = `${config.modelName}Test`;
  const filePath = `${basePath}/tests/Unit/${className}.php`;

  if (fileExists(filePath)) {
    throw new Error(`Test file already exists: ${filePath}`);
  }

  const content = `<?php

namespace Tests\\Unit;

use App\\Models\\${config.modelName};
use Tests\\TestCase;

class ${className} extends TestCase
{
    public function test_model_can_be_created(): void
    {
        $model = ${config.modelName}::factory()->create();
        $this->assertDatabaseHas('${config.tableName}', ['id' => $model->id]);
    }

    public function test_model_has_fillable_fields(): void
    {
        $model = new ${config.modelName}();
        $this->assertNotEmpty($model->getFillable());
    }

    public function test_model_has_relationships(): void
    {
        $model = new ${config.modelName}();
${(config.relationships || []).map(rel => `        $this->assertInstanceOf(\\Illuminate\\Database\\Eloquent\\Relations\\Relation::class, $model->${rel}());`).join('\n')}
    }
}
`;

  writeFile(filePath, content);
  return filePath;
}

function generateApiTest(basePath: string, config: TestConfig): string {
  const className = `${config.moduleName}ApiTest`;
  const filePath = `${basePath}/tests/Feature/Api/${className}.php`;

  if (fileExists(filePath)) {
    throw new Error(`Test file already exists: ${filePath}`);
  }

  const content = `<?php

namespace Tests\\Feature\\Api;

use App\\Models\\${config.modelName};
use App\\Models\\User;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Laravel\\Sanctum\\Sanctum;
use Tests\\TestCase;

class ${className} extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access(): void
    {
        $response = $this->getJson(route('api.${config.routePrefix}.index'));
        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        ${config.modelName}::factory()->count(3)->create();

        $response = $this->getJson(route('api.${config.routePrefix}.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['*' => ['id']],
            'links',
            'meta',
        ]);
    }

    public function test_can_show_single_resource(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $item = ${config.modelName}::factory()->create();

        $response = $this->getJson(route('api.${config.routePrefix}.show', $item));

        $response->assertOk();
        $response->assertJson(['data' => ['id' => $item->id]]);
    }

    public function test_show_returns_404_for_nonexistent(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.${config.routePrefix}.show', 999));
        $response->assertNotFound();
    }
}
`;

  writeFile(filePath, content);
  return filePath;
}

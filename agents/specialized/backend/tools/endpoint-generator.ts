/**
 * Endpoint Generator Utility
 * 
 * Generate API endpoint boilerplate:
 * - Controller methods with Swagger annotations
 * - API Resource classes
 * - Form Request validation
 * - Route definitions
 */

import { writeFile, fileExists } from '../../shared/file-operations';

interface EndpointConfig {
  resourceName: string;
  modelName: string;
  endpoints: ('index' | 'show' | 'store' | 'update' | 'destroy')[];
  relationships: string[];
  validationRules: Record<string, string>;
  apiVersion: string;
}

/**
 * Generate a complete API endpoint set
 */
export function generateEndpoints(basePath: string, config: EndpointConfig): GeneratedFiles {
  const files: GeneratedFiles = {};
  
  // Generate controller
  files.controller = generateController(config);
  
  // Generate resource
  files.resource = generateResource(config);
  
  // Generate request
  files.request = generateRequest(config);
  
  // Generate routes
  files.routes = generateRoutes(config);
  
  return files;
}

function generateController(config: EndpointConfig): string {
  const { resourceName, modelName, endpoints, relationships, apiVersion } = config;
  const withClause = relationships.length > 0 ? `->with(['${relationships.join("', '")}'])` : '';
  
  const methods: string[] = [];
  
  if (endpoints.includes('index')) {
    methods.push(`    /**
     * @OA\\Get(
     *     path="/api/${apiVersion}/${resourceName}",
     *     tags={"${resourceName}"},
     *     summary="List all ${resourceName}",
     *     security={{"sanctum":{}}},
     *     @OA\\Parameter(name="per_page", in="query", @OA\\Schema(type="integer")),
     *     @OA\\Parameter(name="search", in="query", @OA\\Schema(type="string")),
     *     @OA\\Response(response=200, description="Success"),
     *     @OA\\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $query = ${modelName}::query()${withClause ? '\n            ' + withClause : ''};
        
        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        return ${modelName}Resource::collection(
            $query->paginate($request->input('per_page', 15))
        );
    }`);
  }
  
  if (endpoints.includes('show')) {
    methods.push(`    /**
     * @OA\\Get(
     *     path="/api/${apiVersion}/${resourceName}/{id}",
     *     tags={"${resourceName}"},
     *     summary="Show a ${resourceName}",
     *     security={{"sanctum":{}}},
     *     @OA\\Parameter(name="id", in="path", required=true, @OA\\Schema(type="integer")),
     *     @OA\\Response(response=200, description="Success"),
     *     @OA\\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $${modelName.toLowerCase()} = ${modelName}::findOrFail($id)${withClause ? '\n            ' + withClause : ''};
        return new ${modelName}Resource($${modelName.toLowerCase()});
    }`);
  }
  
  return `<?php

namespace App\\Http\\Controllers\\Api\\${apiVersion};

use App\\Http\\Controllers\\Controller;
use App\\Http\\Resources\\${modelName}Resource;
use App\\Models\\${modelName};
use Illuminate\\Http\\Request;

class ${modelName}Controller extends Controller
{
${methods.join('\n\n')}
}
`;
}

function generateResource(config: EndpointConfig): string {
  const { modelName, relationships } = config;
  
  const fields = relationships.map(rel => 
    `            '${rel}' => $this->whenLoaded('${rel}', fn() => $this->${rel}->name),`
  ).join('\n');
  
  return `<?php

namespace App\\Http\\Resources;

use Illuminate\\Http\\Request;
use Illuminate\\Http\\Resources\\Json\\JsonResource;

class ${modelName}Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
${fields}
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
`;
}

function generateRequest(config: EndpointConfig): string {
  const { modelName, validationRules } = config;
  
  const rules = Object.entries(validationRules)
    .map(([field, rule]) => `            '${field}' => '${rule}',`)
    .join('\n');
  
  return `<?php

namespace App\\Http\\Requests\\Api;

use Illuminate\\Foundation\\Http\\FormRequest;

class ${modelName}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
${rules}
        ];
    }
}
`;
}

function generateRoutes(config: EndpointConfig): string {
  const { resourceName, endpoints, apiVersion } = config;
  
  const routeMap: Record<string, string> = {
    index: `Route::get('/', [${config.modelName}Controller::class, 'index']);`,
    show: `Route::get('/{id}', [${config.modelName}Controller::class, 'show']);`,
    store: `Route::post('/', [${config.modelName}Controller::class, 'store']);`,
    update: `Route::put('/{id}', [${config.modelName}Controller::class, 'update']);`,
    destroy: `Route::delete('/{id}', [${config.modelName}Controller::class, 'destroy']);`,
  };
  
  const routes = endpoints.map(e => routeMap[e]).filter(Boolean).join('\n    ');
  
  return `Route::prefix('${apiVersion}/${resourceName}')->middleware('auth:sanctum')->group(function () {
    ${routes}
});`;
}

interface GeneratedFiles {
  controller: string;
  resource: string;
  request: string;
  routes: string;
}

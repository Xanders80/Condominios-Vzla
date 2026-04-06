# Few-Shot Example: API Endpoint

## Input
Create an API endpoint for listing and showing Condominiums with Sanctum auth.

## Output

### Controller: `app/Http/Controllers/Api/V1/CondominiumController.php`
```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CondominiumResource;
use App\Models\Condominium;
use Illuminate\Http\Request;

class CondominiumController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/condominiums",
     *     tags={"Condominiums"},
     *     summary="List all condominiums",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Success",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CondominiumResource"))
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $condominiums = Condominium::with(['state', 'city', 'municipality'])
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->paginate($request->per_page ?? 15);

        return CondominiumResource::collection($condominiums);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/condominiums/{id}",
     *     tags={"Condominiums"},
     *     summary="Show a condominium",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/CondominiumResource")
     *     ),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show($id)
    {
        $condominium = Condominium::with(['state', 'city', 'municipality', 'towers'])->findOrFail($id);
        return new CondominiumResource($condominium);
    }
}
```

### Resource: `app/Http/Resources/CondominiumResource.php`
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CondominiumResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'rif' => $this->rif,
            'address' => $this->address,
            'state' => $this->whenLoaded('state', fn() => $this->state->name),
            'city' => $this->whenLoaded('city', fn() => $this->city->name),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

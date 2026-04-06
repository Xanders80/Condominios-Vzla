# Controller Template

## Input Variables
- `{{module_name}}` - Module name (e.g., "Supplier")
- `{{model_name}}` - Model name (e.g., "Supplier")
- `{{table_name}}` - Database table (e.g., "suppliers")
- `{{route_prefix}}` - Route prefix (e.g., "supplier")
- `{{relationships}}` - Eager loaded relationships (comma-separated)
- `{{view_path}}` - View path (e.g., "backend.supplier")

## Template

```php
<?php

namespace App\Http\Controllers\Backend\{{module_name}};

use App\Http\Controllers\Controller;
use App\Models\{{model_name}};
use App\Http\Requests\{{model_name}}Request;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class {{module_name}}Controller extends Controller
{
    public function index()
    {
        $page = (object) ['code' => '{{route_prefix}}', 'title' => '{{module_name}}'];
        return view('{{view_path}}.index', compact('page'));
    }

    public function data()
    {
        $query = {{model_name}}::select('*');{{#if relationships}}
        $query->with([{{relationships}}]);{{/if}}

        return datatables()->of($query)
            ->addColumn('action', function ($item) {
                return view('{{view_path}}.datatable', compact('item'))->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create()
    {
        $page = (object) ['code' => '{{route_prefix}}', 'title' => 'Create {{module_name}}'];
        return view('{{view_path}}.create', compact('page'));
    }

    public function store({{model_name}}Request $request)
    {
        {{model_name}}::create($request->validated());
        Alert::success('Success', '{{module_name}} created successfully');
        return redirect()->route('{{route_prefix}}.index');
    }

    public function edit($id)
    {
        ${{model_snake}} = {{model_name}}::findOrFail($id);
        $page = (object) ['code' => '{{route_prefix}}', 'title' => 'Edit {{module_name}}'];
        return view('{{view_path}}.edit', compact('{{model_snake}}', 'page'));
    }

    public function update({{model_name}}Request $request, $id)
    {
        ${{model_snake}} = {{model_name}}::findOrFail($id);
        ${{model_snake}}->update($request->validated());
        Alert::success('Success', '{{module_name}} updated successfully');
        return redirect()->route('{{route_prefix}}.index');
    }

    public function delete($id)
    {
        ${{model_snake}} = {{model_name}}::findOrFail($id);
        $page = (object) ['code' => '{{route_prefix}}', 'title' => 'Delete {{module_name}}'];
        return view('{{view_path}}.delete', compact('{{model_snake}}', 'page'));
    }

    public function destroy($id)
    {
        ${{model_snake}} = {{model_name}}::findOrFail($id);
        ${{model_snake}}->delete();
        Alert::success('Success', '{{module_name}} deleted successfully');
        return redirect()->route('{{route_prefix}}.index');
    }
}
```

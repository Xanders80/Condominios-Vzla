# Few-Shot Example: Laravel Controller

## Input
Create a CRUD controller for "Supplier" module with DataTables support.

## Output

### Controller: `app/Http/Controllers/Backend/Supplier/SupplierController.php`
```php
<?php

namespace App\Http\Controllers\Backend\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Http\Requests\SupplierRequest;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class SupplierController extends Controller
{
    public function index()
    {
        $page = (object) ['code' => 'supplier', 'title' => 'Suppliers'];
        return view('backend.supplier.index', compact('page'));
    }

    public function data()
    {
        $suppliers = Supplier::with(['country', 'state', 'city'])->select('*');
        return datatables()->of($suppliers)
            ->addColumn('action', function ($supplier) {
                return view('backend.supplier.datatable', compact('supplier'))->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create()
    {
        $page = (object) ['code' => 'supplier', 'title' => 'Create Supplier'];
        return view('backend.supplier.create', compact('page'));
    }

    public function store(SupplierRequest $request)
    {
        Supplier::create($request->validated());
        Alert::success('Success', 'Supplier created successfully');
        return redirect()->route('supplier.index');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $page = (object) ['code' => 'supplier', 'title' => 'Edit Supplier'];
        return view('backend.supplier.edit', compact('supplier', 'page'));
    }

    public function update(SupplierRequest $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->validated());
        Alert::success('Success', 'Supplier updated successfully');
        return redirect()->route('supplier.index');
    }

    public function delete($id)
    {
        $supplier = Supplier::findOrFail($id);
        $page = (object) ['code' => 'supplier', 'title' => 'Delete Supplier'];
        return view('backend.supplier.delete', compact('supplier', 'page'));
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        Alert::success('Success', 'Supplier deleted successfully');
        return redirect()->route('supplier.index');
    }
}
```

### Routes: Add to `routes/backend.php`
```php
Route::prefix('supplier')->as('supplier')->group(function () {
    Route::get('data', 'Supplier\SupplierController@data');
    Route::get('delete/{id}', 'Supplier\SupplierController@delete');
});
Route::resource('supplier', 'Supplier\SupplierController');
```

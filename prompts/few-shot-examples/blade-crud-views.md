# Few-Shot Example: Blade CRUD Views

## Input
Create Blade views for "Supplier" module following the project pattern.

## Output

### `resources/views/backend/supplier/index.blade.php`
```blade
@extends('backend.main.index')
@section('title', 'Suppliers')
@section('content')
    @include('components.show-header-breadcrumb', ['title' => 'Suppliers'])
    @include('components.body-index', [
        'route' => route('supplier.create'),
        'routeName' => 'Create Supplier',
        'datatable' => true
    ])
@endsection
@section('script')
    @include('backend.supplier.datatable')
@endsection
```

### `resources/views/backend/supplier/create.blade.php`
```blade
@extends('backend.main.index')
@section('title', 'Create Supplier')
@section('content')
    @include('components.show-header-breadcrumb', ['title' => 'Create Supplier'])
    <div class="card">
        <div class="card-body">
            <form action="{{ route('supplier.store') }}" method="POST">
                @csrf
                @include('backend.supplier.form')
                <div class="form-group text-right">
                    <a href="{{ route('supplier.index') }}" class="btn btn-secondary">Cancel</a>
                    @include('components.button-submit', ['label' => 'Save'])
                </div>
            </form>
        </div>
    </div>
@endsection
```

### `resources/views/backend/supplier/datatable.blade.php`
```blade
<script type="application/javascript">
    const backend = '{{ config('master.app.url.backend') }}';
    const page = { code: 'supplier' };
    fetch("{{ url('/js/' . $backend . '/' . $page->code . '/datatable.js') }}", {
        method: 'POST',
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({})
    })
    .then(e => e.text())
    .then(r => {
        Function('"use strict";\n' + r)();
    }).catch(e => console.log(e));
</script>
```

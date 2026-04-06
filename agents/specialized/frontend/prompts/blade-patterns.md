# Frontend Specialist - Blade Patterns

## Common Blade Patterns in This Project

### 1. Index View Pattern
```blade
@extends('backend.main.index')
@section('title', 'Module Name')
@section('content')
    @include('components.show-header-breadcrumb', ['title' => 'Module Name'])
    @include('components.body-index', [
        'route' => route('module.create'),
        'routeName' => 'Create Module',
        'datatable' => true
    ])
@endsection
@section('script')
    @include('backend.module.datatable')
@endsection
```

### 2. Create/Edit Form Pattern
```blade
@extends('backend.main.index')
@section('title', 'Action Module')
@section('content')
    @include('components.show-header-breadcrumb', ['title' => 'Action Module'])
    <div class="card">
        <div class="card-body">
            <form action="{{ $route }}" method="POST">
                @csrf
                @if(isset($item)) @method('PUT') @endif
                
                @include('components.input-text', [
                    'name' => 'name',
                    'label' => 'Name',
                    'value' => old('name', $item->name ?? ''),
                    'required' => true
                ])
                
                <div class="form-group text-right">
                    <a href="{{ route('module.index') }}" class="btn btn-secondary">Cancel</a>
                    @include('components.button-submit', ['label' => 'Save'])
                </div>
            </form>
        </div>
    </div>
@endsection
```

### 3. Delete Confirmation Pattern
```blade
@extends('backend.main.index')
@section('title', 'Delete Module')
@section('content')
    @include('components.show-header-breadcrumb', ['title' => 'Delete Module'])
    @include('components.body-delete', [
        'route' => route('module.destroy', $item),
        'item' => $item,
        'itemName' => $item->name
    ])
@endsection
```

### 4. DataTables AJAX Pattern
```blade
<script type="application/javascript">
    const backend = '{{ config('master.app.url.backend') }}';
    const page = { code: 'module' };
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

### 5. Select2 with Dynamic Data
```blade
@include('components.input-select', [
    'name' => 'state_id',
    'label' => 'State',
    'options' => $states,
    'value' => old('state_id', $item->state_id ?? ''),
    'required' => true,
    'select2' => true
])

@push('scripts')
<script>
$('#state_id').on('change', function() {
    const stateId = $(this).val();
    $.get(`/admin/module/cities/${stateId}`, function(data) {
        // Populate cities dropdown
    });
});
</script>
@endpush
```

### 6. SweetAlert Confirmation
```blade
<script>
function confirmDelete(url, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Delete "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>
```

## CSS Classes from Admins Template
- Cards: `.card`, `.card-body`, `.card-header`
- Forms: `.form-group`, `.form-control`, `.form-control-sm`
- Buttons: `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-danger`, `.btn-sm`
- Tables: `.table`, `.table-striped`, `.table-hover`
- Alerts: `.alert`, `.alert-success`, `.alert-danger`, `.alert-warning`
- Badges: `.badge`, `.badge-success`, `.badge-danger`, `.badge-warning`

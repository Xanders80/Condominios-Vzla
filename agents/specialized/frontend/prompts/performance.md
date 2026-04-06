# Frontend Specialist - Performance Optimization

## Blade Performance

### 1. View Caching
```bash
# Production
php artisan view:cache

# Clear cache
php artisan view:clear
```

### 2. Avoid N+1 in Blade
```blade
{{-- BAD: Causes N+1 queries --}}
@foreach($condominiums as $condo)
    {{ $condo->state->name }}
@endforeach

{{-- GOOD: Eager load in controller --}}
{{-- Controller: Condominium::with('state')->get() --}}
@foreach($condominiums as $condo)
    {{ $condo->state->name }}
@endforeach
```

### 3. Use @once for Repeated Includes
```blade
@once
    @push('scripts')
    <script src="{{ asset('admins/js/module.js') }}"></script>
    @endpush
@endonce
```

### 4. Defer Non-Critical Scripts
```blade
@push('scripts')
<script defer>
    // Non-critical JavaScript
</script>
@endpush
```

## JavaScript Performance

### 1. DataTables Optimization
```javascript
$('#datatable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("module.data") }}',
        type: 'GET',
    },
    deferRender: true,
    pageLength: 25,
});
```

### 2. Debounce Search Inputs
```javascript
let searchTimeout;
$('#search').on('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        // Perform search
    }, 300);
});
```

### 3. Lazy Load Images
```html
<img data-src="{{ $image }}" class="lazyload" alt="Description">
```

## CSS Performance

### 1. Minify Custom CSS
```bash
# Vite handles minification in production
npm run build
```

### 2. Use Existing Admins CSS
- Don't duplicate styles already in `public/admins/css/`
- Extend with `public/admins/css/custom.css`
- Use Bootstrap utility classes when possible

## Asset Loading

### 1. Vite Asset Management
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

### 2. Conditional Script Loading
```blade
@section('script')
    {{-- Only load on this page --}}
    <script src="{{ asset('admins/js/specific-feature.js') }}"></script>
@endsection
```

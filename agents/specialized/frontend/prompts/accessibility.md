# Accessibility Guidelines - Blade Templates

## WCAG 2.1 Level AA Compliance

### 1. Form Accessibility
```blade
{{-- GOOD: Labels associated with inputs --}}
<label for="email">Email</label>
<input type="email" id="email" name="email" required>

{{-- BAD: Missing label association --}}
<label>Email</label>
<input type="email" name="email">

{{-- GOOD: Required fields indicated --}}
<label for="name">Name <span class="sr-only">(required)</span></label>
<input type="text" id="name" name="name" required aria-required="true">
```

### 2. Color Contrast
- Normal text: minimum 4.5:1 contrast ratio
- Large text (18px+ or 14px bold): minimum 3:1
- UI components and graphical objects: minimum 3:1

```blade
{{-- Use Bootstrap text color classes for sufficient contrast --}}
<span class="text-danger">Error message</span>
<span class="text-success">Success message</span>
<span class="text-warning">Warning message</span>
```

### 3. Keyboard Navigation
- All interactive elements must be keyboard accessible
- Visible focus indicators on all elements
- Logical tab order

```blade
{{-- GOOD: Focus styles --}}
<a href="#" class="btn btn-primary" tabindex="0">Link Button</a>

{{-- Add custom focus styles in custom.css --}}
```

### 4. Screen Reader Support
```blade
{{-- Use aria-label for icon-only buttons --}}
<button type="button" aria-label="Delete item">
    <i class="fa fa-trash"></i>
</button>

{{-- Use aria-describedby for help text --}}
<input type="password" id="password" aria-describedby="password-help">
<small id="password-help" class="form-text text-muted">
    Must be at least 8 characters
</small>

{{-- Use aria-live for dynamic content --}}
<div aria-live="polite" id="notification-area">
    @if(session('message'))
        {{ session('message') }}
    @endif
</div>
```

### 5. Data Tables Accessibility
```blade
<table class="table table-striped" role="table">
    <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        {{-- DataTables handles this automatically --}}
    </tbody>
</table>
```

### 6. Skip Navigation
```blade
{{-- Add to main layout --}}
<a href="#main-content" class="sr-only sr-only-focusable">
    Skip to main content
</a>
<main id="main-content">
    @yield('content')
</main>
```

### 7. Image Accessibility
```blade
{{-- Decorative images --}}
<img src="decoration.png" alt="" role="presentation">

{{-- Informative images --}}
<img src="condominium.jpg" alt="Building exterior of Torre Norte condominium">

{{-- Complex images (charts, diagrams) --}}
<figure>
    <img src="payment-chart.png" alt="Monthly payment summary chart">
    <figcaption>Monthly payments: 75% collected, 25% pending</figcaption>
</figure>
```

### 8. Error Messages
```blade
{{-- Associate errors with inputs using aria-invalid --}}
<input type="text" name="rif" 
    class="form-control @error('rif') is-invalid @enderror"
    @error('rif') aria-invalid="true" aria-describedby="rif-error" @enderror>
@error('rif')
    <span id="rif-error" class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
```

### 9. Responsive Text Sizing
- Support browser text zoom up to 200%
- Use relative units (rem, em) instead of px for text
- Test at 320px viewport width minimum

### 10. Motion and Animation
- Respect `prefers-reduced-motion` media query
- Provide alternatives for animated content
- No auto-playing content that cannot be paused

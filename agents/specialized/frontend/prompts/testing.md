# Frontend Testing Guide - Blade + jQuery

## Testing Strategy for Blade Templates

### 1. Feature Tests for Views
```php
public function test_index_view_shows_correct_data(): void
{
    $user = User::factory()->create();
    $condominiums = Condominium::factory()->count(3)->create();

    $response = $this->actingAs($user)
        ->get(route('condominiums.index'));

    $response->assertOk();
    $response->assertSee('Condominiums');
    $response->assertSee($condominiums->first()->name);
    $response->assertViewHas('page');
}
```

### 2. Form Rendering Tests
```php
public function test_create_form_has_all_required_fields(): void
{
    $response = $this->actingAs(User::factory()->create())
        ->get(route('condominiums.create'));

    $response->assertOk();
    $response->assertSee('name="name"');
    $response->assertSee('name="rif"');
    $response->assertSee('@csrf');
    $response->assertSee('method="POST"');
}
```

### 3. Component Tests
```php
public function test_input_text_component_renders_correctly(): void
{
    $view = $this->blade(
        '@include("components.input-text", ["name" => "email", "label" => "Email", "required" => true])'
    );

    $view->assertSee('type="text"');
    $view->assertSee('name="email"');
    $view->assertSee('required');
    $view->assertSee('Email');
}
```

### 4. JavaScript Testing
```javascript
// Test DataTables initialization
describe('DataTables', function() {
    it('initializes with correct configuration', function() {
        const table = $('#datatable').DataTable();
        expect(table.settings().init().processing).toBe(true);
        expect(table.settings().init().serverSide).toBe(true);
    });
});

// Test form validation
describe('Form Validation', function() {
    it('prevents submission with empty required fields', function() {
        const form = $('#create-form');
        form.find('[required]').val('');
        form.submit();
        expect(form[0].checkValidity()).toBe(false);
    });
});
```

### 5. Accessibility Testing
```php
public function test_form_has_accessible_labels(): void
{
    $response = $this->actingAs(User::factory()->create())
        ->get(route('condominiums.create'));

    $dom = $response->dom();
    $inputs = $dom->getElementsByTagName('input');
    
    foreach ($inputs as $input) {
        $id = $input->getAttribute('id');
        if ($id) {
            $label = $dom->getElementById($id);
            // Verify label exists or aria-label is present
        }
    }
}
```

### 6. Responsive Testing
- Test at 320px, 768px, 1024px, 1440px
- Verify tables collapse properly on mobile
- Verify forms stack vertically on small screens
- Verify navigation menu collapses to hamburger

### 7. Cross-Browser Testing
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

### 8. Performance Testing
```javascript
// Measure page load time
console.time('page-load');
window.addEventListener('load', () => {
    console.timeEnd('page-load');
});

// Measure DataTables render time
$('#datatable').on('draw.dt', function() {
    console.timeEnd('datatable-render');
});
console.time('datatable-render');
```

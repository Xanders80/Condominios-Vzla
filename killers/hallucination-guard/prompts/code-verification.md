# Hallucination Guard Code Verification

## Syntax Validation Checklist

### PHP Code
- [ ] Valid PHP syntax (passes `php -l`)
- [ ] Correct namespace declaration
- [ ] All use statements reference real classes
- [ ] Method signatures match parent class
- [ ] Return types are valid PHP types
- [ ] No undefined variables
- [ ] No undefined constants

### Blade Templates
- [ ] All directives properly closed (@if/@endif, @foreach/@endforeach)
- [ ] Variables exist in the controller's view data
- [ ] Route names match definitions in route files
- [ ] Component names match files in resources/views/components/
- [ ] No raw PHP blocks without @php/@endphp

### Migrations
- [ ] Column types are valid Blueprint methods
- [ ] Foreign key references existing tables
- [ ] Table names follow convention (plural, snake_case)
- [ ] Timestamps and softDeletes when needed

### Tests
- [ ] Assert methods exist in PHPUnit
- [ ] Route names match definitions
- [ ] Factory attributes match model fillable
- [ ] Database assertions use correct table names

## Reference Check Process

1. **Before suggesting a method**: Search the codebase for similar patterns
2. **Before using a package function**: Verify in composer.json that package exists
3. **Before creating a route**: Check existing route files for naming conventions
4. **Before adding a relationship**: Verify the related model exists
5. **Before using a Blade component**: Verify the component file exists

## Self-Evaluation Questions

After generating code, ask:
1. Does this code follow the project's established patterns?
2. Are all referenced classes, methods, and routes real?
3. Would this code pass `php -l` and Laravel Pint?
4. Does it use the correct Laravel 12 syntax?
5. Is it consistent with the Blade + jQuery frontend approach?

If any answer is "no" or "unsure", flag for review.

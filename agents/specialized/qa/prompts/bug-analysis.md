# Bug Analysis Guide - QA

## Bug Analysis Process for Condominios-Vzla

### 1. Bug Classification
```yaml
severity_levels:
  critical:
    definition: "System unusable or data loss"
    examples:
      - "Cannot login"
      - "Payment calculation wrong"
      - "Data corruption"
    response_time: "Immediate"

  high:
    definition: "Major feature broken, no workaround"
    examples:
      - "Cannot create receipts"
      - "Common area booking fails"
      - "API endpoint returns 500"
    response_time: "Within 4 hours"

  medium:
    definition: "Feature partially broken, workaround exists"
    examples:
      - "DataTable sorting broken"
      - "Form validation missing field"
      - "PDF receipt formatting issue"
    response_time: "Within 24 hours"

  low:
    definition: "Minor issue, cosmetic, or edge case"
    examples:
      - "Typo in label"
      - "Color inconsistency"
      - "Rare edge case"
    response_time: "Next sprint"
```

### 2. Root Cause Analysis Template
```markdown
## Bug Report: [Title]

### Description
[What is happening]

### Expected Behavior
[What should happen]

### Actual Behavior
[What is actually happening]

### Steps to Reproduce
1. [Step 1]
2. [Step 2]
3. [Step 3]

### Environment
- Browser: [Chrome/Firefox/Safari]
- User Role: [Admin/Resident/Coowner]
- Module: [Module name]

### Root Cause
[Technical explanation of why the bug occurs]

### Impact
- Affected users: [Who is affected]
- Affected data: [What data is impacted]
- Workaround: [If any]

### Fix Proposal
[How to fix it]
```

### 3. Common Bug Patterns in This Project

#### N+1 Query Issues
```php
// Symptom: Slow page load, high query count in Telescope
// Fix: Add eager loading
$condominiums = Condominium::with('state', 'city')->get();
```

#### Mass Assignment Exceptions
```php
// Symptom: "Add [field] to fillable property" error
// Fix: Add field to model's $fillable array
protected $fillable = ['name', 'email', 'new_field'];
```

#### Route Not Found
```php
// Symptom: "Route [name] not defined"
// Fix: Check route name in routes/backend.php
// Common mistake: route('module.store') vs route('modules.store')
```

#### CSRF Token Mismatch
```php
// Symptom: 419 Page Expired on form submit
// Fix: Add @csrf to form
<form method="POST">
    @csrf
    ...
</form>
```

#### Validation Failures
```php
// Symptom: Form won't submit, no visible error
// Fix: Check Form Request rules and error display in view
@error('field')
    <span class="invalid-feedback">{{ $message }}</span>
@enderror
```

### 4. Debugging Tools

#### Laravel Telescope
```bash
# Access: /telescope (root users only)
# Check:
# - Queries tab: N+1, slow queries
# - Exceptions tab: Stack traces
# - Requests tab: Input/output
# - Logs tab: Application logs
```

#### Query Logging
```php
// Enable query logging
DB::enableQueryLog();

// After operation
dd(DB::getQueryLog());
```

#### Error Logs
```bash
# View recent errors
tail -100 storage/logs/laravel.log

# Search for specific error
grep "Error" storage/logs/laravel.log | tail -20
```

### 5. Regression Prevention
```yaml
after_every_bug_fix:
  - "Write a test that reproduces the bug"
  - "Verify the test fails before the fix"
  - "Verify the test passes after the fix"
  - "Run full test suite to check for regressions"
  - "Document the bug pattern in memory"
```

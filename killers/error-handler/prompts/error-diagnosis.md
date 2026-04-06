# Error Handler Prompts

## Error Diagnosis Prompt

When an error occurs, follow this diagnostic process:

### 1. Identify the Error Type
- **Build Failure**: `composer install`, `npm run build`, `php artisan` commands
- **Test Failure**: PHPUnit test failures
- **Runtime Exception**: Laravel exceptions during execution
- **Syntax Error**: PHP parse errors, Blade syntax errors

### 2. Analyze the Stack Trace
```
Look for:
1. The FIRST line in app/ code (not vendor/)
2. The file and line number
3. The method being executed
4. The input that caused the error
```

### 3. Classify the Error
| Severity | Action |
|----------|--------|
| Critical | System broken, immediate fix required |
| High | Feature broken, fix before continuing |
| Medium | Edge case failure, fix in current session |
| Low | Warning or notice, fix if time permits |

### 4. Fix Strategy
1. **Understand** the root cause (don't just fix the symptom)
2. **Reproduce** the error locally
3. **Fix** with minimal changes
4. **Test** the fix (run relevant tests)
5. **Verify** no regressions

### 5. Common Laravel Errors and Fixes

#### N+1 Query Detection
```
Error: Query count is too high
Fix: Add with() to the query for the relationship
```

#### Mass Assignment Exception
```
Error: Add [field] to fillable property
Fix: Add the field to the model's $fillable array
```

#### Route Not Found
```
Error: Route [name] not defined
Fix: Check route name in routes/backend.php or mvc-route.php
```

#### View Not Found
```
Error: View [name] not found
Fix: Check view path matches resources/views/ structure
```

#### Class Not Found
```
Error: Class not found
Fix: Check namespace and run composer dump-autoload
```

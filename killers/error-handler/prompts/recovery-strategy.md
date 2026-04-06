# Error Handler Recovery Strategy

## Auto-Fix Procedures

### PHP Syntax Errors
1. Run `php -l <file>` to confirm syntax error
2. Parse the error message for line number
3. Read the file around the error line
4. Fix the syntax issue
5. Re-run `php -l` to verify

### Blade Syntax Errors
1. Check for unclosed `@if`, `@foreach`, `@section`
2. Verify matching `@endif`, `@endforeach`, `@endsection`
3. Check for missing `@endphp` after raw PHP blocks
4. Verify variable names in `{{ }}` expressions

### Migration Errors
1. If "table already exists": check if migration was already run
2. If "foreign key constraint fails": verify referenced table exists
3. If "column not found": check column name matches migration
4. Run `php artisan migrate:status` to check migration state

### Test Failures
1. Run the single failing test: `php artisan test --filter=test_name`
2. Check if it's a database state issue: add `RefreshDatabase`
3. Check if factory data is valid
4. Check if route names match
5. Verify authentication setup

### Composer Errors
1. If "package not found": check version constraint
2. If "class not found": run `composer dump-autoload`
3. If "memory exhausted": run with `COMPOSER_MEMORY_LIMIT=-1`
4. If "conflict": check version compatibility with Laravel 12

## Rollback Procedures

### Code Rollback
```bash
# Revert last commit
git revert HEAD

# Reset to last known good state
git checkout <commit-hash> -- <file>
```

### Database Rollback
```bash
# Rollback last migration batch
php artisan migrate:rollback

# Rollback specific migration
php artisan migrate:rollback --step=1
```

### When to Rollback
- Fix attempt makes things worse
- Fix introduces new errors
- Fix breaks existing functionality
- More than 3 fix attempts have failed

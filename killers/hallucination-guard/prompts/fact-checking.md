# Hallucination Guard Prompts

## Fact Checking Prompt

When generating code or making technical claims, verify against these sources:

### 1. Laravel Documentation
- Check Laravel 12 docs at https://laravel.com/docs/12.x
- Verify method signatures and parameters
- Confirm deprecation status of features

### 2. Package Documentation
- arwp/mvc: https://github.com/arwahyu01/mvc-builder
- yajra/datatables: https://yajrabox.com/docs/laravel-datatables
- spatie/laravel-html: https://github.com/spatie/laravel-html
- realrashid/sweet-alert: https://github.com/realrashid/sweet-alert

### 3. PHP Documentation
- Check PHP 8.5 features at https://www.php.net/manual/
- Verify function signatures
- Confirm deprecated functions

### 4. Project Codebase
- Search existing code for patterns before suggesting new ones
- Check if a method already exists
- Verify route names match actual definitions
- Confirm model relationships exist

### 5. Confidence Scoring
Rate your confidence in each claim:
- **0.9-1.0**: Verified against documentation or existing code
- **0.7-0.89**: Based on strong pattern matching in codebase
- **0.5-0.69**: Based on general knowledge, should be verified
- **< 0.5**: Uncertain, should NOT be used without verification

### 6. Common Hallucinations to Avoid
- Inventing Laravel methods that don't exist
- Using wrong PHP syntax for the version
- Suggesting packages not in composer.json
- Creating routes with wrong naming conventions
- Using React/Vue patterns in a Blade project
- Inventing database columns that don't exist in migrations

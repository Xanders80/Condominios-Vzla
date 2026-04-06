# Task Prompt: Write Tests

## Context
You are writing tests for Condominios-Vzla, a Laravel 12 condominium management system.

## Test Types
1. **Unit Tests**: Model methods, services, helpers, calculations
2. **Feature Tests**: HTTP endpoints, CRUD operations, auth flows
3. **API Tests**: RESTful endpoints with Sanctum auth

## Conventions
- Base class: `Tests\TestCase`
- Use factories for test data
- Database refreshes after each test
- Descriptive method names in snake_case
- Assert both positive and negative cases

## Minimum Coverage per CRUD Module
- Index returns 200
- Create shows form
- Store validates input (valid and invalid data)
- Store saves to database correctly
- Edit shows form with existing data
- Update modifies database
- Delete removes from database
- Unauthorized access is redirected

## Output Format
- Complete test class file
- Factory file if not exists
- List of scenarios covered

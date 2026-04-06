# Task Prompt: Document Code

## Context
You are documenting code in Condominios-Vzla, a Laravel 12 condominium management system.

## Documentation Types
1. **PHPDoc**: Methods, classes, properties with types
2. **Swagger/OpenAPI**: API endpoints with request/response schemas
3. **README**: Module overview, setup instructions, usage examples
4. **ADR**: Architecture Decision Records for significant changes

## PHPDoc Conventions
```php
/**
 * Brief description of what the method does.
 *
 * @param Type $param Description
 * @return Type Description
 * @throws ExceptionType When condition
 */
```

## Swagger Conventions
- Use `@OA\Get`, `@OA\Post`, etc. annotations
- Include security requirements for protected endpoints
- Document all request body fields and response properties
- Use existing schemas as references

## Output Format
- Inline documentation in code files
- Separate documentation files for modules
- Updated API documentation

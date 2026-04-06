# Task Prompt: Generate Code

## Context
You are generating code for Condominios-Vzla, a Laravel 12 condominium management system.

## Instructions
1. Analyze the requirements and identify the module type (CRUD, API, Service, etc.)
2. Follow the project conventions defined in `.opencode/agents.md`
3. Use existing patterns from similar modules as reference
4. Include validation, error handling, and documentation
5. Generate tests alongside the code

## Output Format
- List all files to be created/modified
- Provide complete file contents for each
- Include migration if database changes are needed
- Include routes if new endpoints are added
- Include tests for the new functionality

## Quality Checklist
- [ ] Follows PSR-12 (Laravel Pint)
- [ ] Type hints on all methods
- [ ] Eloquent relationships with return types
- [ ] Form Request for validation
- [ ] CSRF tokens on forms (if Blade)
- [ ] DataTables AJAX pattern (if index view)
- [ ] Swagger annotations (if API)
- [ ] Tests written and passing

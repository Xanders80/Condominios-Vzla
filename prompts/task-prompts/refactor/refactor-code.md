# Task Prompt: Refactor Code

## Context
You are refactoring code in Condominios-Vzla, a Laravel 12 condominium management system.

## Instructions
1. Identify the code smell or anti-pattern
2. Propose the refactoring approach
3. Ensure behavior is preserved
4. Apply the refactoring incrementally
5. Run tests after each change

## Refactoring Patterns for This Project
- Extract Service from fat controllers
- Convert raw queries to Eloquent with eager loading
- Extract Blade components from duplicated view code
- Convert array responses to API Resources
- Add Form Request validation from inline validation
- Extract query scopes from repeated conditions

## Output Format
- Before/after comparison
- List of files changed
- Migration if schema changes needed
- Tests to verify behavior unchanged

## Quality Checklist
- [ ] All existing tests pass
- [ ] Complexity reduced
- [ ] No new dependencies without justification
- [ ] Code follows project conventions
- [ ] Performance not degraded

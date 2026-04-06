# Task Prompt: Analyze Code

## Context
You are analyzing code in Condominios-Vzla, a Laravel 12 condominium management system.

## Analysis Types
1. **Complexity**: Cyclomatic and cognitive complexity
2. **Performance**: N+1 queries, missing indexes, memory usage
3. **Security**: Input validation, auth gaps, SQL injection risks
4. **Maintainability**: Code duplication, naming, organization
5. **Dependencies**: Unused imports, circular dependencies

## Output Format
- Severity-rated findings (Critical, High, Medium, Low, Info)
- Specific file and line references
- Concrete improvement suggestions
- Estimated effort for each fix

## Priority Order
1. Security vulnerabilities
2. Data integrity risks
3. Performance bottlenecks
4. Code quality issues
5. Style inconsistencies

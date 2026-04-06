# Agentes del Proyecto - Condominios-Vzla

Este documento describe todos los agentes configurados para OpenCode en el proyecto Condominios-Vzla.

---

## 📋 Tabla de Agentes

| ID | Rol | Tipo | Prioridad | Estado |
|----|-----|------|-----------|--------|
| `software-architect` | Arquitecto de Software | Core | Alta | ✅ Activo |
| `product-manager` | Product Manager Técnico | Core | Alta | ✅ Activo |
| `tech-lead` | Líder Técnico | Core | Alta | ✅ Activo |
| `frontend-specialist` | Especialista Frontend (Blade) | Specialized | Alta | ✅ Activo |
| `backend-specialist` | Especialista Backend (Laravel) | Specialized | Alta | ✅ Activo |
| `devops-specialist` | Especialista DevOps | Specialized | Media | ✅ Activo |
| `security-specialist` | Especialista Seguridad | Specialized | Alta | ✅ Activo |
| `qa-specialist` | Especialista QA | Specialized | Alta | ✅ Activo |
| `master-orchestrator` | Orquestador Principal | Orchestrator | Crítica | ✅ Activo |
| `context-manager` | Gestor de Contexto | Orchestrator | Alta | ✅ Activo |

---

## 🏛️ Core Agents

### software-architect
- **Responsabilidad**: Diseño de arquitectura Laravel, patrones, ADRs
- **Delega a**: frontend-specialist, backend-specialist, devops-specialist
- **Escala a**: tech-lead
- **Modelo preferido**: claude-opus-4
- **Temperatura**: 0.2

### product-manager
- **Responsabilidad**: PRDs, user stories, criterios de aceptación, priorización
- **Workflows**: prd_creation, story_breakdown, acceptance_criteria_definition

### tech-lead
- **Responsabilidad**: Code reviews complejos, mentoría, decisiones técnicas
- **Approval gates**: architecture_changes, breaking_changes, security_implementations
- **Stack**: PHP/Laravel, JavaScript/jQuery, Blade

---

## 🎨 Specialized Agents

### frontend-specialist
- **Stack**: Blade Templates, jQuery 3.7, Bootstrap 5.3, Select2, DataTables
- **Responsabilidad**: Vistas Blade, componentes reutilizables, CSS, JavaScript CRUD
- **NOTA**: Este proyecto NO usa React/Vue/Angular. Usa Blade + jQuery.
- **Subagents**: blade-component-generator, test-generator

### backend-specialist
- **Stack**: Laravel 12, PHP 8.5, Eloquent, MySQL, Sanctum, DataTables
- **Responsabilidad**: Controllers, Models, Migrations, Requests, API endpoints, Services
- **Patrones del proyecto**: arwp/mvc CRUD generator, resource controllers, AJAX DataTables
- **Subagents**: laravel-controller-generator, database-migration-generator, api-endpoint-generator

### devops-specialist
- **Stack**: Laravel Sail, Vite, GitHub Actions, MySQL
- **Responsabilidad**: CI/CD, deployment, Docker, monitoring (Telescope)

### security-specialist
- **Responsabilidad**: Auditoría de seguridad, sanitización, CSRF, XSS, SQL injection
- **Enfoque**: Sanctum auth, roles/permisos, validación de inputs, protección de rutas

### qa-specialist
- **Stack**: PHPUnit
- **Responsabilidad**: Tests unitarios, feature tests, cobertura, validación funcional
- **Cobertura actual**: ~28 tests (auth, receipts, interests, helpers, dwellers)

---

## 🎼 Orchestrators

### master-orchestrator
- **Motor**: State machine
- **Patrones**: sequential, parallel, conditional, iterative
- **Estrategia de delegación**:
  - Tarea simple → specialist directo
  - Tarea compleja → planificar luego delegar
  - Tarea crítica → architect review luego delegar
  - Emergencia → killers primero luego evaluar

### context-manager
- **Responsabilidad**: Gestión de ventana de contexto, pruning, prioridad
- **Límite**: 50 items de contexto máximo

---

## 🔪 Killers Activos

| Killer | Función | Trigger |
|--------|---------|---------|
| `error-handler` | Diagnóstico y recuperación de errores | build_failure, test_failure, runtime_exception, syntax_error |
| `hallucination-guard` | Verificación de código generado | confidence < 0.8, syntax errors, security risks |
| `loop-detector` | Detección de bucles infinitos | max 10 iteraciones, 95% similitud, 300s sin progreso |
| `resource-guard` | Control de costos y tokens | Por definir |
| `safety-guard` | Prevención de operaciones peligrosas | Por definir |
| `timeout-enforcer` | Control de timeouts | Por definir |

---

## 📐 Convenciones del Proyecto

### Backend (Laravel)
- Controllers en `app/Http/Controllers/Backend/{Module}/`
- Models en `app/Models/` con fillable y relaciones
- Requests de validación en `app/Http/Requests/`
- Migrations en `database/migrations/`
- Seeders en `database/seeders/`
- Services en `app/Services/`

### Frontend (Blade)
- Vistas en `resources/views/backend/{module}/`
- Patrón CRUD: `index, create, edit, show, delete, datatable.blade.php`
- Componentes en `resources/views/components/`
- Layouts en `resources/views/backend/main/`
- JavaScript DataTables vía POST fetch dinámico

### API
- Endpoints en `routes/api.php` con prefijo `v1/auth`
- Autenticación con Laravel Sanctum
- Controllers en `app/Http/Controllers/Api/V1/`

### Testing
- Feature tests en `tests/Feature/`
- Unit tests en `tests/Unit/`
- Base test: `tests/TestCase.php`
- Trait: `tests/CreatesApplication.php`

### Code Style
- Laravel Pint para formatação
- PSR-12
- Nombres: snake_case para DB, PascalCase para clases, camelCase para métodos

---

## 🔄 Workflow: Feature Development

```
1. Requirements (product-manager) → prd.md
2. Architecture (architect) → tech-spec.md
3. Planning (master-orchestrator) → implementation-plan.json
4. Implementation (parallel):
   - Frontend: frontend-specialist + blade-component-generator
   - Backend: backend-specialist + laravel-controller-generator + migration-generator
5. Integration (master-orchestrator) → merge_and_validate
6. Review (tech-lead) → code_quality, test_coverage, security_scan
7. QA (qa-specialist) → functional_tests, e2e_tests, performance_tests
8. Deployment (devops-specialist) → staging → smoke → production
```

Killers activos durante todo el workflow: error-handler, loop-detector, hallucination-guard, resource-guard

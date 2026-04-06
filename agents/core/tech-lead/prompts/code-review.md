# Code Review Prompt - Tech Lead

## Contexto
Estás realizando un code review de un pull request en Condominios-Vzla.

## Proceso de Review

### Paso 1: Revisión Automática (Lint y Tests)
- [ ] Laravel Pint pasa sin errores
- [ ] Todos los tests pasan
- [ ] No hay tests skipped
- [ ] Cobertura de tests adecuada

### Paso 2: Revisión de Arquitectura
- [ ] Separación de responsabilidades (MVC)
- [ ] Lógica de negocio en Services, no en Controllers
- [ ] Validación en Form Requests, no en Controllers
- [ ] Modelos con fillable, casts y relaciones correctas

### Paso 3: Revisión de Performance
- [ ] No hay queries N+1 (eager loading con `with()`)
- [ ] Índices en columnas de búsqueda/filtro
- [ ] No hay `DB::raw()` sin justificación
- [ ] Paginación en listados grandes

### Paso 4: Revisión de Seguridad
- [ ] CSRF tokens en formularios
- [ ] Middleware auth en rutas protegidas
- [ ] Validación de inputs completa
- [ ] No hay SQL injection posible
- [ ] No hay XSS (Blade escaping correcto)
- [ ] No hay secretos hardcodeados

### Paso 5: Revisión de Código
- [ ] Nombres descriptivos de variables y métodos
- [ ] Type hints en todos los métodos
- [ ] PHPDoc en métodos complejos
- [ ] No hay código duplicado
- [ ] Manejo de errores apropiado

### Paso 6: Revisión de Vistas
- [ ] Consistencia con template Admins
- [ ] Responsive design
- [ ] Componentes reutilizables donde aplica
- [ ] DataTables AJAX funcionando

## Formato del Review

```markdown
## Code Review: [PR Title]

### ✅ Aprobado / ⚠️ Cambios Requeridos / ❌ Rechazado

### Hallazgos

#### 🔴 Crítico (debe corregir antes de merge)
1. [Archivo:Línea] - Descripción del problema
   **Sugerencia:** Cómo corregirlo

#### 🟡 Importante (debería corregir)
1. [Archivo:Línea] - Descripción

#### 🟢 Sugerencia (opcional pero recomendado)
1. [Archivo:Línea] - Descripción

### Resumen
- Líneas revisadas: X
- Hallazgos críticos: X
- Hallazgos importantes: X
- Sugerencias: X
```

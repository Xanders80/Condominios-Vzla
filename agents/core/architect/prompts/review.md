# Architecture Review Prompt - Arquitecto de Software

## Contexto
Estás revisando una implementación técnica en Condominios-Vzla para verificar que sigue los estándares de arquitectura.

## Checklist de Revisión

### Estructura
- [ ] Controllers en `app/Http/Controllers/Backend/{Module}/`
- [ ] Models en `app/Models/`
- [ ] Requests en `app/Http/Requests/`
- [ ] Services en `app/Services/` (si aplica)
- [ ] Views en `resources/views/backend/{module}/`

### Modelos
- [ ] `$fillable` explícito (nunca `$guarded = []`)
- [ ] `$casts` para tipos de datos
- [ ] Relaciones con tipo de retorno (`HasMany`, `BelongsTo`)
- [ ] Query scopes para consultas reutilizables
- [ ] Eager loading en queries con relaciones

### Controllers
- [ ] Método `data()` para DataTables AJAX
- [ ] Lógica de negocio delegada a Services
- [ ] Form Request para validación
- [ ] SweetAlert para notificaciones
- [ ] Redirects apropiados después de acciones

### Vistas
- [ ] Patrón CRUD completo (index, create, edit, delete, datatable)
- [ ] `@csrf` en todos los formularios
- [ ] Componentes reutilizables donde aplica
- [ ] Consistencia con template Admins

### Rutas
- [ ] Route::prefix + Route::resource pattern
- [ ] Middleware auth y userRoles
- [ ] Nombres de rutas consistentes

### API (si aplica)
- [ ] API Resources para respuestas
- [ ] Swagger annotations
- [ ] Middleware auth:sanctum
- [ ] Validación con Form Request

### Base de Datos
- [ ] Migraciones con foreign keys
- [ ] Índices en columnas de búsqueda frecuente
- [ ] Soft deletes donde aplica
- [ ] Columnas de timestamps

## Criterios de Aprobación
- **Aprobar**: Cumple todos los criterios obligatorios
- **Cambios menores**: 1-3 criterios no obligatorios faltantes
- **Cambios mayores**: Criterios obligatorios faltantes o problemas de arquitectura
- **Rechazar**: Violaciones graves de arquitectura o seguridad

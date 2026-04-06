# Planning Prompt - Arquitecto de Software

## Contexto
Estás diseñando la arquitectura técnica para una nueva funcionalidad en Condominios-Vzla (Laravel 12, PHP 8.5).

## Entrada
- PRD del Product Manager (prd.md)
- Requisitos técnicos y de negocio
- Restricciones del sistema existente

## Proceso de Planificación

### 1. Análisis de Impacto
- ¿Qué módulos existentes se ven afectados?
- ¿Se necesitan nuevas tablas en la base de datos?
- ¿Se requieren nuevos endpoints API?
- ¿Hay cambios en el sistema de permisos?

### 2. Diseño de Base de Datos
- Definir nuevas tablas con columnas, tipos, índices
- Definir relaciones con tablas existentes
- Considerar soft deletes, timestamps, foreign keys
- Evaluar necesidad de tablas pivot

### 3. Diseño de Capas
- **Models**: Eloquent con fillable, casts, relaciones, scopes
- **Controllers**: Resource controllers con método data() para DataTables
- **Requests**: Form Request para validación
- **Services**: Lógica de negocio compleja fuera de controllers
- **Resources**: API Resources para respuestas JSON
- **Views**: Blade templates siguiendo patrón CRUD

### 4. Diseño de Rutas
- Web routes en routes/backend.php o routes/mvc-route.php
- API routes en routes/api.php con prefijo v1
- Middleware apropiado (auth, userRoles, auth:sanctum)

### 5. Plan de Testing
- Unit tests para lógica de negocio
- Feature tests para CRUD operations
- API tests para endpoints RESTful

## Salida Esperada
Documento `tech-spec.md` con:
- Diagrama de base de datos (tablas y relaciones)
- Lista de archivos a crear/modificar
- Diagrama de flujo de la funcionalidad
- Plan de testing
- Riesgos y mitigaciones
- Estimación de esfuerzo

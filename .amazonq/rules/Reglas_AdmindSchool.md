# 📄 Guía de Directivas y Contexto del Amazonq

---

## 💻 Directivas de Operación (No Negociables)

Esta sección cubre las prácticas fundamentales para asegurar la calidad, la seguridad y la mantenibilidad del código.

-   **1. Seguridad por Defecto**
    -   Cumplimiento estricto con **OWASP Top 10**.
    -   Prohibición de _hardcodeo_ de credenciales (usar gestión de secretos).
    -   Sanitización de todas las entradas de usuario (contra Inyecciones SQL/XSS).
    -   Implementación de encriptación para datos confidenciales.
-   **2. Código Limpio y Mantenible**
    -   Adherencia a principios **SOLID, DRY y KISS**.
    -   Nomenclatura semántica y autodocumentada.
    -   Aplicación de **YAGNI** y Separación de _Concerns_.
-   **3. Pensamiento en Cadena (CoT)**
    -   Realización de análisis y planificación lógica previa.
    -   Desglose de problemas complejos en pasos manejables.
    -   Documentación del razonamiento antes de la implementación del código.
-   **4. Manejo de Errores y Resiliencia**
    -   Uso de `try/catch` en operaciones críticas.
    -   Implementación de validación de entradas y estados de carga/error.
    -   Integración de prácticas de observabilidad (_logging, monitoreo_).

## 🚀 Directivas de Rendimiento y Escalabilidad

Estas pautas aseguran que la solución sea eficiente y capaz de crecer con la demanda.

-   Optimización de consultas a base de datos (índices, evitar N+1).
-   Implementación de estrategias de caché multi-nivel.
-   Diseño para **escalabilidad horizontal**.
-   Uso de procesamiento **asíncrono** para tareas intensivas.

## 🤝 Directivas de Proyecto y Colaboración

Reglas esenciales para el desarrollo en equipo y la gestión de la calidad del código.

-   Control de versiones con **Git** y **Conventional Commits**.
-   Uso de _linters_, formateadores y análisis estático.
-   Definición clara de estrategia de pruebas (unitarias, integración, E2E).
-   Documentación continua y automatizada.

---

## Resumen de Arquitectura

Este es un sistema de gestión de condominios en **Laravel 12** construido con un **generador CRUD MVC personalizado** (paquete `arwp/mvc`). El sistema sigue una **arquitectura multi-tenant** con control de acceso basado en roles y usa **Bootstrap 5** con la plantilla "admins" para la UI.

### Componentes Clave

-   **Controladores Backend**: `app/Http/Controllers/Backend/` - Toda la funcionalidad administrativa
-   **Modelos**: Entidades de dominio como `Condominiums`, `Dweller`, `Unit`, `Payments` con sistema de direcciones venezolano
-   **Helper Personalizado**: `app/Support/Helper.php` - Utilidades centrales para menús, manejo de archivos, notificaciones
-   **Generador MVC**: Creación automatizada de CRUD vía `php artisan make:mvc [nombre]`

## Flujos de Desarrollo

### Generación de CRUD

```bash
# Generar módulo MVC completo
php artisan make:mvc NombreModelo

# Después de generar, ejecutar migraciones
php artisan migrate

# Eliminar módulo completo (archivos + tablas BD)
php artisan delete:mvc NombreModelo
```

### Gestión de Menús

```bash
# Convertir menús de BD a seeders JSON
php artisan app:convert-menu

# Re-sembrar menús después de cambios
php artisan db:seed --class=MenuSeeder
```

### Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

## Patrones Específicos del Proyecto

### Estructura de Rutas

-   **Rutas Backend**: `routes/backend.php` con prefijo `admin` desde `config('master.app.url.backend')`
-   **Rutas MVC**: Auto-generadas en `routes/mvc-route.php` con prefijo `backend`
-   **Patrón de rutas**: `prefix('modelo')->as('modelo')->group()` con endpoints `data` y `delete/{id}`

### Integración DataTables

Usa **Yajra DataTables** con patrón AJAX personalizado:

```javascript
// Patrón fetch personalizado para DataTables dinámicos
fetch("{{ url('/js/'.$backend.'/'.$page->code.'/datatable.js') }}", {
    method: "POST",
    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
    body: JSON.stringify({ id: "{{ $id }}" }),
});
```

### Gestión de Archivos

-   **Archivos polimórficos**: Modelo `File` con relaciones `morphMany`
-   **Métodos Helper**: `Helper::uploadImageBase64()` para subidas de editor
-   **Streaming de archivos**: Rutas `/file/stream/{id}/{nombre}`

### Sistema de Direcciones Venezolano

Estructura jerárquica compleja de direcciones:

-   `StateAddress` → `MunicipalityAddress` → `CityAddress` → `PostalCodeAddress`
-   Método Helper: `Helper::getAddressById($id)` retorna cadena de dirección formateada

### Control de Acceso Basado en Roles

-   **Niveles**: Jerarquía `root`, `admin`, `user`
-   **Grupos de Acceso**: Definen permisos CRUD (`create`, `read`, `update`, `delete`)
-   **Acceso a Menús**: `AccessMenu` controla visibilidad de menú por grupo
-   **Permisos de usuario**: Accedidos vía `$user->create`, `$user->read`, etc.

### Componentes Frontend

-   **Componentes Blade**: `resources/views/components/` para elementos UI reutilizables
-   **Formularios AJAX**: `jquery-crud.blade.php` maneja todas las submisiones con validación
-   **SweetAlert2**: Integrado para notificaciones y confirmaciones
-   **Botones de acción**: Generados vía `Helper::generateActionButtons()`

## Archivos de Configuración

### Configuración Master (`config/master.php`)

-   **Perfil de app**: Configuraciones de nombre, versión, autor
-   **Rutas**: Mapeos de directorios de controlador, modelo, vista
-   **URLs**: Prefijos de URL backend/frontend
-   **Plantilla**: Plantilla UI y rutas de assets

### Configuración MVC (`config/mvc.php`)

-   **Rutas**: Directorios objetivo de auto-generación
-   **Elementos de entrada**: Plantillas de elementos de formulario HTML
-   **Tipos de columna**: Tipos de columna de migración Laravel disponibles

## Seguridad y Validación

### Validación del Modelo Usuario

-   **Reglas de contraseña complejas**: Regex + lista negra de contraseñas comunes
-   **Validación de nombres**: Previene palabras clave admin/user
-   **Validación de email**: Regex personalizado para formatos venezolanos
-   **Operaciones CRUD**: Validación incorporada con método `handleOperation()`

### Seguridad de Archivos

-   **Subida Base64**: Manejo seguro de imágenes en editores
-   **Streaming de archivos**: Acceso controlado vía rutas autenticadas
-   **Almacenamiento polimórfico**: Archivos vinculados a modelos específicos

## Patrones de Testing

-   **Tests de funcionalidad**: `tests/Feature/` para flujos completos
-   **Tests unitarios**: `tests/Unit/` para componentes individuales
-   **Testing de Helper**: `HelperTest.php` cubre funciones de utilidad

## Convenciones Clave

-   **Idioma español**: Todo el texto de cara al usuario en español
-   **Claves primarias UUID**: Modelo User usa UUIDs
-   **Soft deletes**: La mayoría de modelos soportan eliminación suave
-   **Arrays fillable**: Protección explícita de asignación masiva
-   **Nomenclatura de relaciones**: Seguir convenciones Laravel (`belongsTo`, `hasMany`)

Al trabajar con este codebase, siempre usa el generador MVC para nuevos módulos, sigue los patrones de rutas establecidos, y aprovecha las utilidades de la clase Helper para operaciones comunes.

---

## 🤖 Formato de Respuesta y Proceso del Agente

Este es el proceso paso a paso que se seguirá para entregar la solución completa.

### PASO 1: ANÁLISIS Y ESTRATEGIA

-   Resumen de la arquitectura propuesta.
-   Patrones de diseño aplicables.
-   Justificación de las tecnologías seleccionadas.
-   Casos límite identificados.

### PASO 2: ESTRUCTURA DE ARCHIVOS

-   Árbol de directorios sugerido.
-   Justificación de la organización.

### PASO 3: IMPLEMENTACIÓN

-   Código completo y funcional.
-   **Backend**: Modelos, controladores, rutas, servicios.
-   **Frontend**: Componentes, _hooks_, gestión de estado, _a11y_.

### PASO 4: REVISIÓN

-   Análisis de seguridad y escalabilidad (basado en las directivas).
-   Mínimo **3 mejoras opcionales** sugeridas.
-   Deuda técnica identificada.

### Instrucciones de Interacción

-   **Instrucción de Inicio:** Responder únicamente con: `"TERMINAL DE ARQUITECTO LISTA."`
-   **Instrucciones Paso a Paso:**
    1.  **Comprensión**: Hacer preguntas aclaratorias y esperar respuestas.
    2.  **Resumen**: Explicar el código, pasos, suposiciones y limitaciones.
    3.  **Código**: Presentar código fácil de copiar/pegar con explicación de razonamiento.
-   **Indicaciones Generales:** Tono positivo, lenguaje claro, mantener el contexto y enfoque exclusivo en el código.

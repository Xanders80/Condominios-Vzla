# API RESTful - Documentación Completa

## Resumen de Implementación

Se ha implementado una API RESTful completa para autenticación siguiendo las mejores prácticas de Laravel.

## Estructura de Archivos

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ApiResponseTrait.php              # Trait para respuestas JSON
│   │   └── Api/V1/Auth/
│   │       ├── AuthController.php            # Login, Logout, Registro, User
│   │       ├── PasswordController.php        # Restablecimiento de contraseña
│   │       ├── VerificationController.php    # Verificación de email
│   │       └── PasswordConfirmationController.php
│   ├── Requests/Api/V1/Auth/
│   │   ├── LoginRequest.php
│   │   ├── RegisterRequest.php
│   │   ├── PasswordResetRequest.php
│   │   ├── NewPasswordRequest.php
│   │   └── ResendVerificationRequest.php
│   └── Resources/V1/Auth/
│       └── UserResource.php
tests/
└── Feature/Api/V1/Auth/
    ├── AuthControllerTest.php
    └── PasswordControllerTest.php
```

## Endpoints API

### Autenticación

| Método | Endpoint | Descripción | Auth Requerida |
|--------|----------|-------------|----------------|
| POST | `/api/v1/auth/register` | Registro de usuarios | No |
| POST | `/api/v1/auth/login` | Inicio de sesión | No |
| POST | `/api/v1/auth/logout` | Cierre de sesión | Sí |
| GET | `/api/v1/auth/user` | Obtener usuario autenticado | Sí |
| POST | `/api/v1/auth/password/email` | Solicitar reset de contraseña | No |
| PUT | `/api/v1/auth/password/reset` | Resetear contraseña | No |
| GET | `/api/v1/auth/email/verify/{id}/{hash}` | Verificar email | Sí |
| POST | `/api/v1/auth/email/resend` | Reenviar verificación | Sí |
| POST | `/api/v1/auth/confirm-password` | Confirmar contraseña | Sí |

## Documentación Swagger

La documentación interactiva de la API está disponible en:

**URL:** `/api/documentation`

Esta interfaz permite:
- Ver todos los endpoints disponibles
- Probar los endpoints directamente
- Ver los modelos de datos
- Autenticar con Bearer tokens

## Instalación

### 1. Instalar Dependencias (ya instaladas)

```bash
composer require laravel/sanctum
ccomposer require darkaonline/l5-swagger
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

### 2. Configurar Sanctum

Asegúrate de que el modelo `User` tenga el trait `HasApiTokens`:

```php
use HasApiTokens;
```

### 3. Ejecutar Migraciones

```bash
php artisan migrate
```

### 4. Generar Documentación Swagger

```bash
php artisan l5-swagger:generate
```

## Tests

Los tests automatizados están ubicados en `tests/Feature/Api/V1/Auth/`.

### Ejecutar Tests

```bash
# Ejecutar todos los tests de autenticación
php artisan test --filter=AuthControllerTest

# Ejecutar tests de password
php artisan test --filter=PasswordControllerTest

# Ejecutar todos los tests
php artisan test
```

### Tests Incluidos

- **AuthControllerTest:**
  - Registro de usuario exitoso
  - Validación de datos de registro
  - Login con credenciales válidas
  - Login con email no verificado
  - Login con credenciales inválidas
  - Obtener usuario autenticado
  - Acceso no autorizado
  - Logout exitoso
  - Rate limiting en login

- **PasswordControllerTest:**
  - Solicitud de reset de contraseña
  - Reset de contraseña exitoso
  - Validación de tokens inválidos
  - Confirmación de contraseña requerida

## Respuestas API

### Formato de Respuesta Exitosa

```json
{
  "success": true,
  "message": "Operación exitosa",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      ...
    }
  }
}
```

### Formato de Respuesta de Error

```json
{
  "success": false,
  "message": "Mensaje de error",
  "errors": {
    "email": ["El email es obligatorio."]
  }
}
```

## Seguridad

- **Rate Limiting:** 5 intentos de login por minuto
- **Autenticación:** Sanctum tokens (Bearer)
- **Validación:** Form Requests con reglas estrictas
- **Protección CSRF:** En rutas web
- **Hashing de contraseñas:** Bcrypt

## Variables de Entorno

Agrega estas variables a tu archivo `.env`:

```env
L5_SWAGGER_GENERATE_ALWAYS=true  # Generar docs en cada request (dev)
L5_SWAGGER_CONST_HOST=http://localhost
```

## Próximos Pasos

1. ✅ Implementar en entorno de desarrollo
2. ✅ Crear tests automatizados
3. ✅ Documentar con Swagger/OpenAPI
4. 🔄 Configurar CI/CD para ejecución automática de tests
5. 🔄 Desplegar a producción

## Notas de Implementación

- Los controladores web existentes en `Backend/Auth/` se mantienen intactos
- Los nuevos controladores API están en `Api/V1/Auth/`
- Se utiliza Sanctum para autenticación stateless
- El trait `ApiResponseTrait` proporciona respuestas JSON estandarizadas
- Todos los endpoints tienen validación mediante Form Requests
- Se implementó rate limiting para prevenir ataques de fuerza bruta

---

**Documentación generada el:** 19 de Febrero de 2026
**Versión API:** v1
**Versión Laravel:** 12.x

# PRD Generator Prompt - Product Manager

## Contexto
Estás generando un Product Requirements Document (PRD) para una nueva funcionalidad en Condominios-Vzla.

## Estructura del PRD

```markdown
# PRD: [Nombre de la Funcionalidad]

## 1. Resumen Ejecutivo
- Descripción breve (2-3 oraciones)
- Problema que resuelve
- Valor para el usuario

## 2. Usuarios Afectados
- Tipo de usuario (Admin, Residente, Copropietario)
- Cuántos usuarios se benefician
- Frecuencia de uso esperada

## 3. Requisitos Funcionales
### 3.1 Must Have
- [ ] Requisito 1
- [ ] Requisito 2

### 3.2 Should Have
- [ ] Requisito 3

### 3.3 Could Have
- [ ] Requisito 4

## 4. User Stories
### US-001: [Título]
Como [tipo de usuario]
Quiero [acción]
Para [beneficio]

**Criterios de aceptación:**
- Dado que [contexto]
- Cuando [acción]
- Entonces [resultado]

## 5. Requisitos No Funcionales
- Performance: tiempo de respuesta esperado
- Seguridad: permisos requeridos
- Usabilidad: dispositivos soportados

## 6. Dependencias
- Módulos existentes que se ven afectados
- Datos requeridos de otros módulos

## 7. Métricas de Éxito
- Cómo mediremos si la funcionalidad es exitosa

## 8. Scope Excluido
- Qué NO incluye esta versión
```

## Consideraciones del Dominio
- Moneda dual: Bolívares y USD (tasa BCV)
- Coeficientes de copropiedad
- Marco legal venezolano de propiedad horizontal
- Notificaciones formales para sanciones

# Acceptance Criteria Prompt - Product Manager

## Contexto
Estás definiendo criterios de aceptación detallados para user stories en Condominios-Vzla.

## Framework: Given-When-Then

### Estructura
```
Escenario: [Descripción del escenario]
Dado que [contexto/precondición]
Y [contexto adicional si aplica]
Cuando [acción del usuario o sistema]
Y [acción adicional si aplica]
Entonces [resultado esperado]
Y [resultado adicional si aplica]
```

## Tipos de Criterios

### 1. Happy Path
El flujo principal y esperado sin errores.

### 2. Validación de Inputs
- Campos requeridos vacíos
- Formatos inválidos (email, teléfono, RIF)
- Valores fuera de rango (montos negativos, fechas pasadas)
- Duplicados (RIF existente, nombre duplicado)

### 3. Permisos y Autorización
- Usuario no autenticado
- Usuario sin rol apropiado
- Intento de acceso a datos de otro condominio

### 4. Edge Cases
- Listas vacías
- Primer registro del sistema
- Último registro eliminado
- Montos en cero
- Fechas límite

### 5. Error Handling
- Error de base de datos
- Timeout de servicio externo (tasa BCV)
- Archivo adjunto muy grande

## Ejemplo Completo

```
Feature: Gestión de Pagos

Escenario: Registrar pago exitoso
Dado que el residente "Juan Pérez" tiene una deuda de Bs. 500.00
Y estoy autenticado como administrador
Cuando registro un pago de Bs. 500.00 con fecha de hoy
Y selecciono "Transferencia" como método de pago
Entonces el saldo del residente queda en Bs. 0.00
Y se genera un recibo con número correlativo
Y se muestra mensaje de éxito

Escenario: Pago parcial
Dado que el residente tiene una deuda de Bs. 500.00
Cuando registro un pago de Bs. 200.00
Entonces el saldo pendiente queda en Bs. 300.00
Y el recibo indica "Pago Parcial"

Escenario: Pago con monto inválido
Dado que estoy en el formulario de registro de pago
Cuando intento registrar un pago de Bs. -100.00
Entonces se muestra error "El monto debe ser mayor a cero"
Y el formulario no se envía
```

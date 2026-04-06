# User Stories Prompt - Product Manager

## Contexto
Estás descomponiendo un PRD en user stories implementables para Condominios-Vzla.

## Formato de User Story

```
US-[NÚMERO]: [Título Corto]

Como [rol: administrador | residente | copropietario]
Quiero [acción específica]
Para [beneficio claro]

### Criterios de Aceptación
1. **Dado que** [estado inicial]
   **Cuando** [acción del usuario]
   **Entonces** [resultado esperado]

2. **Dado que** [estado alternativo]
   **Cuando** [acción diferente]
   **Entonces** [resultado alternativo]

### Estimación
- Complejidad: [Baja | Media | Alta]
- Dependencias: [US-XXX, módulo existente]
- Módulo afectado: [nombre del módulo]

### Notas Técnicas
- Tablas involucradas: [lista]
- Endpoints necesarios: [lista]
- Permisos requeridos: [lista]
```

## Reglas de Descomposición
1. Cada user story debe ser implementable en 1-3 días
2. Cada story debe ser testeable independientemente
3. Priorizar stories que desbloquean otros stories
4. Incluir al menos un happy path y un edge case por story
5. Asignar complejidad basada en: tablas nuevas, relaciones, validaciones, vistas

## Ejemplo para Condominios-Vzla

```
US-042: Registrar pago de cuota de condominio

Como administrador
Quiero registrar un pago realizado por un residente
Para mantener actualizado el estado de cuentas

### Criterios de Aceptación
1. **Dado que** un residente tiene una deuda pendiente
   **Cuando** registro el pago con monto, fecha y método
   **Entonces** el saldo se actualiza y se genera un recibo

2. **Dado que** el pago excede la deuda
   **Cuando** registro el pago
   **Entonces** el excedente se marca como crédito a favor

### Estimación
- Complejidad: Media
- Dependencias: US-040 (generar recibos), US-041 (ver deudas)
- Módulo afectado: payments
```

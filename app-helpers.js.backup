/**
 * Formatea el valor de un campo de entrada para que coincida con el formato de RIF venezolano (p. ej., J-12345678-9).
 * @param {Event} event - El evento de entrada del campo.
 */
function formatRifInput(event) {
    const input = event.target;
    // Elimina caracteres no válidos y convierte a mayúsculas.
    let value = input.value.toUpperCase().replace(/[^VJEPG0-9]/g, '');

    if (value.length === 0) {
        input.value = '';
        return;
    }

    // Valida la primera letra.
    const letter = value.substring(0, 1);
    if (!['V', 'J', 'E', 'P', 'G'].includes(letter)) {
        input.value = '';
        return;
    }

    // Limita la cantidad de dígitos a 9.
    let numbers = value.substring(1);
    if (numbers.length > 9) {
        numbers = numbers.substring(0, 9);
    }

    // Construye el RIF formateado.
    let formatted = letter;
    if (numbers.length > 0) {
        formatted += '-' + numbers.substring(0, 8);
    }
    if (numbers.length > 8) {
        formatted += '-' + numbers.substring(8);
    }

    input.value = formatted;
}

/**
 * Formatea el valor de un campo de entrada para que coincida con el formato de teléfono venezolano (p. ej., (0414) 123-4567).
 * @param {Event} event - El evento de entrada del campo.
 */
function formatPhoneInput(event) {
    const input = event.target;
    let value = input.value.replace(/\D/g, ''); // Eliminar caracteres no numéricos

    if (value.length > 11) {
        value = value.slice(0, 11);
    }

    if (value.length > 7) {
        value = `(${value.slice(0, 4)}) ${value.slice(4, 7)}-${value.slice(7, 11)}`;
    } else if (value.length > 4) {
        value = `(${value.slice(0, 4)}) ${value.slice(4, 7)}`;
    } else if (value.length > 0) {
        value = `(${value.slice(0, 4)}`;
    }

    input.value = value;
}

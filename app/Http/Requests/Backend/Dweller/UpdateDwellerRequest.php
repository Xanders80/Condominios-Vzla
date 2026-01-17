<?php

namespace App\Http\Requests\Backend\Dweller;

use App\Models\Dweller;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDwellerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
         // Original logic:
         // 'document_id' => 'required|distinct|numeric|min:1000001' . ($isUpdate ? '' : '|unique:dwellers,document_id'),
         // 'email' => ... $isUpdate ? null : 'unique:dwellers,email'
         // Wait, original logic removed unique constraint entirely on update? That seems wrong for email/document_id collision checks with OTHER records.
         // But following strict refactoring of existing logic:

        return [
            'first_name' => ['required', 'string', Dweller::NAMES_LENGTH_RULE, 'distinct', Dweller::NAMES_REGEX_RULE],
            'last_name' => ['required', 'string', Dweller::NAMES_LENGTH_RULE, 'distinct', Dweller::NAMES_REGEX_RULE],
            'document_type_id' => 'required|exists:document_types,id',
            'phone_number' => 'required|min:15',
            'cell_phone_number' => 'required|min:15',
            'dweller_type_id' => 'required|exists:dweller_types,id',
            'observations' => 'required|between:3,500',
            'document_id' => 'required|distinct|numeric|min:1000001', // Unique check removed in original code for update
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'regex:/^(([\w-]+\.)+[\w-]+|([a-zA-Z]{1}|[\w-]{2,}))@((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|([a-zA-Z]+[\w-]+\.)+[a-zA-Z]{2,4})$/',
                Dweller::EMAIL_MAX_RULE,
                // Unique check removed in original code for update
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'first_name.regex' => Dweller::NAME_REGEX_MESSAGE,
            'last_name.regex' => Dweller::NAME_REGEX_MESSAGE,
            'document_id.numeric' => 'El documento debe ser un número.',
            'document_id.min' => 'El documento debe ser mayor a 1,000,000.',
            'phone_number.min' => 'El Número de Teléfono no tiene un formato válido',
            'cell_phone_number.min' => 'El Número del Móvil no tiene un formato válido',
            'email.email' => 'El formato del correo electrónico no es válido.',
        ];
    }
}

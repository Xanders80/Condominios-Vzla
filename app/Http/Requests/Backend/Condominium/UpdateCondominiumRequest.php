<?php

namespace App\Http\Requests\Backend\Condominium;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCondominiumRequest extends FormRequest
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
        $id = $this->route('condominium') ? $this->route('condominium') : $this->input('id');

        return [
            'name' => 'required|string|distinct|between:3,50', // Unique check removed for update on same ID logic handled by DB usually, or ignore. Original logic removed unique check completely on update.
            'name_incharge' => 'required|string|min:3|max:100',
            'jobs_incharge' => 'required|string|min:3|max:100',
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:100',
                'regex:/^(([\w-]+\.)+[\w-]+|([a-zA-Z]{1}|[\w-]{2,}))@((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|([a-zA-Z]+[\w-]+\.)+[a-zA-Z]{2,4})$/',
                "unique:condominiums,email,$id"
            ],
            'rif' => "required|string|min:12|unique:condominiums,rif,$id",
            'phone' => 'required|string|regex:/^\(\d{3,4}\) \d{3}-\d{4}$/',
            'address_line' => 'required|string|between:3,255',
            'postal_code_address' => 'required|string|max:20',
            'reserve_found' => 'required|numeric|min:0',
            'rate_percentage' => 'required|numeric|between:0,100',
            'billing_date' => 'required|numeric|min:1|max:31',
            'observations' => 'nullable|string|max:500',
            'logo' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'rif.min' => 'El Número de Rif no tiene un formato válido',
            'phone.regex' => 'El Número de Teléfono no tiene un formato válido',
        ];
    }
}

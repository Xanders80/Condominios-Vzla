<?php

namespace App\Http\Requests\Backend\DwellerType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDwellerTypeRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('dweller_type');
        return [
            'name' => 'required|distinct|unique:dweller_types,name,' . $id,
        ];
    }
}

<?php

namespace App\Http\Requests\Backend\UnitType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitTypeRequest extends FormRequest
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
        $id = $this->route('unit_type');
        return [
            'name' => 'required|distinct|unique:unit_types,name,' . $id,
        ];
    }
}

<?php

namespace App\Http\Requests\Backend\Unit;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
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
        // Unit model used the same validation for store and update?
        // Yes, validationRules(array $data) didn't take $id argument in Unit.php.
        return [
            'name' => 'required|string|between:3,50',
            'unit_type_id' => 'required|exists:unit_types,id',
            'dweller_id' => 'required|exists:dwellers,id',
            'tower_sector_id' => 'required|exists:tower_sectors,id',
            'floor_street_id' => 'required|exists:floor_streets,id',
        ];
    }
}

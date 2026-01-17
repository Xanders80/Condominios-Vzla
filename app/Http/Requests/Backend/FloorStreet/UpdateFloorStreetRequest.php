<?php

namespace App\Http\Requests\Backend\FloorStreet;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFloorStreetRequest extends FormRequest
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
        $id = $this->route('floor_street');
        return [
            'name' => 'required|distinct|unique:floor_streets,name,' . $id,
            'tower_sector_id' => 'required|exists:tower_sectors,id',
        ];
    }
}

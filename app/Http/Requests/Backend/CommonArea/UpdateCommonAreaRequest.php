<?php

namespace App\Http\Requests\Backend\CommonArea;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommonAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer|min:0',
            'booking_fee' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:active,under_maintenance,closed',
        ];
    }
}

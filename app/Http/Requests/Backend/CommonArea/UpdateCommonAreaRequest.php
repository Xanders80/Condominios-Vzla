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
            'description' => 'nullable|string',
            'max_occupancy' => 'nullable|integer|min:0',
            'booking_fee' => 'sometimes|numeric|min:0',
            'pricing_type' => 'sometimes|in:fixed,hourly',
            'currency' => 'sometimes|in:USD,BS',
            'min_anticipation_hours' => 'nullable|integer|min:0',
            'max_booking_hours' => 'nullable|integer|min:0',
            'cancellation_penalty_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ];
    }
}

<?php

namespace App\Http\Requests\Backend\CommonArea;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommonAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'condominiums_id' => 'required|exists:condominiums,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_occupancy' => 'nullable|integer|min:0',
            'booking_fee' => 'required|numeric|min:0',
            'pricing_type' => 'required|in:fixed,hourly',
            'currency' => 'required|in:USD,BS',
            'min_anticipation_hours' => 'nullable|integer|min:0',
            'max_booking_hours' => 'nullable|integer|min:0',
            'cancellation_penalty_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ];
    }
}

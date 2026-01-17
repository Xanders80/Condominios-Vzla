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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:0',
            'is_bookable' => 'boolean',
            'booking_fee' => 'required|numeric|min:0',
            'status' => 'required|in:active,under_maintenance,closed',
        ];
    }
}

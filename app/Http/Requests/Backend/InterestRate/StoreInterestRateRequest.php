<?php

namespace App\Http\Requests\Backend\InterestRate;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterestRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'percentage' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ];
    }
}

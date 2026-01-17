<?php

namespace App\Http\Requests\Backend\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'tax_id' => 'sometimes|string|unique:suppliers,tax_id,' . $this->route('id'),
            'email' => 'nullable|email|max:255',
            'status' => 'sometimes|in:active,inactive',
        ];
    }
}

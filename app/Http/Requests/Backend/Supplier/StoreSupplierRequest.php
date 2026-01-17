<?php

namespace App\Http\Requests\Backend\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'tax_id' => 'required|string|unique:suppliers,tax_id',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'service_category' => 'required|string',
            'status' => 'required|in:active,inactive',
        ];
    }
}

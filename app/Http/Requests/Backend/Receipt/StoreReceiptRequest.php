<?php

namespace App\Http\Requests\Backend\Receipt;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unit_id' => 'required|exists:units,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'amount_bs' => 'required|numeric|min:0',
            'amount_usd' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid,partial,cancelled',
            'due_date' => 'required|date',
        ];
    }
}

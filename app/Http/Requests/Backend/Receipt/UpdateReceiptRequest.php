<?php

namespace App\Http\Requests\Backend\Receipt;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unit_id' => 'sometimes|exists:units,id',
            'month' => 'sometimes|integer|min:1|max:12',
            'year' => 'sometimes|integer|min:2000',
            'amount_bs' => 'sometimes|numeric|min:0',
            'amount_usd' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:pending,paid,partial,cancelled',
            'due_date' => 'sometimes|date',
        ];
    }
}

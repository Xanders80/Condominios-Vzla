<?php

namespace App\Http\Requests\Backend\Debt;

use Illuminate\Foundation\Http\FormRequest;

class StoreDebtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unit_id' => 'required|exists:units,id',
            'receipt_id' => 'nullable|exists:receipts,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:current,pre_delinquent,delinquent,paid,judicial',
            'due_date' => 'required|date',
            'grace_period_days' => 'required|integer|min:0',
        ];
    }
}

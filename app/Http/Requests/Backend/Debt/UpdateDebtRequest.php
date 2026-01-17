<?php

namespace App\Http\Requests\Backend\Debt;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDebtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:current,pre_delinquent,delinquent,paid,judicial',
            'due_date' => 'sometimes|date',
            'grace_period_days' => 'sometimes|integer|min:0',
        ];
    }
}

<?php

namespace App\Http\Requests\Backend\WorkOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'actual_cost' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:draft,assigned,in_progress,on_hold,completed,cancelled',
            'completion_date' => 'nullable|date',
        ];
    }
}

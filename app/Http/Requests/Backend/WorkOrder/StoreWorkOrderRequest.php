<?php

namespace App\Http\Requests\Backend\WorkOrder;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'incident_report_id' => 'nullable|exists:incident_reports,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_cost' => 'required|numeric|min:0',
            'status' => 'required|in:draft,assigned,in_progress,on_hold,completed,cancelled',
            'scheduled_date' => 'nullable|date',
        ];
    }
}

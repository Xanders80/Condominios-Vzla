<?php

namespace App\Http\Requests\Backend\IncidentReport;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'priority' => 'sometimes|in:low,medium,high,critical',
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
        ];
    }
}

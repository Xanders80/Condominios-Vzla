<?php

namespace App\Http\Requests\Backend\Assembly;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssemblyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:scheduled,in_progress,completed,cancelled',
            'minutes_header' => 'nullable|string',
            'minutes_footer' => 'nullable|string',
        ];
    }
}

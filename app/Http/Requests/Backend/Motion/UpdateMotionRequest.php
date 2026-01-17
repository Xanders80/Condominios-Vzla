<?php

namespace App\Http\Requests\Backend\Motion;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:proposed,open,closed,approved,rejected',
            'result_summary' => 'nullable|string',
        ];
    }
}

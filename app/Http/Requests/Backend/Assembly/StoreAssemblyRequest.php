<?php

namespace App\Http\Requests\Backend\Assembly;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssemblyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_type' => 'required|in:ordinary,extraordinary',
            'session_date' => 'required|date',
            'location' => 'required|string|max:255',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'quorum_percentage' => 'required|numeric|min:0|max:100',
        ];
    }
}

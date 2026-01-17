<?php

namespace App\Http\Requests\Backend\Motion;

use Illuminate\Foundation\Http\FormRequest;

class StoreMotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assembly_session_id' => 'required|exists:assembly_sessions,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'voting_type' => 'required|in:public,secret',
            'status' => 'required|in:proposed,open,closed,approved,rejected',
        ];
    }
}

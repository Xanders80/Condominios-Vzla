<?php

namespace App\Http\Requests\Backend\Faq;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'file' => ['nullable', 'file'],
            'visitors' => ['required', 'integer'],
            'like' => ['required', 'integer'],
            'dislike' => ['required', 'integer'],
            'publish' => ['nullable'],
            'menu_id' => ['required', 'string'],
        ];
    }
}

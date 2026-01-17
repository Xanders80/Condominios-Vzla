<?php

namespace App\Http\Requests\Backend\Banks;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankRequest extends FormRequest
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
            'code_sudebank' => 'required|max:4|unique:banks,code_sudebank',
            'name_ibp' => 'required|between:3,150',
            'rif' => 'required|min:12',
            'website' => 'required|url',
        ];
    }
}

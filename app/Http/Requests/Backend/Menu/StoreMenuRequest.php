<?php

namespace App\Http\Requests\Backend\Menu;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
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
            'parent_id' => 'nullable',
            'title' => 'required|unique:menus',
            'subtitle' => 'nullable',
            'code' => 'required|unique:menus',
            'url' => 'required|unique:menus',
            'model' => 'nullable',
            'icon' => 'required',
            'type' => 'required',
            'show' => 'nullable',
            'active' => 'nullable',
            'access_group_id' => 'required|array|exists:access_groups,id',
        ];
    }
}

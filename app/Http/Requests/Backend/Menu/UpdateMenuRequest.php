<?php

namespace App\Http\Requests\Backend\Menu;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
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
        $id = $this->route('menu');
        return [
            'parent_id' => 'nullable',
            'title' => 'required',
            'subtitle' => 'nullable',
            'code' => 'required|unique:menus,code,' . $id,
            'url' => 'required|unique:menus,url,' . $id,
            'model' => 'nullable',
            'icon' => 'required',
            'type' => 'required',
            'show' => 'nullable',
            'active' => 'nullable',
        ];
    }
}

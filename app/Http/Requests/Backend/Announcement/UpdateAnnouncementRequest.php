<?php

namespace App\Http\Requests\Backend\Announcement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncementRequest extends FormRequest
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
            'menu_id' => 'required|exists:menus,id',
            'title' => 'required|regex:/^[a-zA-Z0-9\s\-\.\,\(\)\'\’\“\”\/]+$/',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'content' => 'required',
            'urgency' => 'required',
            'publish' => 'nullable',
            'parent_id' => 'nullable',
            'file.*' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:2048',
        ];
    }
}

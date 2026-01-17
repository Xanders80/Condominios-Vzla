<?php

namespace App\Http\Requests\Backend\BanksCondominium;

use Illuminate\Foundation\Http\FormRequest;

class StoreBanksCondominiumRequest extends FormRequest
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
            'account_number' => 'required|min:23',
            'condominiums_id' => 'required|exists:condominiums,id',
            'banks_id' => 'required|exists:banks,id',
        ];
    }
}

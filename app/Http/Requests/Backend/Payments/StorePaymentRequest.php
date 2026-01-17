<?php

namespace App\Http\Requests\Backend\Payments;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'dweller_id' => 'required|exists:dwellers,id',
            'nro_confirmation' => 'required|unique:payments,nro_confirmation',
            'amount' => 'required',
            'banks_id' => 'required|exists:banks,id',
            'condominiums_id' => 'required|exists:condominiums,id',
            'ways_to_pays_id' => 'required|exists:ways_to_pays,id',
            'date_pay' => 'required',
            'date_confirm' => 'required',
            'observations' => 'required|between:3,500',
        ];
    }
}

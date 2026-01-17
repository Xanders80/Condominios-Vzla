<?php

namespace App\Http\Requests\Backend\User;

use App\Models\User;
use App\Support\Helper;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $commonPasswords = Helper::getCommonPasswords();
        $notInList = implode(',', array_map('ucfirst', array_map('strtolower', $commonPasswords)));

        // La contraseña es obligatoria al crear un usuario (store)
        $isRequiredPassword = 'required';

        return [
            'first_name' => [
                'required',
                'string',
                User::NAMES_LENGTH_RULE,
                'distinct',
                User::NAMES_REGEX_RULE,
            ],
            'last_name' => [
                'required',
                'string',
                User::NAMES_LENGTH_RULE,
                'distinct',
                User::NAMES_REGEX_RULE,
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'regex:/^(([\w-]+\.)+[\w-]+|([a-zA-Z]{1}|[\w-]{2,}))@((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|([a-zA-Z]+[\w-]+\.)+[a-zA-Z]{2,4})$/',
                User::EMAIL_MAX_RULE,
                'unique:' . User::class,
            ],
            'password' => [
                $isRequiredPassword,
                'confirmed',
                User::PASSW_REGEX_RULE,
                User::PASSW_NOTIN_RULE . $notInList,
            ],
            'password_confirmation' => [
                $isRequiredPassword,
                'same:password',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'first_name.regex' => User::FISTN_MESS_REGEX,
            'last_name.regex' => User::FISTN_MESS_REGEX,
            'password.not_in' => User::PASSW_MESS_NOTIN,
            'password.regex' => User::PASSW_MESS_REGEX,
            'password_confirmation.same' => User::PASSC_MESS_SAME,
            'email.regex' => ' Formatos válidos. Ej: correo@dominio.com ó correo@192.168.1.1',
        ];
    }
}

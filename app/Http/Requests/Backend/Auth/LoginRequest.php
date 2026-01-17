<?php

namespace App\Http\Requests\Backend\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
        // Cargar y formatear la lista de contraseñas comunes
        $commonPasswords = config('common_passwords.common_passwords');
        $notInList = implode(',', array_map('ucfirst', array_map('strtolower', $commonPasswords)));

        return [
            'email' => [
                'required',
                'string',
                'email',
                'regex:/^(([\w-]+\.)+[\w-]+|([a-zA-Z]{1}|[\w-]{2,}))@((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\.([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|([a-zA-Z]+[\w-]+\.)+[a-zA-Z]{2,4})$/',
                User::EMAIL_MAX_RULE,
                'exists:users,email',
                'lowercase',
            ],
            'password' => [
                'required',
                User::PASSW_REGEX_RULE,
                User::PASSW_NOTIN_RULE . $notInList,
            ],
            'remember' => 'nullable|string|in:true,false',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'password.not_in' => User::PASSW_MESS_NOTIN,
            'password.regex' => User::PASSW_MESS_REGEX,
            'email.regex' => ' Formatos válidos. Ej: correo@dominio.com ó correo@192.168.1.1',
        ];
    }
}
